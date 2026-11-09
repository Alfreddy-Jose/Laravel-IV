<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class GestionAcademicaService
{
    /**
     * Obtiene el panorama completo de la carga académica basándose en los horarios creados,
     * y cruza la información con el control de entregas para un tipo de documento específico.
     *
     * @param int $lapsoId
     * @param int $tipoDocumentoId
     * @param int|null $pnfId  Filtro opcional para el PNF del coordinador
     * @return \Illuminate\Support\Collection
     */


    public function obtenerMatrizDashboard(int $pnfId)
    {
        $lapsoId = app('lapso_actual');
        $lapsoMaestro = DB::table('lapso_academicos')->where('id', $lapsoId)->first();
        $lapsoTextoPlano = $lapsoMaestro ? $lapsoMaestro->nombre_lapso : $lapsoId;

        // 1. Obtener TODA la carga académica
        $cargas = DB::table('docente_unidad_curricular as duc')
            ->join('docentes as d', 'duc.docente_id', '=', 'd.id')
            ->join('personas as p', 'd.persona_id', '=', 'p.id')
            ->join('unidad_curriculars as uc', 'duc.unidad_curricular_id', '=', 'uc.id')
            // Validamos que la clase impartida le pertenezca exactamente a este docente
            ->join('clases as c', function ($join) {
                $join->on('uc.id', '=', 'c.unidad_curricular_id')
                    ->on('c.docente_id', '=', 'd.id');
            })
            ->join('horarios as h', 'c.horario_id', '=', 'h.id')
            ->join('seccions as s', 'h.seccion_id', '=', 's.id')
            ->where('h.lapso_academico', $lapsoTextoPlano)
            ->where('c.pnf_id', $pnfId)
            ->select(
                'd.id as docente_id',
                'p.nombre as docente_nombre',
                'p.apellido as docente_apellido',
                'uc.id as uc_id',
                'uc.nombre as uc_nombre',
                'uc.es_proyecto',
                's.id as seccion_id',
                's.nombre as seccion_nombre'
            )
            ->groupBy(
                'd.id',
                'p.nombre',
                'p.apellido',
                'uc.id',
                'uc.nombre',
                'uc.es_proyecto',
                's.id',
                's.nombre'
            )
            ->get();

        // 2. Obtener TODAS las entregas de esos docentes en este lapso
        $docenteIds = $cargas->pluck('docente_id')->unique();
        $entregas = DB::table('control_entregas')
            ->where('lapso_academico_id', $lapsoId)
            ->whereIn('docente_id', $docenteIds)
            ->get();

        // 3. Agrupar y formatear para React
        $matriz = [];
        foreach ($cargas->groupBy('docente_id') as $docenteId => $asignaciones) {
            $primeraAsignacion = $asignaciones->first();

            // Filtrar entregas de este docente
            $entregasDocente = $entregas->where('docente_id', $docenteId);

            $globales = $entregasDocente->whereNull('seccion_id')
                ->mapWithKeys(function ($entrega) {
                    return [
                        $entrega->tipo_documento_id => [
                            'entregado' => (bool) $entrega->entregado,
                            'observaciones' => $entrega->observaciones,
                            'archivo_url' => $entrega->archivo_path ? asset('storage/' . $entrega->archivo_path) : null
                        ]
                    ];
                })->toArray();

            // Mapear secciones
            $secciones = $asignaciones->map(function ($asig) use ($entregasDocente) {

                // Filtramos por sección Y por unidad curricular
                $entregasSeccion = $entregasDocente
                    ->where('seccion_id', $asig->seccion_id)
                    ->where('unidad_curricular_id', $asig->uc_id)
                    ->mapWithKeys(function ($entrega) {
                        return [
                            $entrega->tipo_documento_id => [
                                'entregado' => (bool) $entrega->entregado,
                                'observaciones' => $entrega->observaciones,
                                'archivo_url' => $entrega->archivo_path ? asset('storage/' . $entrega->archivo_path) : null
                            ]
                        ];
                    })->toArray();

                return [
                    'seccion_id' => $asig->seccion_id,
                    'seccion_nombre' => $asig->seccion_nombre,
                    'uc_id' => $asig->uc_id,
                    'uc_nombre' => $asig->uc_nombre,
                    'es_proyecto' => (bool) $asig->es_proyecto,
                    'entregas' => $entregasSeccion
                ];
            })->values();

            $matriz[] = [
                'docente_id' => $docenteId,
                'nombre_completo' => $primeraAsignacion->docente_nombre . ' ' . $primeraAsignacion->docente_apellido,
                'entregas_globales' => $globales,
                'secciones' => $secciones
            ];
        }

        return $matriz;
    }
}
