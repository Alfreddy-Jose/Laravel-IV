<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixConsecutiveBlocksConflictV2 extends Migration
{
    public function up()
    {
        // Primero: Eliminar la función existente
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(bigint,
                bigint,
                text,
                bigint,
                integer,
                bigint,
                text,
                bigint);');

        // Segundo: Crear la nueva función que evita clases consecutivas
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
                v_bloque_actual bigint;
                v_bloque_final bigint;
                v_disponible boolean := true;
                v_bloques_totales integer;
                v_i integer;
            BEGIN
                -- Obtener total de bloques
                SELECT COUNT(*) INTO v_bloques_totales FROM bloques_turnos;
                
                -- Calcular bloque final
                v_bloque_final := p_bloque_inicio + p_duracion - 1;
                
                -- Verificar que no nos salgamos de los bloques disponibles
                IF v_bloque_final > v_bloques_totales THEN
                    RETURN false;
                END IF;
                
                -- Verificar cada bloque consecutivo
                FOR v_i IN 0..(p_duracion - 1) LOOP
                    v_bloque_actual := p_bloque_inicio + v_i;
                    
                    -- Verificar conflicto en espacio (mismo bloque)
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
                    
                    -- Verificar conflicto en docente (mismo bloque)
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
                    
                    -- Verificar conflicto en el mismo horario nuevo (mismo bloque)
                    IF EXISTS (
                        SELECT 1 
                        FROM clases c
                        WHERE c.horario_id = p_horario_id
                          AND c.dia = p_dia
                          AND c.bloque_id = v_bloque_actual
                          AND (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
                    ) THEN
                        v_disponible := false;
                        EXIT;
                    END IF;
                END LOOP;
                
                -- Si ya hay conflicto en los bloques principales, retornar false
                IF NOT v_disponible THEN
                    RETURN false;
                END IF;
                
                -- VERIFICACIÓN CRÍTICA: Evitar que una clase empiece justo después de donde termina otra
                
                -- Verificar si hay una clase que TERMINA en el bloque anterior al inicio de esta clase
                -- Esto previene que una clase empiece en p_bloque_inicio cuando otra termina en p_bloque_inicio - 1
                IF p_bloque_inicio > 1 THEN
                    -- Verificar conflicto en espacio (clase que termina justo antes)
                    IF EXISTS (
                        SELECT 1 
                        FROM clases c
                        JOIN horarios h ON c.horario_id = h.id
                        WHERE h.lapso_academico = p_lapso_academico
                          AND c.trimestre_id = p_trimestre_id
                          AND c.espacio_id = p_espacio_id
                          AND c.dia = p_dia
                          AND c.bloque_id = p_bloque_inicio - 1
                    ) THEN
                        RETURN false;
                    END IF;
                    
                    -- Verificar conflicto en docente (clase que termina justo antes)
                    IF EXISTS (
                        SELECT 1 
                        FROM clases c
                        JOIN horarios h ON c.horario_id = h.id
                        WHERE h.lapso_academico = p_lapso_academico
                          AND c.trimestre_id = p_trimestre_id
                          AND c.docente_id = p_docente_id
                          AND c.dia = p_dia
                          AND c.bloque_id = p_bloque_inicio - 1
                    ) THEN
                        RETURN false;
                    END IF;
                END IF;
                
                -- Verificar si hay una clase que EMPIEZA justo después del final de esta clase
                -- Esto previene que esta clase termine en v_bloque_final cuando otra empieza en v_bloque_final + 1
                IF v_bloque_final < v_bloques_totales THEN
                    -- Verificar conflicto en espacio (clase que empieza justo después)
                    IF EXISTS (
                        SELECT 1 
                        FROM clases c
                        JOIN horarios h ON c.horario_id = h.id
                        WHERE h.lapso_academico = p_lapso_academico
                          AND c.trimestre_id = p_trimestre_id
                          AND c.espacio_id = p_espacio_id
                          AND c.dia = p_dia
                          AND c.bloque_id = v_bloque_final + 1
                    ) THEN
                        RETURN false;
                    END IF;
                    
                    -- Verificar conflicto en docente (clase que empieza justo después)
                    IF EXISTS (
                        SELECT 1 
                        FROM clases c
                        JOIN horarios h ON c.horario_id = h.id
                        WHERE h.lapso_academico = p_lapso_academico
                          AND c.trimestre_id = p_trimestre_id
                          AND c.docente_id = p_docente_id
                          AND c.dia = p_dia
                          AND c.bloque_id = v_bloque_final + 1
                    ) THEN
                        RETURN false;
                    END IF;
                END IF;
                
                RETURN true;
            END;
            $function$
        ');
    }

    public function down()
    {
        // Eliminar la función corregida
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(bigint,
                bigint,
                text,
                bigint,
                integer,
                bigint,
                text,
                bigint);');

        // Recrear la función original (sin la verificación de bloques consecutivos)
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
                    
                    IF EXISTS (
                        SELECT 1 
                        FROM clases c
                        WHERE c.horario_id = p_horario_id
                          AND c.dia = p_dia
                          AND c.bloque_id = v_bloque_actual
                          AND (c.espacio_id = p_espacio_id OR c.docente_id = p_docente_id)
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
