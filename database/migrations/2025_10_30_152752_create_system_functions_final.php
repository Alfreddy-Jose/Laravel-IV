<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSystemFunctionsFinal extends Migration  // ← NOMBRE ÚNICO
{
    public function up()
    {
        $this->createFunctions();
        $this->createTriggers();
    }

    public function down()
    {
        $this->dropTriggers();
        $this->dropFunctions();
    }

    private function createFunctions()
    {
        // 1. FUNCTION: check_clase_uniqueness
        DB::statement('
            CREATE OR REPLACE FUNCTION public.check_clase_uniqueness()
            RETURNS trigger
            LANGUAGE plpgsql
            AS $function$
            DECLARE
                v_lapso_id BIGINT;
                v_conflict_exists BOOLEAN;
            BEGIN
                -- Obtener el lapso_id de la sección asociada al horario
                SELECT s.lapso_id INTO v_lapso_id
                FROM seccions s
                INNER JOIN horarios h ON h.seccion_id = s.id
                WHERE h.id = NEW.horario_id;

                -- Verificar si existe conflicto con misma combinación en el mismo trimestre y lapso
                SELECT EXISTS (
                    SELECT 1 
                    FROM clases c
                    INNER JOIN horarios h ON c.horario_id = h.id
                    INNER JOIN seccions s ON h.seccion_id = s.id
                    WHERE c.docente_id = NEW.docente_id
                      AND c.espacio_id = NEW.espacio_id
                      AND c.dia = NEW.dia
                      AND c.bloque_id = NEW.bloque_id
                      AND c.trimestre_id = NEW.trimestre_id
                      AND s.lapso_id = v_lapso_id
                      AND c.id != COALESCE(NEW.id, 0)
                ) INTO v_conflict_exists;

                IF v_conflict_exists THEN
                    RAISE EXCEPTION \'Ya existe una clase con el mismo docente, espacio, día y bloque en el mismo trimestre y lapso académico\';
                END IF;

                RETURN NEW;
            END;
            $function$
        ');

        // [TODAS LAS OTRAS FUNCIONES IGUAL...]
        // 2. FUNCTION: crear_horario_automatico
        DB::statement('
            CREATE OR REPLACE FUNCTION public.crear_horario_automatico(p_nuevo_horario_id bigint, p_seccion_id bigint, p_nuevo_trimestre_id bigint)
            RETURNS jsonb
            LANGUAGE plpgsql
            AS $function$
            DECLARE
                v_horario_anterior_id BIGINT;
                v_lapso_academico TEXT;
                v_sede_id BIGINT;
                v_pnf_id BIGINT;
                v_trayecto_id BIGINT;
                v_clases_anteriores_count INTEGER;
                v_clases_copiadas INTEGER := 0;
                v_clases_eliminadas INTEGER := 0;
                v_advertencias TEXT[] := ARRAY[]::TEXT[];
                v_nombre_horario_anterior TEXT;
                v_nombre_horario_nuevo TEXT;
                v_nombre_seccion TEXT;
                v_nombre_trimestre_anterior TEXT;
                v_nombre_trimestre_nuevo TEXT;
            BEGIN
                -- 1. ENCONTRAR HORARIO ANTERIOR Y DATOS
                SELECT h.id, h.lapso_academico, h.nombre
                INTO v_horario_anterior_id, v_lapso_academico, v_nombre_horario_anterior
                FROM horarios h
                WHERE h.seccion_id = p_seccion_id 
                  AND h.trimestre_id < p_nuevo_trimestre_id
                ORDER BY h.trimestre_id DESC 
                LIMIT 1;

                -- OBTENER DATOS DE LA SECCIÓN
                SELECT s.sede_id, s.pnf_id, s.trayecto_id, s.nombre
                INTO v_sede_id, v_pnf_id, v_trayecto_id, v_nombre_seccion
                FROM seccions s 
                WHERE s.id = p_seccion_id;

                -- OBTENER NOMBRES DE TRIMESTRES
                SELECT nombre INTO v_nombre_trimestre_anterior
                FROM trimestres WHERE id = (SELECT trimestre_id FROM horarios WHERE id = v_horario_anterior_id);
                
                SELECT nombre INTO v_nombre_trimestre_nuevo
                FROM trimestres WHERE id = p_nuevo_trimestre_id;

                -- OBTENER NOMBRE DEL NUEVO HORARIO
                SELECT nombre INTO v_nombre_horario_nuevo
                FROM horarios WHERE id = p_nuevo_horario_id;

                IF v_horario_anterior_id IS NULL THEN
                    RETURN jsonb_build_object(
                        \'success\', false,
                        \'error\', \'No se encontró horario anterior para esta sección\',
                        \'clases_creadas\', 0,
                        \'advertencias\', v_advertencias
                    );
                END IF;

                -- VERIFICAR QUE EL HORARIO NUEVO ESTÉ VACÍO
                IF EXISTS (SELECT 1 FROM clases WHERE horario_id = p_nuevo_horario_id) THEN
                    RETURN jsonb_build_object(
                        \'success\', false,
                        \'error\', \'El horario nuevo ya tiene clases. Debe estar vacío para la creación automática.\',
                        \'clases_creadas\', 0,
                        \'advertencias\', v_advertencias
                    );
                END IF;

                -- 2. CONTAR CLASES EN HORARIO ANTERIOR
                SELECT COUNT(*) INTO v_clases_anteriores_count
                FROM clases 
                WHERE horario_id = v_horario_anterior_id;

                -- 3. COPIAR EXACTAMENTE TODAS LAS CLASES DEL HORARIO ANTERIOR
                DECLARE
                    v_clase_record RECORD;
                    v_error_text TEXT;
                BEGIN
                    FOR v_clase_record IN (
                        SELECT 
                            c.sede_id, c.pnf_id, c.trayecto_id,
                            c.unidad_curricular_id, c.docente_id, c.espacio_id,
                            c.bloque_id, c.dia, c.duracion
                        FROM clases c
                        WHERE c.horario_id = v_horario_anterior_id
                    ) LOOP
                        BEGIN
                            INSERT INTO clases (
                                sede_id, pnf_id, trayecto_id, trimestre_id, 
                                unidad_curricular_id, docente_id, espacio_id, 
                                bloque_id, horario_id, dia, duracion, created_at, updated_at
                            ) VALUES (
                                v_clase_record.sede_id,
                                v_clase_record.pnf_id,
                                v_clase_record.trayecto_id,
                                p_nuevo_trimestre_id,
                                v_clase_record.unidad_curricular_id,
                                v_clase_record.docente_id,
                                v_clase_record.espacio_id,
                                v_clase_record.bloque_id,
                                p_nuevo_horario_id,
                                v_clase_record.dia,
                                v_clase_record.duracion,
                                NOW(),
                                NOW()
                            );
                            
                            v_clases_copiadas := v_clases_copiadas + 1;
                            
                        EXCEPTION 
                            WHEN OTHERS THEN
                                v_error_text := \'Error copiando clase: \' || SQLERRM;
                                v_advertencias := array_append(v_advertencias, v_error_text);
                        END;
                    END LOOP;
                END;

                -- 4. ELIMINAR CLASES QUE NO PERTENECEN AL NUEVO TRIMESTRE
                DELETE FROM clases 
                WHERE horario_id = p_nuevo_horario_id
                  AND unidad_curricular_id NOT IN (
                      SELECT tuc.unidad_curricular_id
                      FROM trimestre_unidad_curricular tuc
                      WHERE tuc.trimestre_id = p_nuevo_trimestre_id
                  );

                GET DIAGNOSTICS v_clases_eliminadas = ROW_COUNT;

                -- 5. CONSTRUIR RESULTADO CON REPORTE MEJORADO
                RETURN jsonb_build_object(
                    \'success\', true,
                    \'clases_creadas\', v_clases_copiadas - v_clases_eliminadas,
                    \'clases_copiadas\', v_clases_copiadas,
                    \'clases_eliminadas\', v_clases_eliminadas,
                    \'clases_nuevas_uc\', 0,
                    \'conflictos_resueltos\', 0,
                    \'advertencias\', v_advertencias,
                    \'horario_anterior_utilizado\', v_horario_anterior_id,
                    \'mensaje\', \'Horario creado automáticamente para la sección \' || v_nombre_seccion ||
                              \' - Copiado desde: \' || COALESCE(v_nombre_horario_anterior, \'Horario anterior\') || \' (\' || v_nombre_trimestre_anterior || \')\' ||
                              \' - Hacia: \' || COALESCE(v_nombre_horario_nuevo, \'Nuevo horario\') || \' (\' || v_nombre_trimestre_nuevo || \')\',
                    \'reporte_detallado\', jsonb_build_object(
                        \'clases_en_horario_anterior\', v_clases_anteriores_count,
                        \'clases_copiadas_exitosamente\', v_clases_copiadas,
                        \'clases_eliminadas_sin_uc\', v_clases_eliminadas,
                        \'clases_finales_en_nuevo_horario\', v_clases_copiadas - v_clases_eliminadas,
                        \'seccion\', v_nombre_seccion,
                        \'trimestre_anterior\', v_nombre_trimestre_anterior,
                        \'trimestre_nuevo\', v_nombre_trimestre_nuevo
                    )
                );

            EXCEPTION
                WHEN OTHERS THEN
                    v_advertencias := array_append(v_advertencias, \'Error inesperado: \' || SQLERRM);
                    RETURN jsonb_build_object(
                        \'success\', false,
                        \'error\', \'Error durante la copia: \' || SQLERRM,
                        \'clases_creadas\', 0,
                        \'clases_eliminadas\', 0,
                        \'clases_nuevas_uc\', 0,
                        \'conflictos_resueltos\', 0,
                        \'advertencias\', v_advertencias
                    );
            END;
            $function$
        ');

        // 3. FUNCTION: encontrar_bloque_alternativo
        DB::statement('
            CREATE OR REPLACE FUNCTION public.encontrar_bloque_alternativo(p_espacio_id bigint, p_docente_id bigint, p_dia character varying, p_trimestre_id bigint, p_lapso_academico text)
            RETURNS bigint
            LANGUAGE plpgsql
            AS $function$
            BEGIN
                RETURN (
                    SELECT bt.id
                    FROM bloques_turnos bt
                    WHERE bt.id NOT IN (
                        SELECT c.bloque_id
                        FROM clases c
                        JOIN horarios h ON c.horario_id = h.id
                        WHERE h.lapso_academico = p_lapso_academico
                          AND c.trimestre_id = p_trimestre_id
                          AND c.dia = p_dia
                          AND (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                    )
                    LIMIT 1
                );
            END;
            $function$
        ');

        // 4. FUNCTION: encontrar_dia_alternativo
        DB::statement('
            CREATE OR REPLACE FUNCTION public.encontrar_dia_alternativo(p_espacio_id bigint, p_docente_id bigint, p_bloque_id bigint, p_trimestre_id bigint, p_lapso_academico text)
            RETURNS character varying
            LANGUAGE plpgsql
            AS $function$
            BEGIN
                RETURN (
                    SELECT dia
                    FROM (VALUES 
                        (\'LUNES\'), (\'MARTES\'), (\'MIERCOLES\'), 
                        (\'JUEVES\'), (\'VIERNES\')
                    ) AS dias(dia)
                    WHERE NOT EXISTS (
                        SELECT 1 FROM clases c
                        JOIN horarios h ON c.horario_id = h.id
                        WHERE h.lapso_academico = p_lapso_academico
                          AND c.trimestre_id = p_trimestre_id
                          AND c.dia = dias.dia
                          AND c.bloque_id = p_bloque_id
                          AND (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                    )
                    LIMIT 1
                );
            END;
            $function$
        ');

        // 5. FUNCTION: encontrar_docente_alternativo
        DB::statement('
            CREATE OR REPLACE FUNCTION public.encontrar_docente_alternativo(p_unidad_curricular_id bigint, p_dia character varying, p_bloque_id bigint, p_trimestre_id bigint, p_lapso_academico text)
            RETURNS bigint
            LANGUAGE plpgsql
            AS $function$
            BEGIN
                RETURN (
                    SELECT du.docente_id
                    FROM docente_unidad_curricular du
                    WHERE du.unidad_curricular_id = p_unidad_curricular_id
                      AND du.docente_id NOT IN (
                          SELECT c.docente_id
                          FROM clases c
                          JOIN horarios h ON c.horario_id = h.id
                          WHERE h.lapso_academico = p_lapso_academico
                            AND c.trimestre_id = p_trimestre_id
                            AND c.dia = p_dia
                            AND c.bloque_id = p_bloque_id
                      )
                      AND du.docente_id IN (
                          SELECT d.id
                          FROM docentes d
                          WHERE d.horas_dedicacion > (
                              SELECT COALESCE(SUM(c.duracion), 0)
                              FROM clases c
                              JOIN horarios h ON c.horario_id = h.id
                              WHERE c.docente_id = d.id
                                AND h.lapso_academico = p_lapso_academico
                                AND c.trimestre_id = p_trimestre_id
                          )
                      )
                    ORDER BY (
                        SELECT COUNT(*) 
                        FROM clases c2 
                        WHERE c2.docente_id = du.docente_id 
                        AND c2.unidad_curricular_id = p_unidad_curricular_id
                    ) DESC
                    LIMIT 1
                );
            END;
            $function$
        ');

        // 6. FUNCTION: encontrar_espacio_alternativo
        DB::statement('
            CREATE OR REPLACE FUNCTION public.encontrar_espacio_alternativo(p_sede_id bigint, p_dia character varying, p_bloque_id bigint, p_trimestre_id bigint, p_lapso_academico text)
            RETURNS bigint
            LANGUAGE plpgsql
            AS $function$
            BEGIN
                RETURN (
                    SELECT e.id
                    FROM espacios e
                    WHERE e.sede_id = p_sede_id
                      AND e.id NOT IN (
                          SELECT c.espacio_id
                          FROM clases c
                          JOIN horarios h ON c.horario_id = h.id
                          WHERE h.lapso_academico = p_lapso_academico
                            AND c.trimestre_id = p_trimestre_id
                            AND c.dia = p_dia
                            AND c.bloque_id = p_bloque_id
                      )
                    LIMIT 1
                );
            END;
            $function$
        ');

        // 7. FUNCTION: encontrar_mejor_docente_uc
        DB::statement('
            CREATE OR REPLACE FUNCTION public.encontrar_mejor_docente_uc(p_unidad_curricular_id bigint, p_trimestre_id bigint, p_lapso_academico text)
            RETURNS bigint
            LANGUAGE plpgsql
            AS $function$
            BEGIN
                RETURN (
                    SELECT du.docente_id
                    FROM docente_unidad_curricular du
                    WHERE du.unidad_curricular_id = p_unidad_curricular_id
                      AND du.docente_id IN (
                          SELECT d.id
                          FROM docentes d
                          WHERE d.horas_dedicacion > (
                              SELECT COALESCE(SUM(c.duracion), 0)
                              FROM clases c
                              JOIN horarios h ON c.horario_id = h.id
                              WHERE c.docente_id = d.id
                                AND h.lapso_academico = p_lapso_academico
                                AND c.trimestre_id = p_trimestre_id
                          )
                      )
                    ORDER BY (
                        SELECT COUNT(*) 
                        FROM clases c 
                        WHERE c.docente_id = du.docente_id 
                        AND c.unidad_curricular_id = p_unidad_curricular_id
                    ) DESC,
                    (
                        SELECT COALESCE(SUM(c.duracion), 0)
                        FROM clases c
                        JOIN horarios h ON c.horario_id = h.id
                        WHERE c.docente_id = du.docente_id
                          AND h.lapso_academico = p_lapso_academico
                          AND c.trimestre_id = p_trimestre_id
                    ) ASC
                    LIMIT 1
                );
            END;
            $function$
        ');

        // 8. FUNCTION: existe_conflicto_clase
        DB::statement('
            CREATE OR REPLACE FUNCTION public.existe_conflicto_clase(p_espacio_id bigint, p_docente_id bigint, p_dia character varying, p_bloque_id bigint, p_trimestre_id bigint, p_lapso_academico text, p_excluir_clase_id bigint DEFAULT NULL::bigint)
            RETURNS boolean
            LANGUAGE plpgsql
            AS $function$
            BEGIN
                RETURN EXISTS (
                    SELECT 1 FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE h.lapso_academico = p_lapso_academico
                      AND c.trimestre_id = p_trimestre_id
                      AND c.dia = p_dia
                      AND c.bloque_id = p_bloque_id
                      AND (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                      AND (p_excluir_clase_id IS NULL OR c.id != p_excluir_clase_id)
                );
            END;
            $function$
        ');

        // 9. FUNCTION: existe_conflicto_unico
        DB::statement('
            CREATE OR REPLACE FUNCTION public.existe_conflicto_unico(p_docente_id bigint, p_espacio_id bigint, p_dia character varying, p_bloque_id bigint, p_trimestre_id bigint, p_lapso_academico text)
            RETURNS boolean
            LANGUAGE plpgsql
            AS $function$
            BEGIN
                RETURN EXISTS (
                    SELECT 1 FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE h.lapso_academico = p_lapso_academico
                      AND c.trimestre_id = p_trimestre_id
                      AND c.docente_id = p_docente_id
                      AND c.espacio_id = p_espacio_id
                      AND c.dia = p_dia
                      AND c.bloque_id = p_bloque_id
                );
            END;
            $function$
        ');

        // 10. FUNCTION: resolver_conflicto_clase
        DB::statement('
            CREATE OR REPLACE FUNCTION public.resolver_conflicto_clase(p_clase_record record, p_trimestre_id bigint, p_horario_id bigint, p_lapso_academico text, p_sede_id bigint, p_pnf_id bigint, p_trayecto_id bigint, INOUT p_advertencias text[])
            RETURNS text[]
            LANGUAGE plpgsql
            AS $function$
            DECLARE
                v_docente_alt BIGINT;
                v_espacio_alt BIGINT;
                v_bloque_alt BIGINT;
                v_dia_alt VARCHAR(10);
                v_encontrado BOOLEAN := false;
            BEGIN
                FOR v_docente_alt IN (
                    SELECT du.docente_id
                    FROM docente_unidad_curricular du
                    WHERE du.unidad_curricular_id = p_clase_record.unidad_curricular_id
                      AND du.docente_id != p_clase_record.docente_id
                      AND NOT EXISTS (
                        SELECT 1 FROM clases c
                        JOIN horarios h ON c.horario_id = h.id
                        WHERE h.lapso_academico = p_lapso_academico
                          AND c.trimestre_id = p_trimestre_id
                          AND c.docente_id = du.docente_id
                          AND c.espacio_id = p_clase_record.espacio_id
                          AND c.dia = p_clase_record.dia
                          AND c.bloque_id = p_clase_record.bloque_id
                      )
                    LIMIT 1
                ) LOOP
                    BEGIN
                        INSERT INTO clases (
                            sede_id, pnf_id, trayecto_id, trimestre_id, 
                            unidad_curricular_id, docente_id, espacio_id, 
                            bloque_id, horario_id, dia, duracion, created_at, updated_at
                        ) VALUES (
                            p_sede_id, p_pnf_id, 
                            p_trayecto_id, p_trimestre_id,
                            p_clase_record.unidad_curricular_id, v_docente_alt,
                            p_clase_record.espacio_id, p_clase_record.bloque_id,
                            p_horario_id, p_clase_record.dia, p_clase_record.duracion,
                            NOW(), NOW()
                        );
                        
                        p_advertencias := array_append(p_advertencias, 
                            \'Conflicto resuelto: UC \' || p_clase_record.unidad_curricular_id || 
                            \' - Nuevo docente: \' || v_docente_alt
                        );
                        v_encontrado := true;
                        RETURN;
                        
                    EXCEPTION WHEN OTHERS THEN
                        CONTINUE;
                    END;
                END LOOP;
                
                IF NOT v_encontrado THEN
                    FOR v_espacio_alt IN (
                        SELECT e.id
                        FROM espacios e
                        WHERE e.sede_id = p_sede_id
                          AND e.id != p_clase_record.espacio_id
                          AND NOT EXISTS (
                            SELECT 1 FROM clases c
                            JOIN horarios h ON c.horario_id = h.id
                            WHERE h.lapso_academico = p_lapso_academico
                              AND c.trimestre_id = p_trimestre_id
                              AND c.docente_id = p_clase_record.docente_id
                              AND c.espacio_id = e.id
                              AND c.dia = p_clase_record.dia
                              AND c.bloque_id = p_clase_record.bloque_id
                          )
                        LIMIT 1
                    ) LOOP
                        BEGIN
                            INSERT INTO clases (
                                sede_id, pnf_id, trayecto_id, trimestre_id, 
                                unidad_curricular_id, docente_id, espacio_id, 
                                bloque_id, horario_id, dia, duracion, created_at, updated_at
                            ) VALUES (
                                p_sede_id, p_pnf_id, 
                                p_trayecto_id, p_trimestre_id,
                                p_clase_record.unidad_curricular_id, p_clase_record.docente_id,
                                v_espacio_alt, p_clase_record.bloque_id,
                                p_horario_id, p_clase_record.dia, p_clase_record.duracion,
                                NOW(), NOW()
                            );
                            
                            p_advertencias := array_append(p_advertencias, 
                                \'Conflicto resuelto: UC \' || p_clase_record.unidad_curricular_id || 
                                \' - Nuevo espacio: \' || v_espacio_alt
                            );
                            v_encontrado := true;
                            RETURN;
                            
                        EXCEPTION WHEN OTHERS THEN
                            CONTINUE;
                        END;
                    END LOOP;
                END IF;
                
                IF NOT v_encontrado THEN
                    p_advertencias := array_append(p_advertencias, 
                        \'No se pudo resolver conflicto para UC \' || p_clase_record.unidad_curricular_id || 
                        \'. Requiere ajuste manual.\'
                    );
                END IF;
            END;
            $function$
        ');
    }

    private function createTriggers()
    {
        // SOLO el trigger para check_clase_uniqueness
        DB::statement('
            CREATE TRIGGER tr_check_clase_uniqueness
            BEFORE INSERT OR UPDATE ON clases
            FOR EACH ROW EXECUTE FUNCTION check_clase_uniqueness();
        ');
    }

    private function dropTriggers()
    {
        DB::statement('DROP TRIGGER IF EXISTS tr_check_clase_uniqueness ON clases CASCADE');
    }

    private function dropFunctions()
    {
        DB::statement('DROP FUNCTION IF EXISTS public.check_clase_uniqueness() CASCADE');
        DB::statement('DROP FUNCTION IF EXISTS public.crear_horario_automatico(bigint, bigint, bigint) CASCADE');
        DB::statement('DROP FUNCTION IF EXISTS public.encontrar_bloque_alternativo(bigint, bigint, varchar, bigint, text) CASCADE');
        DB::statement('DROP FUNCTION IF EXISTS public.encontrar_dia_alternativo(bigint, bigint, bigint, bigint, text) CASCADE');
        DB::statement('DROP FUNCTION IF EXISTS public.encontrar_docente_alternativo(bigint, varchar, bigint, bigint, text) CASCADE');
        DB::statement('DROP FUNCTION IF EXISTS public.encontrar_espacio_alternativo(bigint, varchar, bigint, bigint, text) CASCADE');
        DB::statement('DROP FUNCTION IF EXISTS public.encontrar_mejor_docente_uc(bigint, bigint, text) CASCADE');
        DB::statement('DROP FUNCTION IF EXISTS public.existe_conflicto_clase(bigint, bigint, varchar, bigint, bigint, text, bigint) CASCADE');
        DB::statement('DROP FUNCTION IF EXISTS public.existe_conflicto_unico(bigint, bigint, varchar, bigint, bigint, text) CASCADE');
        DB::statement('DROP FUNCTION IF EXISTS public.resolver_conflicto_clase(record, bigint, bigint, text, bigint, bigint, bigint, text[]) CASCADE');
    }
}
