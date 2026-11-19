<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRelativeNumberToTrimestres extends Migration
{
    public function up()
    {
        // PASO 1: Agregar campos necesarios uno por uno
        DB::statement('ALTER TABLE trimestres ADD COLUMN IF NOT EXISTS trayecto_id BIGINT;');
        DB::statement('ALTER TABLE trimestres ADD COLUMN IF NOT EXISTS numero_relativo INTEGER;');
        DB::statement('ALTER TABLE horarios ADD COLUMN IF NOT EXISTS trayecto_id BIGINT;');
        
        // Agregar las constraints después de agregar las columnas
        DB::statement('ALTER TABLE trimestres ADD CONSTRAINT fk_trimestres_trayecto FOREIGN KEY (trayecto_id) REFERENCES trayectos(id);');
        DB::statement('ALTER TABLE horarios ADD CONSTRAINT fk_horarios_trayecto FOREIGN KEY (trayecto_id) REFERENCES trayectos(id);');

        // PASO 2: Actualizar trimestres existentes - UNA SENTENCIA POR UPDATE
        // Trayecto 1: I, II, III
        DB::statement("UPDATE trimestres SET trayecto_id = 1, numero_relativo = 1 WHERE nombre = 'I';");
        DB::statement("UPDATE trimestres SET trayecto_id = 1, numero_relativo = 2 WHERE nombre = 'II';");
        DB::statement("UPDATE trimestres SET trayecto_id = 1, numero_relativo = 3 WHERE nombre = 'III';");
        
        // Trayecto 2: IV, V, VI  
        DB::statement("UPDATE trimestres SET trayecto_id = 2, numero_relativo = 1 WHERE nombre = 'IV';");
        DB::statement("UPDATE trimestres SET trayecto_id = 2, numero_relativo = 2 WHERE nombre = 'V';");
        DB::statement("UPDATE trimestres SET trayecto_id = 2, numero_relativo = 3 WHERE nombre = 'VI';");
        
        // Trayecto 3: VII, VIII, IX
        DB::statement("UPDATE trimestres SET trayecto_id = 3, numero_relativo = 1 WHERE nombre = 'VII';");
        DB::statement("UPDATE trimestres SET trayecto_id = 3, numero_relativo = 2 WHERE nombre = 'VIII';");
        DB::statement("UPDATE trimestres SET trayecto_id = 3, numero_relativo = 3 WHERE nombre = 'IX';");
        
        // Si tienes más trayectos, continúa aquí...
        // Trayecto 4: X, XI, XII (si existen)
        DB::statement("UPDATE trimestres SET trayecto_id = 4, numero_relativo = 1 WHERE nombre = 'X';");
        DB::statement("UPDATE trimestres SET trayecto_id = 4, numero_relativo = 2 WHERE nombre = 'XI';");
        DB::statement("UPDATE trimestres SET trayecto_id = 4, numero_relativo = 3 WHERE nombre = 'XII';");

        // PASO 3: Actualizar horarios existentes con trayecto_id (si es necesario)
        // Esto depende de cómo esté estructurada tu base de datos
        // DB::statement("UPDATE horarios SET trayecto_id = 1 WHERE ...");

        // PASO 4: Eliminar función existente
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(
                bigint,
                bigint,
                text,
                bigint,
                integer,
                bigint,
                text,
                bigint
            );');

        // PASO 5: Crear nueva función usando numero_relativo
        DB::statement('
            CREATE OR REPLACE FUNCTION public.verificar_bloques_consecutivos_disponibles(
                p_espacio_id bigint,
                p_docente_id bigint,
                p_dia text,
                p_bloque_inicio bigint,
                p_duracion integer,
                p_trimestre_id bigint,
                p_lapso_academico text,
                p_horario_id bigint,
                p_trayecto_id bigint
            )
            RETURNS boolean
            LANGUAGE plpgsql
            AS $function$
            DECLARE
                v_bloque_final bigint;
                v_bloques_totales integer;
                v_numero_relativo integer;
            BEGIN
                -- Obtener total de bloques
                SELECT COUNT(*) INTO v_bloques_totales FROM bloques_turnos;
                
                -- Calcular bloque final
                v_bloque_final := p_bloque_inicio + p_duracion - 1;
                
                -- Verificar que no nos salgamos de los bloques disponibles
                IF v_bloque_final > v_bloques_totales OR p_bloque_inicio < 1 THEN
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
                    JOIN trimestres t ON c.trimestre_id = t.id
                    WHERE c.espacio_id = p_espacio_id
                      AND c.dia = p_dia
                      AND t.trayecto_id = p_trayecto_id
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
                    JOIN trimestres t ON c.trimestre_id = t.id
                    WHERE c.docente_id = p_docente_id
                      AND c.dia = p_dia
                      AND t.trayecto_id = p_trayecto_id
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
        // Eliminar función
        DB::statement('DROP FUNCTION IF EXISTS public.verificar_bloques_consecutivos_disponibles(
                bigint,
                bigint,
                text,
                bigint,
                integer,
                bigint,
                text,
                bigint
            );');
        
        // Eliminar constraints primero
        DB::statement('ALTER TABLE trimestres DROP CONSTRAINT IF EXISTS fk_trimestres_trayecto;');
        DB::statement('ALTER TABLE horarios DROP CONSTRAINT IF EXISTS fk_horarios_trayecto;');
        
        // Eliminar columnas
        DB::statement('ALTER TABLE trimestres DROP COLUMN IF EXISTS trayecto_id;');
        DB::statement('ALTER TABLE trimestres DROP COLUMN IF EXISTS numero_relativo;');
        DB::statement('ALTER TABLE horarios DROP COLUMN IF EXISTS trayecto_id;');

        // Recrear función anterior (sin trayecto)
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
}