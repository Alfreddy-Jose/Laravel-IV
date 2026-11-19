<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixConsecutiveBlocksFinalSolution extends Migration
{
    public function up()
    {
        // Eliminar funciones existentes
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(bigint,
                bigint,
                text,
                bigint,
                integer,
                pbigint,
                text,
                bigint);');
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_disponibilidad_docente;');
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_disponibilidad_espacio;');

        // Crear función principal mejorada
        DB::statement('
            CREATE OR REPLACE FUNCTION public.verificar_bloques_consecutivos_disponibles(
                p_espacio_id bigint,
                p_docente_id bigint,
                p_dia text,
                p_bloque_inicio bigint,
                p_duracion integer,
                p_trimestre_id bigint,
                p_lapso_academico text,
                p_horario_id bigint
            )
            RETURNS boolean
            LANGUAGE plpgsql
            AS $function$
            DECLARE
                v_bloque_final bigint;
                v_bloques_totales integer;
            BEGIN
                -- Obtener total de bloques
                SELECT COUNT(*) INTO v_bloques_totales FROM bloques_turnos;
                
                -- Calcular bloque final
                v_bloque_final := p_bloque_inicio + p_duracion - 1;
                
                -- Verificar que no nos salgamos de los bloques disponibles
                IF v_bloque_final > v_bloques_totales THEN
                    RETURN false;
                END IF;
                
                -- VERIFICACIÓN 1: Conflictos directos en los bloques solicitados
                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE (
                        (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                        AND c.dia = p_dia
                        AND c.bloque_id BETWEEN p_bloque_inicio AND v_bloque_final
                        AND h.lapso_academico = p_lapso_academico
                        AND c.trimestre_id = p_trimestre_id
                    )
                ) THEN
                    RETURN false;
                END IF;
                
                -- VERIFICACIÓN 2: Conflictos en el mismo horario nuevo
                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    WHERE c.horario_id = p_horario_id
                      AND c.dia = p_dia
                      AND c.bloque_id BETWEEN p_bloque_inicio AND v_bloque_final
                      AND (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                ) THEN
                    RETURN false;
                END IF;
                
                -- VERIFICACIÓN 3: Clases que terminan justo antes del inicio propuesto
                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE (
                        (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                        AND c.dia = p_dia
                        AND (c.bloque_id + c.duracion) = p_bloque_inicio
                        AND h.lapso_academico = p_lapso_academico
                        AND c.trimestre_id = p_trimestre_id
                    )
                ) THEN
                    RETURN false;
                END IF;
                
                -- VERIFICACIÓN 4: Clases que empiezan justo después del final propuesto
                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE (
                        (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                        AND c.dia = p_dia
                        AND c.bloque_id = v_bloque_final + 1
                        AND h.lapso_academico = p_lapso_academico
                        AND c.trimestre_id = p_trimestre_id
                    )
                ) THEN
                    RETURN false;
                END IF;
                
                -- VERIFICACIÓN 5: Solapamientos parciales (caso general)
                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE (
                        (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                        AND c.dia = p_dia
                        AND c.bloque_id <= v_bloque_final
                        AND (c.bloque_id + c.duracion - 1) >= p_bloque_inicio
                        AND h.lapso_academico = p_lapso_academico
                        AND c.trimestre_id = p_trimestre_id
                    )
                ) THEN
                    RETURN false;
                END IF;
                
                RETURN true;
            END;
            $function$
        ');
    }

    public function down()
    {
        // Eliminar función creada
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(bigint,
                bigint,
                text,
                bigint,
                integer,
                pbigint,
                text,
                bigint);');

        // Recrear función básica original
        DB::statement('
            CREATE OR REPLACE FUNCTION public.verificar_bloques_consecutivos_disponibles(
                p_espacio_id bigint,
                p_docente_id bigint,
                p_dia character varying,
                p_bloque_inicio integer,
                p_duracion integer,
                p_trimestre_id bigint,
                p_lapso_academico text,
                p_horario_id bigint
            )
            RETURNS boolean
            LANGUAGE plpgsql
            AS $function$
            DECLARE
                v_bloque_actual integer;
                v_disponible boolean := true;
                v_bloques_totales integer;
                v_i integer;
            BEGIN
                SELECT COUNT(*) INTO v_bloques_totales FROM bloques_turnos;
                
                IF (p_bloque_inicio + p_duracion - 1) > v_bloques_totales THEN
                    RETURN false;
                END IF;
                
                FOR v_i IN 0..(p_duracion - 1) LOOP
                    v_bloque_actual := p_bloque_inicio + v_i;
                    
                    IF EXISTS (
                        SELECT 1 
                        FROM clases c
                        JOIN horarios h ON c.horario_id = h.id
                        WHERE h.lapso_academico = p_lapso_academico
                          AND c.trimestre_id = p_trimestre_id
                          AND c.espacio_id = p_espacio_id
                          AND c.dia = p_dia
                          AND c.bloque_id = v_bloque_actual
                    ) THEN
                        v_disponible := false;
                        EXIT;
                    END IF;
                    
                    IF EXISTS (
                        SELECT 1 
                        FROM clases c
                        JOIN horarios h ON c.horario_id = h.id
                        WHERE h.lapso_academico = p_lapso_academico
                          AND c.trimestre_id = p_trimestre_id
                          AND c.docente_id = p_docente_id
                          AND c.dia = p_dia
                          AND c.bloque_id = v_bloque_actual
                    ) THEN
                        v_disponible := false;
                        EXIT;
                    END IF;
                END LOOP;
                
                RETURN v_disponible;
            END;
            $function$
        ');
    }
}