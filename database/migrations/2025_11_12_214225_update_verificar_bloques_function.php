<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateVerificarBloquesFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Primero eliminamos la función existente
        DB::unprepared('
            DROP FUNCTION IF EXISTS verificar_bloques_consecutivos_disponibles(bigint, bigint, text, bigint, integer, bigint, text, bigint);
        ');

        // Usamos NOWDOC para evitar problemas de escape
        $functionSQL = <<<'SQL'
CREATE OR REPLACE FUNCTION verificar_bloques_consecutivos_disponibles(
    p_espacio_id bigint,
    p_docente_id bigint,
    p_dia text,
    p_bloque_id_inicio bigint,
    p_duracion integer,
    p_trimestre_id bigint,
    p_lapso_academico text,
    p_horario_id bigint
)
RETURNS boolean
LANGUAGE plpgsql
AS $$
DECLARE
    v_bloque_final_id bigint;
    v_bloques_totales integer;
    v_numero_relativo integer;
    v_trayecto_id bigint;
    v_posicion_inicio integer;
    v_posicion_final integer;
BEGIN
    -- Obtener la posición secuencial del bloque inicial (por ID)
    SELECT sequencia.posicion INTO v_posicion_inicio
    FROM (
        SELECT id, ROW_NUMBER() OVER (ORDER BY id) as posicion
        FROM bloques_turnos
    ) as sequencia
    WHERE sequencia.id = p_bloque_id_inicio;

    -- Si no encuentra el bloque, retornar false
    IF v_posicion_inicio IS NULL THEN
        RAISE NOTICE 'Bloque ID % no encontrado', p_bloque_id_inicio;
        RETURN false;
    END IF;

    -- Calcular posición final
    v_posicion_final := v_posicion_inicio + p_duracion - 1;
    
    -- Obtener total de bloques
    SELECT COUNT(*) INTO v_bloques_totales FROM bloques_turnos;
    
    -- Verificar que no nos salgamos de los bloques disponibles
    IF v_posicion_final > v_bloques_totales OR v_posicion_inicio < 1 THEN
        RAISE NOTICE 'Posiciones fuera de rango. Inicio: %, Final: %, Total: %', 
                     v_posicion_inicio, v_posicion_final, v_bloques_totales;
        RETURN false;
    END IF;

    -- Obtener el ID del bloque final
    SELECT sequencia.id INTO v_bloque_final_id
    FROM (
        SELECT id, ROW_NUMBER() OVER (ORDER BY id) as posicion
        FROM bloques_turnos
    ) as sequencia
    WHERE sequencia.posicion = v_posicion_final;

    -- Obtener el trayecto_id desde la sección del horario
    SELECT s.trayecto_id INTO v_trayecto_id
    FROM horarios h
    JOIN seccions s ON h.seccion_id = s.id
    WHERE h.id = p_horario_id;

    IF v_trayecto_id IS NULL THEN
        RAISE NOTICE 'No se encontró trayecto para horario_id: %', p_horario_id;
        RETURN false;
    END IF;

    -- Obtener el número relativo del trimestre actual
    SELECT numero_relativo INTO v_numero_relativo 
    FROM trimestres 
    WHERE id = p_trimestre_id;

    IF v_numero_relativo IS NULL THEN
        RAISE NOTICE 'No se encontró numero_relativo para trimestre_id: %', p_trimestre_id;
    END IF;

    -- VERIFICACIÓN 1: ESPACIO
    IF EXISTS (
        SELECT 1 
        FROM clases c
        JOIN horarios h ON c.horario_id = h.id
        JOIN seccions s ON h.seccion_id = s.id
        JOIN trimestres t ON c.trimestre_id = t.id
        WHERE c.espacio_id = p_espacio_id
          AND c.dia = p_dia
          AND s.trayecto_id = v_trayecto_id
          AND t.numero_relativo = v_numero_relativo
          AND h.lapso_academico = p_lapso_academico
          AND c.bloque_id IN (
              SELECT bt.id 
              FROM bloques_turnos bt
              JOIN (
                  SELECT id, ROW_NUMBER() OVER (ORDER BY id) as posicion
                  FROM bloques_turnos
              ) as seq ON bt.id = seq.id
              WHERE seq.posicion BETWEEN v_posicion_inicio AND v_posicion_final
          )
    ) THEN
        RAISE NOTICE 'Conflicto de espacio: espacio_id %, dia %, bloques % a % (posiciones % a %)', 
                     p_espacio_id, p_dia, p_bloque_id_inicio, v_bloque_final_id, v_posicion_inicio, v_posicion_final;
        RETURN false;
    END IF;

    -- VERIFICACIÓN 2: DOCENTE
    IF EXISTS (
        SELECT 1 
        FROM clases c
        JOIN horarios h ON c.horario_id = h.id
        JOIN seccions s ON h.seccion_id = s.id
        JOIN trimestres t ON c.trimestre_id = t.id
        WHERE c.docente_id = p_docente_id
          AND c.dia = p_dia
          AND s.trayecto_id = v_trayecto_id
          AND t.numero_relativo = v_numero_relativo
          AND h.lapso_academico = p_lapso_academico
          AND c.bloque_id IN (
              SELECT bt.id 
              FROM bloques_turnos bt
              JOIN (
                  SELECT id, ROW_NUMBER() OVER (ORDER BY id) as posicion
                  FROM bloques_turnos
              ) as seq ON bt.id = seq.id
              WHERE seq.posicion BETWEEN v_posicion_inicio AND v_posicion_final
          )
    ) THEN
        RAISE NOTICE 'Conflicto de docente: docente_id %, dia %, bloques % a % (posiciones % a %)', 
                     p_docente_id, p_dia, p_bloque_id_inicio, v_bloque_final_id, v_posicion_inicio, v_posicion_final;
        RETURN false;
    END IF;

    -- VERIFICACIÓN 3: MISMO HORARIO
    IF EXISTS (
        SELECT 1 
        FROM clases c
        WHERE c.horario_id = p_horario_id
          AND c.dia = p_dia
          AND c.bloque_id IN (
              SELECT bt.id 
              FROM bloques_turnos bt
              JOIN (
                  SELECT id, ROW_NUMBER() OVER (ORDER BY id) as posicion
                  FROM bloques_turnos
              ) as seq ON bt.id = seq.id
              WHERE seq.posicion BETWEEN v_posicion_inicio AND v_posicion_final
          )
    ) THEN
        RAISE NOTICE 'Conflicto en mismo horario: horario_id %, dia %, bloques % a % (posiciones % a %)', 
                     p_horario_id, p_dia, p_bloque_id_inicio, v_bloque_final_id, v_posicion_inicio, v_posicion_final;
        RETURN false;
    END IF;

    RAISE NOTICE '✅ Bloques disponibles: espacio_id %, docente_id %, dia %, bloques % a % (posiciones % a %)', 
                 p_espacio_id, p_docente_id, p_dia, p_bloque_id_inicio, v_bloque_final_id, v_posicion_inicio, v_posicion_final;
    RETURN true;
END;
$$;
SQL;

        DB::unprepared($functionSQL);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar la función en caso de rollback
        DB::unprepared('
            DROP FUNCTION IF EXISTS verificar_bloques_consecutivos_disponibles(bigint, bigint, text, bigint, integer, bigint, text, bigint);
        ');
    }
}