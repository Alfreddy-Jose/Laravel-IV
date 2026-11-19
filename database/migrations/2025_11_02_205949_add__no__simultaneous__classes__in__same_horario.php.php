<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNoSimultaneousClassesInSameHorario extends Migration
{
    public function up()
    {
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(bigint,
                bigint,
                text,
                bigint,
                integer,
                bigint,
                text,
                bigint
            );');

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
                IF v_bloque_final > v_bloques_totales OR p_bloque_inicio < 1 THEN
                    RETURN false;
                END IF;

                -- VERIFICACIÓN 1: Conflictos de ESPACIO
                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE c.espacio_id = p_espacio_id
                      AND c.dia = p_dia
                      AND c.trimestre_id = p_trimestre_id
                      AND h.lapso_academico = p_lapso_academico
                      AND c.bloque_id <= v_bloque_final
                      AND (c.bloque_id + c.duracion - 1) >= p_bloque_inicio
                ) THEN
                    RETURN false;
                END IF;

                -- VERIFICACIÓN 2: Conflictos de DOCENTE
                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE c.docente_id = p_docente_id
                      AND c.dia = p_dia
                      AND c.trimestre_id = p_trimestre_id
                      AND h.lapso_academico = p_lapso_academico
                      AND c.bloque_id <= v_bloque_final
                      AND (c.bloque_id + c.duracion - 1) >= p_bloque_inicio
                ) THEN
                    RETURN false;
                END IF;

                -- VERIFICACIÓN 3: NUEVA - Evitar dos clases en el MISMO HORARIO
                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    WHERE c.horario_id = p_horario_id
                      AND c.dia = p_dia
                      AND c.bloque_id <= v_bloque_final
                      AND (c.bloque_id + c.duracion - 1) >= p_bloque_inicio
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
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(bigint,
                bigint,
                text,
                bigint,
                integer,
                bigint,
                text,
                bigint
            );');
        
        // Recrear función anterior (sin la verificación 3)
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
                SELECT COUNT(*) INTO v_bloques_totales FROM bloques_turnos;
                
                v_bloque_final := p_bloque_inicio + p_duracion - 1;
                
                IF v_bloque_final > v_bloques_totales OR p_bloque_inicio < 1 THEN
                    RETURN false;
                END IF;

                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE c.espacio_id = p_espacio_id
                      AND c.dia = p_dia
                      AND c.trimestre_id = p_trimestre_id
                      AND h.lapso_academico = p_lapso_academico
                      AND c.bloque_id <= v_bloque_final
                      AND (c.bloque_id + c.duracion - 1) >= p_bloque_inicio
                ) THEN
                    RETURN false;
                END IF;

                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    WHERE c.docente_id = p_docente_id
                      AND c.dia = p_dia
                      AND c.trimestre_id = p_trimestre_id
                      AND h.lapso_academico = p_lapso_academico
                      AND c.bloque_id <= v_bloque_final
                      AND (c.bloque_id + c.duracion - 1) >= p_bloque_inicio
                ) THEN
                    RETURN false;
                END IF;

                RETURN true;
            END;
            $function$
        ');
    }
}