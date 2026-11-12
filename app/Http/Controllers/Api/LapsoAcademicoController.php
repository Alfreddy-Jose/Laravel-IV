<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLapsoAcademicoRequest;
use App\Http\Requests\UpdateLapsoAcademicoRequest;
use App\Models\LapsoAcademico;
use App\Models\AnoAcademico;
use Illuminate\Http\JsonResponse;
use App\Models\TipoLapso;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LapsoAcademicoController extends Controller
{
    /**
     * Panel de Administración: Trae la lista completa de todos los lapsos registrados
     */
    public function index()
    {
        try {
            $lapsos = LapsoAcademico::with(['anoAcademico', 'tipoLapso'])
            // Hacemos el join con la tabla de años académicos usando la clave foránea
            ->join('ano_academicos', 'lapso_academicos.ano_academico_id', '=', 'ano_academicos.id')
            // Ordenamos por la columna 'ano' de la tabla unida
            ->orderBy('ano_academicos.ano', 'desc')
            // Evitamos colisión de IDs seleccionando los campos originales de la tabla base
            ->select('lapso_academicos.*')
            ->get()
            ->map(function ($lapso) {

                // LÓGICA DE HERENCIA: Si la fecha del lapso es null, extrae la del año padre
                $fechaInicioRaw = $lapso->fecha_inicio ?? ($lapso->anoAcademico ? $lapso->anoAcademico->fecha_inicio : null);
                $fechaFinRaw    = $lapso->fecha_fin    ?? ($lapso->anoAcademico ? $lapso->anoAcademico->fecha_fin : null);

                // Formateamos de manera segura garantizando que sean instancias de Carbon
                $fechaInicioFormateada = ($fechaInicioRaw instanceof \Carbon\Carbon)
                    ? $fechaInicioRaw->format('d/m/Y')
                    : ($fechaInicioRaw ? \Carbon\Carbon::parse($fechaInicioRaw)->format('d/m/Y') : null);

                $fechaFinFormateada = ($fechaFinRaw instanceof \Carbon\Carbon)
                    ? $fechaFinRaw->format('d/m/Y')
                    : ($fechaFinRaw ? \Carbon\Carbon::parse($fechaFinRaw)->format('d/m/Y') : null);

                return [
                    'id'               => $lapso->id,
                    'nombre_lapso'     => $lapso->nombre_lapso,
                    'fecha_inicio'     => $fechaInicioFormateada, // Ya no llegará null para los regulares
                    'fecha_fin'        => $fechaFinFormateada,    // Ya no llegará null para los regulares
                    'ano_academico_id' => $lapso->ano_academico_id,
                    'tipo_lapso_id'    => $lapso->tipo_lapso_id,

                    'ano_real_estado'  => $lapso->anoAcademico ? $lapso->anoAcademico->estado_real : 'cerrado',
                    'ano_perteneciente' => $lapso->anoAcademico ? $lapso->anoAcademico->ano : 'N/A',
                    'tipo_nombre'      => $lapso->tipoLapso ? $lapso->tipoLapso->nombre : 'N/A',
                    'estado'           => $lapso->estado,
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $lapsos
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al obtener el listado de lapsos académicos.',
                'error_debug' => $e->getMessage()
            ], 500);
        }
    }

    // Metodo para Traer Lapsos academicos activos
    public function indexActivos()
    {
        // Orden cronológico estable: el frontend toma el primer elemento como
        // lapso por defecto y debe coincidir con LapsoAcademico::actual().
        $lapsos = LapsoAcademico::activos()
            ->with(['anoAcademico', 'tipoLapso'])
            ->ordenCronologico()
            ->get();

        // Retornando los datos al Frontend
        return response()->json($lapsos);
    }

    // Metodo para traer lapsos academicos en Planificacion
    public function lapsosPlanificacion()
    {
        $lapsos = LapsoAcademico::with('tipolapso')
            ->where('status', 'planificacion')
            ->select('id', 'nombre_lapso', )
            ->get();

        return response()->json(['data' => $lapsos], 200);
    }

    /**
     * Registrar un nuevo lapso académico con validación de secuencia de fecha de inicio
     */
    public function store(StoreLapsoAcademicoRequest $request)
    {
        $ano = AnoAcademico::findOrFail($request->ano_academico_id);

        // 1. Validar que el año padre no esté cerrado
        if ($ano->estado_real === 'cerrado') {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden añadir lapsos a un año académico cerrado.'
            ], 422);
        }

        // 2. VALIDACIÓN DE FECHA DE INICIO (Evitar que se pisen cronológicamente)
        if ($request->filled('fecha_inicio')) {
            $inicioNuevo = $request->fecha_inicio;

            // Buscamos si ya existe algún lapso en el mismo año que inicie en la misma fecha o después
            $fechaInvalida = LapsoAcademico::where('ano_academico_id', $request->ano_academico_id)
                ->where(function ($query) use ($inicioNuevo) {
                    $query->where('fecha_inicio', '>=', $inicioNuevo);
                })
                ->exists();

            if ($fechaInvalida) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de inicio debe ser posterior a la de los lapsos ya registrados en este año.'
                ], 422);
            }
        }

        // 3. Si pasa los controles, se crea el lapso con los datos validados
        $lapso = LapsoAcademico::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Lapso Académico creado exitosamente.',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($lapso_academico)
    {
        try {
            // Cargamos el lapso junto con su año académico padre para validar estados
            $lapso = LapsoAcademico::with(['anoAcademico', 'tipoLapso'])->findOrFail($lapso_academico);

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'                => $lapso->id,
                    'nombre_lapso'      => $lapso->nombre_lapso,
                    'ano_academico_id'  => $lapso->ano_academico_id,
                    'tipo_lapso_id'     => $lapso->tipo_lapso_id,
                    'fecha_inicio'      => $lapso->fecha_inicio ? $lapso->fecha_inicio->format('Y-m-d') : null,
                    'fecha_fin'         => $lapso->fecha_fin ? $lapso->fecha_fin->format('Y-m-d') : null,
                    'estado'            => $lapso->estado, // El accesor ('activo', 'planificacion', 'cerrado')
                    'ano_real_estado'   => $lapso->anoAcademico ? $lapso->anoAcademico->estado_real : 'cerrado'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lapso académico no encontrado.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLapsoAcademicoRequest $request, $lapso_academico)
    {
        try {
            $lapso = LapsoAcademico::findOrFail($lapso_academico);
            $estado = $lapso->estado;

            // Si el lapso está ACTIVO, el Request ya garantizó que solo se procese la prórroga de fecha_fin
            if ($estado === 'activo') {
                $lapso->update([
                    'fecha_fin' => $request->fecha_fin
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Lapso académico extendido con éxito (Solo se actualizó la fecha de fin).'
                ]);
            }

            // Si está en PLANIFICACIÓN, se guardan todos los cambios estructurales permitidos
            if ($estado === 'planificacion') {

                // Opcional: Si manejas una función en tu modelo o un Observer que autogenera
                // el nombre_lapso concatenando año y tipo, se disparará automáticamente aquí.
                $lapso->update($request->only([
                    'ano_academico_id',
                    'tipo_lapso_id',
                    'fecha_inicio',
                    'fecha_fin'
                ]));

                return response()->json([
                    'success' => true,
                    'message' => 'Lapso académico modificado con éxito.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error interno al intentar actualizar el lapso académico.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lapso_academico)
    {
        try {
            $lapso = LapsoAcademico::findOrFail($lapso_academico);

            // Si el lapso está ACTIVO o CERRADO, se prohíbe su eliminación
            if ($lapso->estado === 'activo') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un lapso académico que se encuentra Activo. Primero debe ser cerrado o estar en planificación.'
                ], 422);
            }

            if ($lapso->estado === 'cerrado') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un lapso académico Cerrado, ya que forma parte del historial académico institucional.'
                ], 422);
            }

            // Solo se elimina si está en 'planificacion'
            $lapso->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lapso académico eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al intentar eliminar el lapso académico.'
            ], 500);
        }
    }

    /**
     * Forzar el Cierre Manual de un Lapso Académico
     */
    public function forzarInicioLapso($lapso)
    {
        try {
            $lapso = LapsoAcademico::findOrFail($lapso);

            // 1. Validar que el año académico no esté cerrado
            if ($lapso->anoAcademico && $lapso->anoAcademico->estado_real === 'cerrado') {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes iniciar un lapso si su Año Académico ya se encuentra cerrado.'
                ], 422);
            }

            // MODIFICACIÓN: Validar de forma estricta que el Año Académico esté ACTIVO
            if (!$lapso->anoAcademico || $lapso->anoAcademico->estado_real !== 'activo') {
                return response()->json([
                    'success' => false,
                    'message' => 'No es posible cambiar el estado de este lapso debido a que su Año Académico no se encuentra Activo.'
                ], 422); // Código 422 para indicar un error de lógica de negocio
            }

            // 2. REGLA DE ORO: Cerrar únicamente aquellos lapsos del mismo año académico que compartan exactamente el mismo tipo_lapso_id
            LapsoAcademico::where('ano_academico_id', $lapso->ano_academico_id)
                ->where('tipo_lapso_id', $lapso->tipo_lapso_id)
                ->where('id', '!=', $lapso->id)
                ->update(['status' => 'cerrado']);

            // 3. Activar el lapso actual
            $lapso->update([
                'status' => 'activo',
                'fecha_inicio' => $lapso->fecha_inicio ?? \Carbon\Carbon::today() // Si no tenía fecha, le asignamos hoy
            ]);

            return response()->json([
                'success' => true,
                'message' => "El lapso '{$lapso->nombre_lapso}' ha sido iniciado manualmente con éxito."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al intentar iniciar el lapso académico.'
            ], 500);
        }
    }

    /**
     * Forzar el Cierre Manual de un Lapso Académico
     */
    public function forzarCierreLapso($lapso)
    {
        try {
            $lapso = LapsoAcademico::findOrFail($lapso);

            $lapso->update([
                'status' => 'cerrado',
                'fecha_fin' => \Carbon\Carbon::today() // Seteamos la fecha de fin al día de hoy
            ]);

            return response()->json([
                'success' => true,
                'message' => "El lapso '{$lapso->nombre_lapso}' ha sido cerrado manualmente con éxito."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al intentar cerrar el lapso académico.'
            ], 500);
        }
    }

    // Obtener la Data Select
    public function getDataSelect()
    {
        $anosAptos = AnoAcademico::orderBy('ano', 'desc')
            ->get()
            // Filtramos usando el accesor dinámico 'estado_real'
            ->filter(function ($ano) {
                return $ano->estado_real === 'activo' || $ano->estado_real === 'planificacion';
            })
            // Reindexamos el array y formateamos la respuesta para el Select del Frontend
            ->values()
            ->map(function ($ano) {
                return [
                    'id'          => $ano->id,
                    'ano'         => $ano->ano,
                    'estado_real' => $ano->estado_real,
                ];
            });

        $tipoLapsos = TipoLapso::select('id', 'nombre')->get();


        // RETORNO CORRECTO: Estructuramos todo dentro de un mismo objeto JSON
        return response()->json([
            'success'     => true,
            'anosAptos'   => $anosAptos,
            'tipoLapsos'  => $tipoLapsos
        ], 200);
    }

    /**
     * Genera el reporte PDF del cronograma de lapsos académicos.
     */
    public function generarPdf(Request $request)
    {
        // 1. Usuario autenticado y su PNF (los lapsos son globales, el PNF solo se informa en el título)
        $user = auth()->user();
        $pnfUser = $user?->pnf;

        // 2. Consulta base con relaciones
        $query = LapsoAcademico::with(['anoAcademico', 'tipoLapso']);

        // 3. Filtro opcional por año académico
        if ($request->filled('ano_academico_id')) {
            $query->where('ano_academico_id', $request->ano_academico_id);
        }

        $lapsos = $query->orderByDesc('id')->get();

        // 4. Generar PDF
        $pdf = Pdf::loadView('pdf.lapsos', compact('lapsos', 'pnfUser'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('LAPSOS_' . date('Y_m_d_His') . '.pdf');
    }
}
