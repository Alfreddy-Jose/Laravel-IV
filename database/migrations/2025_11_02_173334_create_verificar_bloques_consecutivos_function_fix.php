<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateVerificarBloquesConsecutivosFunctionFix extends Migration
{
    public function up()
    {
        DB::statement('
            CREATE OR REPLACE FUNCTION public.verificar_bloques_consecutivos_disponibles(
                p_espacio_id bigint,
                p_docente_id bigint,
                p_dia varchar(10),
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
                -- Obtener total de bloques
                SELECT COUNT(*) INTO v_bloques_totales FROM bloques_turnos;
                
                -- Verificar que no nos salgamos de los bloques disponibles
                IF (p_bloque_inicio + p_duracion - 1) > v_bloques_totales THEN
                    RETURN false;
                END IF;
                
                -- Verificar cada bloque consecutivo
                FOR v_i IN 0..(p_duracion - 1) LOOP
                    v_bloque_actual := p_bloque_inicio + v_i;
                    
                    -- Verificar conflicto en espacio
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
                    
                    -- Verificar conflicto en docente
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
                    
                    -- Verificar conflicto en el mismo horario nuevo
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

    public function down()
    {
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles;');
    }
}