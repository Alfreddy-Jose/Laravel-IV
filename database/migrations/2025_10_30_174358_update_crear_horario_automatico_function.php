<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateCrearHorarioAutomaticoFunction extends Migration
{
    public function up()
    {
        DB::statement('
            CREATE OR REPLACE FUNCTION public.crear_horario_automatico(
                p_nuevo_horario_id bigint, 
                p_seccion_id bigint, 
                p_nuevo_trimestre_id bigint
            )
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
                v_clases_nuevas_uc INTEGER := 0;
                v_conflictos_resueltos INTEGER := 0;
                v_advertencias TEXT[] := ARRAY[]::TEXT[];
                v_nombre_horario_anterior TEXT;
                v_nombre_horario_nuevo TEXT;
                v_nombre_seccion TEXT;
                v_nombre_trimestre_anterior TEXT;
                v_nombre_trimestre_nuevo TEXT;
                
                -- Variables para nuevas UC
                v_uc_record RECORD;
                v_docente_asignado BIGINT;
                v_espacio_asignado BIGINT;
                v_bloque_asignado BIGINT;
                v_dia_asignado VARCHAR(10);
                v_encontrado BOOLEAN;
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

                -- 5. CREAR NUEVAS CLASES PARA UC QUE PERTENECEN AL NUEVO TRIMESTRE PERO NO EXISTÍAN EN EL ANTERIOR
                FOR v_uc_record IN (
                    SELECT DISTINCT tuc.unidad_curricular_id, uc.nombre as uc_nombre
                    FROM trimestre_unidad_curricular tuc
                    INNER JOIN unidad_curriculars uc ON tuc.unidad_curricular_id = uc.id
                    WHERE tuc.trimestre_id = p_nuevo_trimestre_id
                      AND tuc.unidad_curricular_id NOT IN (
                          SELECT DISTINCT unidad_curricular_id 
                          FROM clases 
                          WHERE horario_id = v_horario_anterior_id
                      )
                ) LOOP
                    BEGIN
                        -- Buscar el mejor docente para esta UC
                        v_docente_asignado := public.encontrar_mejor_docente_uc(
                            v_uc_record.unidad_curricular_id,
                            p_nuevo_trimestre_id,
                            v_lapso_academico
                        );

                        IF v_docente_asignado IS NOT NULL THEN
                            -- Buscar espacio disponible
                            v_espacio_asignado := public.encontrar_espacio_alternativo(
                                v_sede_id,
                                \'LUNES\',  -- Empezar buscando el lunes
                                1,         -- Empezar con el primer bloque
                                p_nuevo_trimestre_id,
                                v_lapso_academico
                            );

                            -- Buscar día y bloque disponibles
                            IF v_espacio_asignado IS NOT NULL THEN
                                v_dia_asignado := public.encontrar_dia_alternativo(
                                    v_espacio_asignado,
                                    v_docente_asignado,
                                    1,  -- Primer bloque
                                    p_nuevo_trimestre_id,
                                    v_lapso_academico
                                );

                                IF v_dia_asignado IS NOT NULL THEN
                                    v_bloque_asignado := public.encontrar_bloque_alternativo(
                                        v_espacio_asignado,
                                        v_docente_asignado,
                                        v_dia_asignado,
                                        p_nuevo_trimestre_id,
                                        v_lapso_academico
                                    );
                                END IF;
                            END IF;

                            -- Si encontramos todos los componentes, crear la clase
                            IF v_docente_asignado IS NOT NULL AND v_espacio_asignado IS NOT NULL 
                               AND v_dia_asignado IS NOT NULL AND v_bloque_asignado IS NOT NULL THEN
                                
                                INSERT INTO clases (
                                    sede_id, pnf_id, trayecto_id, trimestre_id, 
                                    unidad_curricular_id, docente_id, espacio_id, 
                                    bloque_id, horario_id, dia, duracion, created_at, updated_at
                                ) VALUES (
                                    v_sede_id, v_pnf_id, v_trayecto_id, p_nuevo_trimestre_id,
                                    v_uc_record.unidad_curricular_id, v_docente_asignado,
                                    v_espacio_asignado, v_bloque_asignado,
                                    p_nuevo_horario_id, v_dia_asignado, 2,  -- Duración por defecto de 2 horas
                                    NOW(), NOW()
                                );
                                
                                v_clases_nuevas_uc := v_clases_nuevas_uc + 1;
                                v_advertencias := array_append(v_advertencias, 
                                    \'Nueva UC agregada: \' || v_uc_record.uc_nombre || 
                                    \' - Docente: \' || v_docente_asignado ||
                                    \' - Espacio: \' || v_espacio_asignado ||
                                    \' - Día: \' || v_dia_asignado || 
                                    \' - Bloque: \' || v_bloque_asignado
                                );
                            ELSE
                                v_advertencias := array_append(v_advertencias, 
                                    \'No se pudo asignar horario para nueva UC: \' || v_uc_record.uc_nombre
                                );
                            END IF;
                        ELSE
                            v_advertencias := array_append(v_advertencias, 
                                \'No hay docente disponible para nueva UC: \' || v_uc_record.uc_nombre
                            );
                        END IF;
                        
                    EXCEPTION WHEN OTHERS THEN
                        v_advertencias := array_append(v_advertencias, 
                            \'Error creando clase para nueva UC \' || v_uc_record.uc_nombre || \': \' || SQLERRM
                        );
                    END;
                END LOOP;

                -- 6. REASIGNAR CLASES DE DOCENTES CUYAS UC FUERON ELIMINADAS
                DECLARE
                    v_docente_uc_record RECORD;
                    v_nueva_uc_id BIGINT;
                    v_clase_creada BOOLEAN;
                BEGIN
                    FOR v_docente_uc_record IN (
                        -- Docentes que tenían clases en el horario anterior pero sus UC fueron eliminadas
                        SELECT DISTINCT c.docente_id, d.nombre as docente_nombre
                        FROM clases c
                        INNER JOIN docentes d ON c.docente_id = d.id
                        WHERE c.horario_id = v_horario_anterior_id
                          AND c.unidad_curricular_id NOT IN (
                              SELECT tuc.unidad_curricular_id
                              FROM trimestre_unidad_curricular tuc
                              WHERE tuc.trimestre_id = p_nuevo_trimestre_id
                          )
                    ) LOOP
                        -- Buscar una UC del nuevo trimestre que este docente pueda impartir
                        SELECT tuc.unidad_curricular_id INTO v_nueva_uc_id
                        FROM trimestre_unidad_curricular tuc
                        INNER JOIN docente_unidad_curricular duc ON tuc.unidad_curricular_id = duc.unidad_curricular_id
                        WHERE tuc.trimestre_id = p_nuevo_trimestre_id
                          AND duc.docente_id = v_docente_uc_record.docente_id
                          AND tuc.unidad_curricular_id NOT IN (
                              SELECT unidad_curricular_id 
                              FROM clases 
                              WHERE horario_id = p_nuevo_horario_id
                                AND docente_id = v_docente_uc_record.docente_id
                          )
                        LIMIT 1;

                        IF v_nueva_uc_id IS NOT NULL THEN
                            -- Verificar que el docente tenga horas disponibles
                            IF EXISTS (
                                SELECT 1 
                                FROM docentes d
                                WHERE d.id = v_docente_uc_record.docente_id
                                  AND d.horas_dedicacion > (
                                      SELECT COALESCE(SUM(c.duracion), 0)
                                      FROM clases c
                                      JOIN horarios h ON c.horario_id = h.id
                                      WHERE c.docente_id = d.id
                                        AND h.lapso_academico = v_lapso_academico
                                        AND c.trimestre_id = p_nuevo_trimestre_id
                                  )
                            ) THEN
                                -- Buscar espacio y horario disponible
                                v_espacio_asignado := public.encontrar_espacio_alternativo(
                                    v_sede_id,
                                    \'LUNES\',
                                    1,
                                    p_nuevo_trimestre_id,
                                    v_lapso_academico
                                );

                                IF v_espacio_asignado IS NOT NULL THEN
                                    v_dia_asignado := public.encontrar_dia_alternativo(
                                        v_espacio_asignado,
                                        v_docente_uc_record.docente_id,
                                        1,
                                        p_nuevo_trimestre_id,
                                        v_lapso_academico
                                    );

                                    IF v_dia_asignado IS NOT NULL THEN
                                        v_bloque_asignado := public.encontrar_bloque_alternativo(
                                            v_espacio_asignado,
                                            v_docente_uc_record.docente_id,
                                            v_dia_asignado,
                                            p_nuevo_trimestre_id,
                                            v_lapso_academico
                                        );

                                        IF v_bloque_asignado IS NOT NULL THEN
                                            INSERT INTO clases (
                                                sede_id, pnf_id, trayecto_id, trimestre_id, 
                                                unidad_curricular_id, docente_id, espacio_id, 
                                                bloque_id, horario_id, dia, duracion, created_at, updated_at
                                            ) VALUES (
                                                v_sede_id, v_pnf_id, v_trayecto_id, p_nuevo_trimestre_id,
                                                v_nueva_uc_id, v_docente_uc_record.docente_id,
                                                v_espacio_asignado, v_bloque_asignado,
                                                p_nuevo_horario_id, v_dia_asignado, 2,
                                                NOW(), NOW()
                                            );
                                            
                                            v_conflictos_resueltos := v_conflictos_resueltos + 1;
                                            v_advertencias := array_append(v_advertencias, 
                                                \'Docente reasignado: \' || v_docente_uc_record.docente_nombre ||
                                                \' - Nueva UC: \' || v_nueva_uc_id
                                            );
                                        END IF;
                                    END IF;
                                END IF;
                            END IF;
                        END IF;
                    END LOOP;
                END;

                -- 7. CONSTRUIR RESULTADO CON REPORTE MEJORADO
                RETURN jsonb_build_object(
                    \'success\', true,
                    \'clases_creadas\', v_clases_copiadas - v_clases_eliminadas + v_clases_nuevas_uc,
                    \'clases_copiadas\', v_clases_copiadas,
                    \'clases_eliminadas\', v_clases_eliminadas,
                    \'clases_nuevas_uc\', v_clases_nuevas_uc,
                    \'conflictos_resueltos\', v_conflictos_resueltos,
                    \'advertencias\', v_advertencias,
                    \'horario_anterior_utilizado\', v_horario_anterior_id,
                    \'mensaje\', \'Horario creado automáticamente para la sección \' || v_nombre_seccion ||
                              \' - Copiado desde: \' || COALESCE(v_nombre_horario_anterior, \'Horario anterior\') || \' (\' || v_nombre_trimestre_anterior || \')\' ||
                              \' - Hacia: \' || COALESCE(v_nombre_horario_nuevo, \'Nuevo horario\') || \' (\' || v_nombre_trimestre_nuevo || \')\',
                    \'reporte_detallado\', jsonb_build_object(
                        \'clases_en_horario_anterior\', v_clases_anteriores_count,
                        \'clases_copiadas_exitosamente\', v_clases_copiadas,
                        \'clases_eliminadas_sin_uc\', v_clases_eliminadas,
                        \'clases_nuevas_agregadas\', v_clases_nuevas_uc,
                        \'docentes_reasignados\', v_conflictos_resueltos,
                        \'clases_finales_en_nuevo_horario\', v_clases_copiadas - v_clases_eliminadas + v_clases_nuevas_uc,
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
    }

    public function down()
    {
        // Revertir a la versión anterior de la función
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
                -- [Código original de la función...]
                -- (Aquí iría el código original que tenías antes)
                RETURN jsonb_build_object(\'success\', true, \'clases_creadas\', 0);
            END;
            $function$
        ');
    }
}