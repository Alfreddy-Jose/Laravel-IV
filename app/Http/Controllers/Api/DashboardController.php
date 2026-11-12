<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ControlEntrega;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\LapsoAcademico;
use App\Models\Seccion;
use App\Models\Vocero;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Retorna todas las métricas necesarias para renderizar el dashboard principal.
     */
    public function index(Request $request): JsonResponse
    {
        // -------------------------------------------------------------------------
        // 0. CAPTURA DE FILTROS (PNF DESDE AUTH / LAPSO DESDE REQUEST)
        // -------------------------------------------------------------------------
        $user = $request->user();

        // PNF desde el usuario autenticado (Si no posee PNF, es null y consulta todo)
        $pnfIdFilter = $user->pnf_id ?? $user->pnf?->id;

        // Captura el ID del lapso desde la cabecera X-Lapso-Id
        $lapsoIdHeader = $request->header('X-Lapso-Id');

        // -------------------------------------------------------------------------
        // 1. LAPSO ACADÉMICO
        // -------------------------------------------------------------------------
        $lapso = $lapsoIdHeader
            ? LapsoAcademico::with('anoAcademico')->find($lapsoIdHeader)
            : null;

        // Si no hay header (o apunta a un lapso inexistente) se usa el mismo
        // criterio que el middleware para que el dashboard y el resto de la
        // API trabajen siempre con el mismo período.
        $lapso ??= LapsoAcademico::actual()?->load('anoAcademico');

        $resumenLapso = null;

        if ($lapso) {
            $fechaInicio = $lapso->fecha_inicio ?? optional($lapso->anoAcademico)->fecha_inicio;
            $fechaFin    = $lapso->fecha_fin    ?? optional($lapso->anoAcademico)->fecha_fin;

            $porcentajeTranscurrido = 0;

            if ($fechaInicio && $fechaFin) {
                $hoy        = Carbon::today();
                $inicio     = Carbon::parse($fechaInicio);
                $fin        = Carbon::parse($fechaFin);
                $totalDias  = $inicio->diffInDays($fin);

                if ($totalDias > 0) {
                    $diasTranscurridos  = max(0, min($inicio->diffInDays($hoy), $totalDias));
                    $porcentajeTranscurrido = (int) round(($diasTranscurridos / $totalDias) * 100);
                }
            }

            $resumenLapso = [
                'nombre'                  => $lapso->nombre_lapso,
                'fecha_inicio'            => $fechaInicio ? Carbon::parse($fechaInicio)->toDateString() : null,
                'fecha_fin'               => $fechaFin ? Carbon::parse($fechaFin)->toDateString() : null,
                'porcentaje_transcurrido' => $porcentajeTranscurrido,
            ];
        }

        // -------------------------------------------------------------------------
        // 2. MÉTRICAS GENERALES (KPIs)
        // -------------------------------------------------------------------------
        $lapsoId = $lapso?->id;

        // Estudiantes
        $totalMatricula = Estudiante::query()
            ->when($lapsoId, fn ($q) => $q->whereHas('secciones', fn ($s) => $s->where('lapso_id', $lapsoId)))
            ->when($pnfIdFilter, fn ($q) => $q->whereHas('secciones', fn ($s) => $s->where('pnf_id', $pnfIdFilter)))
            ->count();

        // Secciones
        $totalSecciones = Seccion::query()
            ->when($lapsoId, fn ($q) => $q->where('lapso_id', $lapsoId))
            ->when($pnfIdFilter, fn ($q) => $q->where('pnf_id', $pnfIdFilter))
            ->count();

        // Docentes (Filtrados directamente por pnf_id si existe)
        $totalDocentes = Docente::query()
            ->when($pnfIdFilter, fn ($q) => $q->where('pnf_id', $pnfIdFilter))
            ->count();

        // Total Voceros (Filtrados vía Sección por PNF y Lapso Académico)
        $totalVoceros = Vocero::query()
            ->when($pnfIdFilter, fn ($q) => $q->whereHas('seccion', fn ($s) => $s->where('pnf_id', $pnfIdFilter)))
            ->when($lapsoId, fn ($q) => $q->whereHas('seccion', fn ($s) => $s->where('lapso_id', $lapsoId)))
            ->count();

        $metricasGenerales = [
            'total_matricula' => $totalMatricula,
            'total_secciones' => $totalSecciones,
            'total_docentes'  => $totalDocentes,
            'total_voceros'   => $totalVoceros,
        ];

        // -------------------------------------------------------------------------
        // 3. GRÁFICA DE ENTREGAS POR TRAYECTO / CATEGORÍA
        // -------------------------------------------------------------------------
        $configuracionTrayectos = [
            ['label' => 'Trayecto I',   'filtro_nombre' => '1', 'color' => '#3b82f6'],
            ['label' => 'Trayecto II',  'filtro_nombre' => '2', 'color' => '#06b6d4'],
            ['label' => 'Trayecto III', 'filtro_nombre' => '3', 'color' => '#6366f1'],
            ['label' => 'Trayecto IV',  'filtro_nombre' => '4', 'color' => '#a855f7'],
        ];

        $statsPorTrayecto = ControlEntrega::withoutGlobalScopes()
            ->when($lapsoId, fn ($q) => $q->where('lapso_academico_id', $lapsoId))
            ->when($pnfIdFilter, fn ($q) => $q->whereHas('seccion', fn ($s) => $s->where('pnf_id', $pnfIdFilter)))
            ->with('seccion.trayecto')
            ->get()
            ->groupBy(fn ($entrega) => (string) ($entrega->seccion?->trayecto?->nombre ?? 'Sin Trayecto'))
            ->map(function ($items) {
                return (object) [
                    'total'      => $items->count(),
                    'entregados' => $items->where('entregado', true)->count(),
                ];
            });

        $graficaEntregas = array_map(function ($config) use ($statsPorTrayecto) {
            $stats      = $statsPorTrayecto->get($config['filtro_nombre']);
            $total      = $stats?->total ?? 0;
            $entregados = $stats?->entregados ?? 0;
            $percentage = $total > 0 ? (int) round(($entregados / $total) * 100) : 0;

            return [
                'label'      => $config['label'],
                'percentage' => $percentage,
                'color'      => $config['color'],
            ];
        }, $configuracionTrayectos);

        // -------------------------------------------------------------------------
        // 4. SECCIONES POR TRAYECTO
        // -------------------------------------------------------------------------
        $seccionesPorTrayecto = Seccion::query()
            ->when($lapsoId, fn ($q) => $q->where('lapso_id', $lapsoId))
            ->when($pnfIdFilter, fn ($q) => $q->where('pnf_id', $pnfIdFilter))
            ->join('trayectos', 'seccions.trayecto_id', '=', 'trayectos.id')
            ->select('trayectos.nombre as trayecto', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('trayectos.nombre')
            ->orderBy('trayectos.nombre')
            ->get()
            ->map(fn ($row) => [
                'trayecto' => $row->trayecto,
                'cantidad' => (int) $row->cantidad,
            ])
            ->values()
            ->toArray();

        // -------------------------------------------------------------------------
        // 5. CONTROL DE ENTREGAS RECIENTES
        // -------------------------------------------------------------------------
        $entregasRecientes = ControlEntrega::withoutGlobalScopes()
            ->with([
                'docente.persona',
                'seccion.trayecto',
                'tipoDocumento',
            ])
            ->when($lapsoId, fn ($q) => $q->where('lapso_academico_id', $lapsoId))
            ->when($pnfIdFilter, fn ($q) => $q->whereHas('seccion', fn ($s) => $s->where('pnf_id', $pnfIdFilter)))
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(function (ControlEntrega $entrega) {
                $estatus = 'pendiente';

                if ($entrega->entregado) {
                    $estatus = 'completado';
                } elseif ($entrega->fecha_entrega && Carbon::parse($entrega->fecha_entrega)->isPast()) {
                    $estatus = 'retrasado';
                }

                $persona = $entrega->docente?->persona;
                $nombreDocente = trim(
                    ($persona?->nombre ?? '') . ' ' . ($persona?->apellido ?? '')
                ) ?: 'Sin asignar';

                return [
                    'id'                => $entrega->id,
                    'docente'           => $nombreDocente,
                    'unidad_curricular' => $entrega->tipoDocumento?->nombre ?? 'N/A',
                    'trayecto'          => (string) ($entrega->seccion?->trayecto?->nombre ?? 'N/A'),
                    'entrega'           => $entrega->observaciones ?? 'Sin descripción',
                    'estatus'           => $estatus,
                ];
            })
            ->values()
            ->toArray();

        // -------------------------------------------------------------------------
        // RESPUESTA FINAL
        // -------------------------------------------------------------------------
        return response()->json([
            'resumen_lapso'              => $resumenLapso,
            'metricas_generales'         => $metricasGenerales,
            'grafica_entregas'           => $graficaEntregas,
            'secciones_por_trayecto'     => $seccionesPorTrayecto,
            'control_entregas_recientes' => $entregasRecientes,
        ]);
    }
}
