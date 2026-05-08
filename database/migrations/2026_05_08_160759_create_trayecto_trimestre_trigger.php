<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrayectoTrimestreTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Primero eliminamos el trigger si existe
        DB::statement('DROP TRIGGER IF EXISTS trigger_crear_trimestres ON trayectos;');

        // Eliminamos la función si existe
        DB::statement('DROP FUNCTION IF EXISTS crear_trimestres_para_trayecto();');

        // Creamos la función actualizada con numero_relativo
        DB::statement('
            CREATE OR REPLACE FUNCTION public.crear_trimestres_para_trayecto()
                RETURNS trigger
                LANGUAGE plpgsql
            AS $function$ 
            DECLARE
                trayecto_num INTEGER;
                romanos TEXT[] := ARRAY[\'I\', \'II\', \'III\', \'IV\', \'V\', \'VI\', \'VII\', \'VIII\', \'IX\', \'X\', 
                                \'XI\', \'XII\', \'XIII\', \'XIV\', \'XV\', \'XVI\', \'XVII\', \'XVIII\', \'XIX\', \'XX\',
                                \'XXI\', \'XXII\', \'XXIII\', \'XXIV\', \'XXV\', \'XXVI\', \'XXVII\', \'XXVIII\', \'XXIX\', \'XXX\'];
                i INTEGER;
                trimestre_base INTEGER;
            BEGIN
                -- Para UPDATE, eliminar trimestres existentes antes de crear nuevos
                IF TG_OP = \'UPDATE\' THEN
                    DELETE FROM public.trimestres WHERE trayecto_id = NEW.id;
                END IF;
                
                -- Extraer el número del trayecto del nombre
                BEGIN
                    trayecto_num := CAST(REGEXP_REPLACE(NEW.nombre, \'[^0-9]\', \'\', \'g\') AS INTEGER);
                EXCEPTION WHEN OTHERS THEN
                    trayecto_num := 1;
                END;
                
                trimestre_base := (trayecto_num - 1) * 3;
                
                IF array_length(romanos, 1) < trimestre_base + 3 THEN
                    RAISE EXCEPTION \'No hay suficientes números romanos definidos para el trayecto %\', trayecto_num;
                END IF;
                
                -- Crear los 3 trimestres para el trayecto
                FOR i IN 1..3 LOOP
                    INSERT INTO public.trimestres (
                        nombre, 
                        numero_relativo,  -- NUEVO: número relativo (1, 2, 3)
                        trayecto_id, 
                        created_at, 
                        updated_at
                    ) VALUES (
                        romanos[trimestre_base + i],
                        i,  -- Este es el número relativo (siempre 1, 2, 3)
                        NEW.id,
                        NOW(),
                        NOW()
                    );
                END LOOP;
                
                RETURN NEW;
            END;
            $function$;
        ');

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
        DB::unprepared('DROP TRIGGER IF EXISTS after_trayecto_insert ON trayectos;');
        DB::unprepared('DROP FUNCTION IF EXISTS crear_trimestres_para_trayecto();');
    }
}

