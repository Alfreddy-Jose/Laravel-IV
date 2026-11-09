<?php

namespace App\Services;

use App\Models\Trayecto;
use Illuminate\Http\JsonResponse;

class DocentesProyeccion
{
    /**
     * Recopila las horas requeridas por materia y las horas disponibles
     * de los profesores asignados para un Trayecto dado.
     *
     * Cadena de relaciones:
     *   Trayecto → hasMany → Trimestre
     *             → belongsToMany → UnidadCurricular  (vía trimestre_unidad_curricular)
     *                             → belongsToMany → Docente (vía docente_unidad_curricular)
     *                                             → hasOne → CondicionContrato
     *
     * @param  int  $trayectoId
     * @return JsonResponse
     */
    public function execute(int $trayectoId): JsonResponse
    {
        // 1. Verificar que el Trayecto existe
        $trayecto = Trayecto::find($trayectoId);

        if (!$trayecto) {
            return response()->json(
                ['error' => 'Trayecto no encontrado.'],
                404
            );
        }

        // 2. Obtener todas las UCs del Trayecto con Eager Loading para evitar N+1.
        //    Cargamos: trimestres → sus unidadCurriculares → sus docentes → condicionContrato + persona
        $trayecto->load([
            'trimestres.unidadCurriculares.docentes.condicionContrato',
            'trimestres.unidadCurriculares.docentes.persona',
        ]);

        // 3. Aplanar y deduplicar las Unidades Curriculares (una UC puede
        //    aparecer en varios trimestres del mismo trayecto).
        $unidadesVistas = [];
        $resultado = [];

        foreach ($trayecto->trimestres as $trimestre) {
            foreach ($trimestre->unidadCurriculares as $uc) {

                // Evitar procesar la misma UC más de una vez
                if (in_array($uc->id, $unidadesVistas)) {
                    continue;
                }
                $unidadesVistas[] = $uc->id;

                // 4. Mapear los docentes con su información de contrato
                $docentesDisponibles = $uc->docentes->map(function ($docente) {
                    $condicion = $docente->condicionContrato;
                    $persona   = $docente->persona;

                    $nombreCompleto = $persona
                        ? trim("Prof. {$persona->nombre} {$persona->apellido}")
                        : "Docente #{$docente->id}";

                    return [
                        'id'              => $docente->id,
                        'nombre_completo' => $nombreCompleto,
                        'dedicacion'      => $condicion->dedicacion ?? null,
                        'horas_contrato'  => $docente->horas_dedicacion,
                    ];
                })->values();

                // 5. Sumatoria de horas disponibles de todos los docentes
                $horasDisponiblesTotales = $uc->docentes->sum('horas_dedicacion');

                $resultado[] = [
                    'unidad_curricular_id'        => $uc->id,
                    'nombre_materia'              => $uc->nombre,
                    'horas_teoricas_base_materia' => $uc->hora_teorica,
                    'horas_disponibles_totales'   => $horasDisponiblesTotales,
                    'docentes_disponibles'        => $docentesDisponibles,
                ];
            }
        }

        return response()->json($resultado, 200);
    }
}
