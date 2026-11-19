<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTrayectoTrimestreTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Primero eliminamos el trigger si existe
        DB::statement('DROP TRIGGER IF EXISTS trigger_crear_trimestres ON trayectos;');

        // Eliminamos la función si existe
        DB::statement('DROP FUNCTION IF EXISTS crear_trimestres_para_trayecto();');

        // Creamos la función actualizada con la nueva lógica de evaluación numérica directa
        DB::unprepared("
            CREATE OR REPLACE FUNCTION public.crear_trimestres_para_trayecto()
                RETURNS trigger
                LANGUAGE 'plpgsql'
                COST 100
                VOLATILE NOT LEAKPROOF
            AS \$\$
            DECLARE
                trayecto_num INTEGER;
                romanos TEXT[] := ARRAY['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 
                                       'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII', 'XVIII', 'XIX', 'XX',
                                       'XXI', 'XXII', 'XXIII', 'XXIV', 'XXV', 'XXVI', 'XXVII', 'XXVIII', 'XXIX', 'XXX'];
                i INTEGER;
                trimestre_base INTEGER;
                cant_trimestres INTEGER;
            BEGIN
                -- Para UPDATE, eliminar trimestres existentes antes de crear nuevos
                IF TG_OP = 'UPDATE' THEN
                    DELETE FROM public.trimestres WHERE trayecto_id = NEW.id;
                END IF;
                
                -- Extraer el número del trayecto del nombre de forma segura (ej: '0', '1', '2')
                BEGIN
                    trayecto_num := CAST(REGEXP_REPLACE(NEW.nombre, '[^0-9]', '', 'g') AS INTEGER);
                EXCEPTION WHEN OTHERS THEN
                    -- Por seguridad, si el campo no contiene números, por defecto es 1
                    trayecto_num := 1;
                END;
                
                -- Evaluación numérica directa
                IF trayecto_num = 0 THEN
                    cant_trimestres := 1; -- El Trayecto 0 (PIU) solo tiene 1 trimestre
                    trimestre_base := 0;  -- Apunta a romanos[1] que es 'I'
                ELSE
                    cant_trimestres := 3; -- Los trayectos regulares (1, 2, 3...) tienen 3 trimestres
                    trimestre_base := (trayecto_num - 1) * 3;
                END IF;
                
                -- Validación de límites de números romanos
                IF array_length(romanos, 1) < trimestre_base + cant_trimestres THEN
                    RAISE EXCEPTION 'No hay suficientes números romanos definidos para el trayecto %', trayecto_num;
                END IF;
                
                -- Crear los trimestres correspondientes para el trayecto
                FOR i IN 1..cant_trimestres LOOP
                    INSERT INTO public.trimestres (
                        nombre, 
                        numero_relativo,
                        trayecto_id, 
                        created_at, 
                        updated_at
                    ) VALUES (
                        romanos[trimestre_base + i],
                        i,
                        NEW.id,
                        NOW(),
                        NOW()
                    );
                END LOOP;
                
                RETURN NEW;
            END;
            \$\$;
        ");

        // Creamos el trigger
        DB::statement('
            CREATE TRIGGER trigger_crear_trimestres
                AFTER INSERT OR UPDATE ON trayectos
                FOR EACH ROW
                EXECUTE FUNCTION crear_trimestres_para_trayecto();
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trigger_crear_trimestres ON trayectos;');
        DB::unprepared('DROP FUNCTION IF EXISTS crear_trimestres_para_trayecto();');
    }
}
