<?php

namespace App\Http\Controllers\Api;

use App\Exports\ProyeccionSeccionesExport;
use App\Http\Controllers\Controller;
use App\Models\AnoAcademico;
use App\Http\Requests\IniciarProyeccionRequest;
use App\Http\Requests\StoreProyeccionRequest;
use App\Http\Requests\StorePaso2Request;
use App\Services\DocentesProyeccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pnf;
use App\Models\Proyeccion;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProyeccionSeccion;
use App\Http\Requests\FinalizarProyeccionRequest;

class ProyeccionController extends Controller
{
    public function __construct(
        protected DocentesProyeccion $docentesProyeccion
    ) {
    }

    public function getFactibilidadDocente(int $trayectoId): JsonResponse
    {
        return $this->docentesProyeccion->execute($trayectoId);
    }

    private function obtenerPnfIdDocente(): ?int
    {
        return DB::table('users')
            ->join('personas', 'users.persona_id', '=', 'personas.id')
            ->join('docentes', 'docentes.persona_id', '=', 'personas.id')
            ->where('users.id', auth()->id())
            ->value('docentes.pnf_id');
    }

    /**
     * Prevención de IDOR
     * Valida si el usuario actual tiene permisos para interactuar con el PNF solicitado.
     */
    private function autorizarAccesoPnf(int $pnfIdSolicitado): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // 1. Consultamos directamente la relación de roles en la BD
        // Omitimos el chequeo de guard de Spatie y usamos LOWER para comparar sin importar mayúsculas
        $esAdmin = $user->roles()
            ->whereRaw('LOWER(name) = ?', ['administrador'])
            ->exists();

        if ($esAdmin) {
            return true;
        }

        // 2. Si es un docente/coordinador regular
        $pnfRealDelDocente = $this->obtenerPnfIdDocente();

        return (int) $pnfRealDelDocente === (int) $pnfIdSolicitado;
    }

    /**
     * Obtiene las secciones de origen del año anterior para iniciar la proyección.
     */

    public function finalizarProyeccion(FinalizarProyeccionRequest $request)
    {
        $validated = $request->validated();

        try {
            // Actualizar únicamente el estado del contenedor creado en el Paso 1
            $proyeccion = Proyeccion::findOrFail($validated['proyeccion_id']);
            $proyeccion->update([
                'estado' => $validated['estado'] ?? 'finalizado',
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => '¡Proyección finalizada con éxito!',
                'proyeccion_id' => $proyeccion->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al finalizar la proyección: ' . $e->getMessage()
            ], 500);
        }
    }
    public function prepararPlantillaPaso1(IniciarProyeccionRequest $request): JsonResponse
    {
        // 1. Validamos si el usuario puede consultar el PNF
        if (!$this->autorizarAccesoPnf($request->pnf_id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tiene permisos para gestionar proyecciones en este PNF.'
            ], 403);
        }

        $anoObjetivo = AnoAcademico::findOrFail($request->ano_academico_id);

        // =========================================================================
        // NUEVO: Verificamos si ya existe una proyección (borrador o finalizada) para este Año y PNF
        // =========================================================================
        $proyeccionExistente = Proyeccion::with(['detalles.sede', 'detalles.trayecto'])
            ->where('ano_academico_id', $request->ano_academico_id)
            ->where('pnf_id', $request->pnf_id)
            ->first();

        if ($proyeccionExistente && $proyeccionExistente->detalles->count() > 0) {
            // ORDENAMIENTO INTELIGENTE: Forzamos al Trayecto 0 a ir primero
            $detallesOrdenados = $proyeccionExistente->detalles->sort(function ($a, $b) {
                $nombreA = strtolower(optional($a->trayecto)->nombre ?? '');
                $nombreB = strtolower(optional($b->trayecto)->nombre ?? '');

                // Detectamos si corresponde al Trayecto 0 (buscando el '0' o la palabra 'cero')
                $esA0 = str_contains($nombreA, '0') || str_contains($nombreA, 'cero');
                $esB0 = str_contains($nombreB, '0') || str_contains($nombreB, 'cero');

                // Si A es el trayecto 0 y B no, A va de primero (-1)
                if ($esA0 && !$esB0) {
                    return -1;
                }
                // Si B es el trayecto 0 y A no, B va de primero (1)
                if (! $esA0 && $esB0) {
                    return 1;
                }

                // Si ambos son del mismo trayecto, ordenamos alfabéticamente por la sección de origen
                if ($a->trayecto_id === $b->trayecto_id) {
                    return strcmp($a->seccion_origen, $b->seccion_origen);
                }

                // Para los demás trayectos (1, 2, 3...), respetamos su orden normal
                return $a->trayecto_id <=> $b->trayecto_id;
            })->values(); // .values() reindexa la colección limpia

            $plantillaRecuperada = $detallesOrdenados->map(function ($detalle) {
                return [
                    'sede_id'           => $detalle->sede_id,
                    'sede_nombre'       => optional($detalle->sede)->nombre_sede ?? 'N/D',
                    'trayecto_id'       => $detalle->trayecto_id,
                    'trayecto_nombre'   => optional($detalle->trayecto)->nombre ?? 'N/D',
                    'seccion_origen'    => $detalle->seccion_origen,
                    'seccion_destino'   => $detalle->seccion_destino,
                    'grupos_aprobados'  => $detalle->grupos_aprobados,
                    'grupos_reprobados' => $detalle->grupos_reprobados,
                    'crear_seccion'     => $detalle->crear_seccion ?? false,
                ];
            });

            return response()->json([
                'status' => 'success',
                'es_borrador_recuperado' => true,
                'proyeccion_id' => $proyeccionExistente->id,
                'estado' => $proyeccionExistente->estado,
                'message' => 'Se ha recuperado el progreso guardado anteriormente.',
                'meta' => [
                    'pnf_id' => $request->pnf_id,
                    'ano_proyectado' => $anoObjetivo->ano,
                ],
                'plantilla' => $plantillaRecuperada
            ]);
        }

        // =========================================================================
        // FLUJO NORMAL: Si NO existe nada guardado previamente, buscamos el histórico
        // =========================================================================
        $anoAnteriorValor = $anoObjetivo->ano - 1;
        $anoAnterior = AnoAcademico::where('ano', $anoAnteriorValor)->first();

        if (!$anoAnterior) {
            return response()->json([
                'status' => 'error',
                'message' => "No se puede iniciar la proyección porque no existen registros históricos para el año académico anterior ({$anoAnteriorValor})."
            ], 422);
        }

        $seccionesOrigen = DB::table('seccions')
            ->join('lapso_academicos', 'seccions.lapso_id', '=', 'lapso_academicos.id')
            ->join('matriculas', 'seccions.matricula_id', '=', 'matriculas.id')
            ->join('trayectos', 'seccions.trayecto_id', '=', 'trayectos.id')
            ->join('sedes', 'seccions.sede_id', '=', 'sedes.id')
            ->where('lapso_academicos.ano_academico_id', $anoAnterior->id)
            ->where('seccions.pnf_id', $request->pnf_id)
            ->select([
                'seccions.id as seccion_id',
                'seccions.nombre as seccion_origen',
                'sedes.id as sede_id',
                'sedes.nombre_sede as sede_nombre',
                'trayectos.id as trayecto_id',
                'trayectos.nombre as trayecto_nombre',
                'matriculas.nombre as tipo_matricula',
                'lapso_academicos.nombre_lapso as lapso_origen'
            ])
            ->get();

        if ($seccionesOrigen->isEmpty()) {
            return response()->json([
                'status' => 'info',
                'message' => 'No se encontraron secciones del año anterior para este PNF. Empezarás la proyección con una plantilla vacía.',
                'data' => []
            ]);
        }

        $plantilla = $seccionesOrigen->map(function ($seccion) {
            return [
                'sede_id' => $seccion->sede_id,
                'sede_nombre' => $seccion->sede_nombre,
                'trayecto_id' => $seccion->trayecto_id,
                'trayecto_nombre' => $seccion->trayecto_nombre,
                'seccion_origen' => $seccion->seccion_origen,
                'tipo_matricula' => $seccion->tipo_matricula,
                'lapso_origen' => $seccion->lapso_origen,
                'grupos_aprobados' => 0,
                'grupos_reprobados' => 0,
            ];
        });

        return response()->json([
            'status' => 'success',
            'es_borrador_recuperado' => false,
            'meta' => [
                'pnf_id' => $request->pnf_id,
                'ano_proyectado' => $anoObjetivo->ano,
                'ano_origen_historico' => $anoAnterior->ano,
            ],
            'plantilla' => $plantilla
        ]);
    }

    public function store(StoreProyeccionRequest $request)
    {
        // Validamos permisos
        if (!$this->autorizarAccesoPnf($request->pnf_id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tiene permisos para modificar proyecciones en este PNF.'
            ], 403);
        }

        // Ejecutamos transacción
        $resultado = DB::transaction(function () use ($request) {

            // Buscamos o creamos el contenedor principal de la proyección
            $proyeccion = Proyeccion::firstOrCreate(
                [
                    'ano_academico_id' => $request->ano_academico_id,
                    'pnf_id'           => $request->pnf_id,
                ],
                [
                    'estado'           => 'borrador'
                ]
            );

            // Bloqueo si ya está finalizada
            if ($proyeccion->estado === 'finalizado') {
                throw new \Exception('No se puede modificar esta proyección porque ya ha sido finalizada formalmente.');
            }

            foreach ($request->secciones as $seccion) {
                $proyeccion->detalles()->updateOrCreate(
                    [
                        'seccion_origen' => $seccion['seccion_origen']
                    ],
                    [
                        'sede_id'           => $seccion['sede_id'],
                        'trayecto_id'       => $seccion['trayecto_id'],
                        'seccion_destino'   => $seccion['seccion_destino'] ?? $seccion['seccion_origen'],
                        'grupos_aprobados'  => $seccion['grupos_aprobados'],   // ¡Aquí se guardan los cambios reales del usuario!
                        'grupos_reprobados' => $seccion['grupos_reprobados'], // ¡Aquí se guardan los cambios reales del usuario!
                        'crear_seccion'     => $seccion['crear_seccion'] ?? false
                    ]
                );
            }

            return $proyeccion->load('detalles');
        });

        return response()->json([
            'status'        => 'success',
            'message'       => 'Borrador guardado y actualizado con éxito.',
            'proyeccion_id' => $resultado->id,
            'estado'        => $resultado->estado,
            'detalles'      => $resultado->detalles
        ], 200);
    }

    public function obtenerPaso2(int $proyeccionId): JsonResponse
    {
        $proyeccion = Proyeccion::with(['detalles.sede', 'detalles.trayecto'])->find($proyeccionId);

        if (!$proyeccion) {
            return response()->json([
                'status' => 'error',
                'message' => 'Proyección no encontrada.'
            ], 404);
        }

        if (!$this->autorizarAccesoPnf($proyeccion->pnf_id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No autorizado para ver esta proyección.'
            ], 403);
        }

        // Factor promedio de estudiantes por grupo
        $estudiantesPorGrupo = 5;

        $seccionesProyectadas = $proyeccion->detalles->map(function ($detalle) use ($estudiantesPorGrupo) {
            $nombreTrayecto = strtolower(optional($detalle->trayecto)->nombre ?? '');
            $esTrayecto0 = str_contains($nombreTrayecto, '0') || str_contains($nombreTrayecto, 'cero');

            if ($esTrayecto0) {
                // REGLA TRAYECTO 0: Valor directo, sin multiplicación y sin reprobados
                $estudiantesAprobados = (int) $detalle->grupos_aprobados;
                $estudiantesReprobados = 0;
            } else {
                // REGLA TRAYECTOS NORMALES: Multiplicación por 5
                $estudiantesAprobados = $detalle->grupos_aprobados * $estudiantesPorGrupo;
                $estudiantesReprobados = $detalle->grupos_reprobados * $estudiantesPorGrupo;
            }

            return [
                'id' => $detalle->id,
                'sede_id' => $detalle->sede_id,
                'sede_nombre' => optional($detalle->sede)->nombre_sede ?? 'N/A',
                'trayecto_origen_id' => $detalle->trayecto_id,
                'trayecto_origen_nombre' => optional($detalle->trayecto)->nombre ?? 'N/A',
                'seccion_origen' => $detalle->seccion_origen,
                'seccion_destino' => $detalle->seccion_destino,

                // Métrica de Grupos / Estudiantes
                'grupos_aprobados' => $detalle->grupos_aprobados,
                'grupos_reprobados' => $esTrayecto0 ? 0 : $detalle->grupos_reprobados,

                // Métrica traducida a Estudiantes
                'estudiantes_aprobados_estimados' => $estudiantesAprobados,
                'estudiantes_reprobados_estimados' => $estudiantesReprobados,
                'estudiantes_totales_estimados' => $estudiantesAprobados + $estudiantesReprobados,

                // Banderas de control
                'crear_seccion' => (bool) $detalle->crear_seccion,
                'tiene_reincorporados' => (bool) ($detalle->tiene_reincorporados ?? false),
                'es_trayecto_cero' => $esTrayecto0,
            ];
        });

        // Totales consolidados
        $totalEstudiantesAprobados = $seccionesProyectadas->sum('estudiantes_aprobados_estimados');
        $totalEstudiantesReprobados = $seccionesProyectadas->sum('estudiantes_reprobados_estimados');

        // Grupos puros (excluyendo el trayecto 0 para los conteos de grupos)
        $gruposAprobadosTotales = $proyeccion->detalles->filter(function ($d) {
            $nombre = strtolower(optional($d->trayecto)->nombre ?? '');
            return !str_contains($nombre, '0') && !str_contains($nombre, 'cero');
        })->sum('grupos_aprobados');

        $gruposReprobadosTotales = $proyeccion->detalles->filter(function ($d) {
            $nombre = strtolower(optional($d->trayecto)->nombre ?? '');
            return !str_contains($nombre, '0') && !str_contains($nombre, 'cero');
        })->sum('grupos_reprobados');

        return response()->json([
            'status' => 'success',
            'proyeccion_id' => $proyeccion->id,
            'estado' => $proyeccion->estado,
            'resumen_métricas' => [
                'total_grupos_aprobados' => $gruposAprobadosTotales,
                'total_grupos_reprobados' => $gruposReprobadosTotales,
                'total_estudiantes_aprobados' => $totalEstudiantesAprobados,
                'total_estudiantes_reprobados' => $totalEstudiantesReprobados,
                'total_secciones_activas' => $proyeccion->detalles->where('crear_seccion', true)->pluck('seccion_destino')
                    ->unique()->count(),
            ],
            'detalles' => $seccionesProyectadas
        ]);
    }

    /**
     * Paso 2: Guardar los ajustes de secciones destino y activación.
     */
    public function storePaso2(StorePaso2Request $request): JsonResponse
    {
        $proyeccion = Proyeccion::findOrFail($request->proyeccion_id);

        if (!$this->autorizarAccesoPnf($proyeccion->pnf_id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No autorizado.'
            ], 403);
        }

        if ($proyeccion->estado === 'finalizado') {
            return response()->json([
                'status' => 'error',
                'message' => 'La proyección ya se encuentra finalizada.'
            ], 422);
        }

        DB::transaction(function () use ($request) {
            foreach ($request->detalles as $item) {
                DB::table('proyeccion_secciones')
                    ->where('id', $item['id'])
                    ->update([
                        'seccion_destino' => $item['seccion_destino'],
                        'crear_seccion' => $item['crear_seccion'],
                        'tiene_reincorporados' => $item['tiene_reincorporados'], // ◄ Se guarda la decisión
                        'updated_at' => now(),
                    ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Decisiones del Paso 2 guardadas exitosamente.'
        ]);
    }

    public function obtenerDatosSelects(): JsonResponse
    {
        $user = auth()->user();

        $anosAcademicos = AnoAcademico::where('status', 'planificacion')
            ->select('id', 'ano')
            ->orderBy('ano', 'desc')
            ->get();

        // Verificación agnóstica al Guard
        $esAdmin = $user->roles()
            ->whereRaw('LOWER(name) = ?', ['administrador'])
            ->exists();

        $pnfs = [];
        if ($esAdmin) {
            $pnfs = Pnf::select('id', 'nombre', 'abreviado')->orderBy('nombre', 'asc')->get();
        }

        $pnfCoordinador = $esAdmin ? null : $this->obtenerPnfIdDocente();

        return response()->json([
            'status' => 'success',
            'data' => [
                'anos_academicos' => $anosAcademicos,
                'pnfs' => $pnfs,
                'is_admin' => $esAdmin,
                'pnf_coordinador_id' => $pnfCoordinador
            ]
        ]);
    }

    public function exportarExcel(Request $request)
    {
        // Recibimos el arreglo de detalles (con los destinos ya generados) desde React
        $detalles = $request->input('detalles', []);
        $anoProyectado = $request->input('ano_proyectado');
        $anoOrigenHistorico = $request->input('ano_origen_historico');

        if (empty($anoProyectado) || empty($anoOrigenHistorico)) {
            $detalleId = isset($detalles[0]['id']) ? $detalles[0]['id'] : null;
            if ($detalleId) {
                $proyeccion = DB::table('proyeccion_secciones')
                    ->join('proyecciones', 'proyeccion_secciones.proyeccion_id', '=', 'proyecciones.id')
                    ->join('ano_academicos', 'proyecciones.ano_academico_id', '=', 'ano_academicos.id')
                    ->where('proyeccion_secciones.id', $detalleId)
                    ->select('ano_academicos.ano')
                    ->first();
                if ($proyeccion) {
                    if (empty($anoProyectado)) {
                        $anoProyectado = (string)$proyeccion->ano;
                    }
                    if (empty($anoOrigenHistorico)) {
                        $anoOrigenHistorico = (string)($proyeccion->ano - 1);
                    }
                }
            }
        }

        // Si por alguna razón sigue vacío, usamos los defaults
        $anoProyectado = $anoProyectado ?: '2026';
        $anoOrigenHistorico = $anoOrigenHistorico ?: '2025';

        return Excel::download(
            new ProyeccionSeccionesExport($detalles, $anoProyectado, $anoOrigenHistorico),
            'Proyeccion_Secciones_' . $anoProyectado . '.xlsx'
        );
    }
}
