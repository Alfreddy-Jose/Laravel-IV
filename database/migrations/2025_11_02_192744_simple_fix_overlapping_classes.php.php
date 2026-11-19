<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SimpleFixOverlappingClasses extends Migration
{
    public function up()
    {
        // PASO 1: Eliminar función existente
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(bigint,
                bigint,
                text,
                bigint,
                integer,
                bigint,
                text,
                bigint
            );');

        // PASO 2: Crear función simplificada pero efectiva
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
            BEGIN
                -- Calcular bloque final
                v_bloque_final := p_bloque_inicio + p_duracion - 1;
                
                -- Verificar límites de bloques
                IF v_bloque_final > (SELECT COUNT(*) FROM bloques_turnos) OR p_bloque_inicio < 1 THEN
                    RETURN false;
                END IF;

                -- VERIFICACIÓN ÚNICA Y COMPLETA: Buscar cualquier solapamiento
                RETURN NOT EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE (
                        -- Mismo espacio o mismo docente
                        (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                        -- Mismo día y trimestre
                        AND c.dia = p_dia
                        AND c.trimestre_id = p_trimestre_id
                        AND h.lapso_academico = p_lapso_academico
                        -- Condición de solapamiento: los rangos se superponen
                        AND c.bloque_id <= v_bloque_final
                        AND (c.bloque_id + c.duracion - 1) >= p_bloque_inicio
                    )
                ) AND NOT EXISTS (
                    SELECT 1 
                    FROM clases c
                    WHERE c.horario_id = p_horario_id
                      AND c.dia = p_dia
                      AND (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                      AND c.bloque_id <= v_bloque_final
                      AND (c.bloque_id + c.duracion - 1) >= p_bloque_inicio
                );
            END;
            $function$
        ');

        // PASO 3: Agregar restricción única simple a la tabla clases
        DB::statement('
            CREATE UNIQUE INDEX IF NOT EXISTS unique_clase_espacio_dia_bloque 
            ON clases (espacio_id, dia, bloque_id, trimestre_id, horario_id) 
            WHERE duracion = 1;
        ');

        DB::statement('
            CREATE UNIQUE INDEX IF NOT EXISTS unique_clase_docente_dia_bloque 
            ON clases (docente_id, dia, bloque_id, trimestre_id, horario_id) 
            WHERE duracion = 1;
        ');
    }

    public function down()
    {
        // Eliminar índices
        DB::statement('DROP INDEX IF EXISTS unique_clase_espacio_dia_bloque;');
        DB::statement('DROP INDEX IF EXISTS unique_clase_docente_dia_bloque;');
        
        // Eliminar función
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(bigint,
                bigint,
                text,
                bigint,
                integer,
                bigint,
                text,
                bigint
            );');
        
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