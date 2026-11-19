<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateVerificarBloquesFunctionWithSeccion extends Migration
{
    public function up()
    {

        DB::statement('ALTER TABLE horarios DROP COLUMN IF EXISTS trayecto_id;');
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(bigint,
                bigint,
                text,
                bigint,
                integer,
                bigint,
                text,
                bigint);');

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
                v_numero_relativo integer;
                v_trayecto_id bigint;
            BEGIN
                -- Obtener total de bloques
                SELECT COUNT(*) INTO v_bloques_totales FROM bloques_turnos;
                
                -- Calcular bloque final
                v_bloque_final := p_bloque_inicio + p_duracion - 1;
                
                -- Verificar que no nos salgamos de los bloques disponibles
                IF v_bloque_final > v_bloques_totales OR p_bloque_inicio < 1 THEN
                    RETURN false;
                END IF;

                -- Obtener el trayecto_id desde la sección del horario
                SELECT s.trayecto_id INTO v_trayecto_id
                FROM horarios h
                JOIN seccion s ON h.seccion_id = s.id
                WHERE h.id = p_horario_id;

                -- Si no se encuentra el trayecto, retornar false
                IF v_trayecto_id IS NULL THEN
                    RETURN false;
                END IF;

                -- Obtener el número relativo del trimestre actual
                SELECT numero_relativo INTO v_numero_relativo 
                FROM trimestres 
                WHERE id = p_trimestre_id;

                -- VERIFICACIÓN 1: ESPACIO - mismo trayecto y mismo número relativo
                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    JOIN secciones s ON h.seccion_id = s.id
                    JOIN trimestres t ON c.trimestre_id = t.id
                    WHERE c.espacio_id = p_espacio_id
                      AND c.dia = p_dia
                      AND s.trayecto_id = v_trayecto_id
                      AND t.numero_relativo = v_numero_relativo
                      AND h.lapso_academico = p_lapso_academico
                      AND c.bloque_id <= v_bloque_final
                      AND (c.bloque_id + c.duracion - 1) >= p_bloque_inicio
                ) THEN
                    RETURN false;
                END IF;

                -- VERIFICACIÓN 2: DOCENTE - mismo trayecto y mismo número relativo
                IF EXISTS (
                    SELECT 1 
                    FROM clases c
                    JOIN horarios h ON c.horario_id = h.id
                    JOIN secciones s ON h.seccion_id = s.id
                    JOIN trimestres t ON c.trimestre_id = t.id
                    WHERE c.docente_id = p_docente_id
                      AND c.dia = p_dia
                      AND s.trayecto_id = v_trayecto_id
                      AND t.numero_relativo = v_numero_relativo
                      AND h.lapso_academico = p_lapso_academico
                      AND c.bloque_id <= v_bloque_final
                      AND (c.bloque_id + c.duracion - 1) >= p_bloque_inicio
                ) THEN
                    RETURN false;
                END IF;

                -- VERIFICACIÓN 3: MISMO HORARIO
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
                bigint);');
    }
}
