<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnoAcademico;
use App\Http\Requests\StoreAnoAcademicoRequest;
use App\Http\Requests\UpdateAnoAcademicoRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class AnoAcademicoController extends Controller
{
    /**
     * Metodo para traer todos los Años Académicos
     */
    public function indexPanel()
    {
        $cronograma = AnoAcademico::with(['lapsos.tipoLapso'])
            ->orderBy('ano', 'desc')
            ->get()
            ->map(function ($ano) {
                return [
                    'id'           => $ano->id,
                    'ano'          => $ano->ano,
                    // Forzamos el formato YYYY-MM-DD nativo de Carbon
                    'fecha_inicio' => $ano->fecha_inicio ? $ano->fecha_inicio->format('d/m/Y') : null,
                    'fecha_fin'    => $ano->fecha_fin ? $ano->fecha_fin->format('d/m/Y') : null,
                    'status'       => $ano->status,
                    'estado_real'  => $ano->estado_real,
                    'lapsos'       => $ano->lapsos
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $cronograma
        ]);
    }

    /**
     * Registrar un nuevo Año Académico (Por defecto entra en modo 'cronograma')
     */
    public function store(StoreAnoAcademicoRequest $request)
    {
        try {

            // Combinamos los datos validados del formulario y forzamos el status inicial
            $data = array_merge($request->validated(), ['status' => 'planificacion']);
            // El status no se pide en el formulario, se asigna 'cronograma' por defecto en la migración
            $anoAcademico = AnoAcademico::create($data);

            return response()->json([
                'success' => true,
                'message' => "Año académico creado exitosamente.",
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al registrar el año académico.'
            ], 500);
        }
    }

    /**
     * Método para traer los datos de un Año Académico
     */
    public function show($id)
    {
        try {
            // Buscamos el año cargando de una vez la relación de sus lapsos asociados
            $anoAcademico = AnoAcademico::with('lapsos')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'           => $anoAcademico->id,
                    'ano'          => $anoAcademico->ano,
                    'fecha_inicio' => $anoAcademico->fecha_inicio->format('Y-m-d'),
                    'fecha_fin'    => $anoAcademico->fecha_fin->format('Y-m-d'),
                    'estado_real'  => $anoAcademico->estado_real, // El accesor dinámico
                    'lapsos'       => $anoAcademico->lapsos // Array de hijos para validar en React
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Año académico no encontrado.'
            ], 404);
        }
    }

    /**
     * Método para actualizar un Año Académico
     */
    public function update(UpdateAnoAcademicoRequest $request, $id)
    {
        $anoAcademico = AnoAcademico::findOrFail($id);
        $estado = $anoAcademico->estado_real; // Accesor que ya evalúa el estado

        try {
            $anoAcademico = AnoAcademico::findOrFail($id);
            $estado = $anoAcademico->estado_real;

            if ($estado === 'activo') {
                // El Request ya filtró que solo venga la fecha_fin válida
                $anoAcademico->update([
                    'fecha_fin' => $request->fecha_fin
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Año académico extendido con éxito (Solo se actualizó la fecha de fin).'
                ]);
            }

            if ($estado === 'planificacion') {
                // El Request ya validó todo el bloque estructural y el bloqueo de hijos
                $anoAcademico->update($request->only(['ano', 'fecha_inicio', 'fecha_fin']));

                return response()->json([
                    'success' => true,
                    'message' => 'Año académico modificado con éxito.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error interno al intentar actualizar el año académico.'
            ], 500);
        }
    }

    /**
     * ACCIÓN MANUAL: Forzar el inicio de un año académico antes de su fecha
     */
    public function forzarInicioAno($id)
    {
        $ano = AnoAcademico::findOrFail($id);

        if ($ano->estado_real === 'cerrado') {
            return response()->json(['success' => false, 'message' => 'No puedes reabrir un año histórico.'], 422);
        }

        // Buscamos si hay algún otro año activo y lo clausuramos
        $anoActivoActual = AnoAcademico::all()->first(fn ($a) => $a->estado_real === 'activo');
        if ($anoActivoActual) {
            $anoActivoActual->update(['status' => 'cerrado']);
        }

        // Activamos este año manualmente
        $ano->update(['status' => 'activo']);

        // Buscar únicamente el primer lapso de tipo ordinario/regular asociado a ese año académico
        $primerLapsoRegular = $ano->lapsos()
            ->whereHas('tipoLapso', function ($q) {
                $q->where('nombre', 'REGULAR')
                  ->orWhere('nombre', 'Regular')
                  ->orWhere('nombre', 'Ordinario')
                  ->orWhere('nombre', 'ORDINARIO');
            })
            ->orderBy('id', 'asc')
            ->first();

        if ($primerLapsoRegular) {
            $primerLapsoRegular->update(['status' => 'activo']);
        }

        return response()->json([
            'success' => true,
            'message' => "El año académico {$ano->ano} ha sido activado de manera anticipada."
        ]);
    }

    /**
     * ACCIÓN MANUAL: Clausurar un año académico antes de que venza su fecha fin
     */
    public function forzarCierreAno($id)
    {
        $ano = AnoAcademico::findOrFail($id);

        $ano->update(['status' => 'cerrado']);

        return response()->json([
            'success' => true,
            'message' => "El año académico {$ano->ano} ha sido cerrado permanentemente."
        ]);
    }

    /**
     * ELIMINAR UN AÑO ACADÉMICO (Con validación de dependencias)
     */
    public function destroy($id)
    {
        try {
            $ano = AnoAcademico::withCount('lapsos')->findOrFail($id);

            // VALIDACIÓN: Si el conteo de lapsos relacionados es mayor a 0, se deniega la acción.
            if ($ano->lapsos_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No es posible eliminar el año académico {$ano->ano} porque tiene {$ano->lapsos_count} lapso(s) relacionado(s). Debe eliminarlos primero."
                ], 422); // Código 422: Unprocessable Entity (Error de lógica de negocio)
            }

            // Si está libre de dependencias, se procede al borrado físico
            $ano->delete();

            return response()->json([
                'success' => true,
                'message' => "El año académico {$ano->ano} ha sido eliminado correctamente."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error interno al intentar eliminar el año académico.'
            ], 500);
        }
    }

    /**
     * Genera el reporte PDF del cronograma de años académicos.
     */
    public function generarPdf(Request $request)
    {
        // 1. Usuario autenticado y su PNF (los años académicos son globales, el PNF solo se informa en el título)
        $user = auth()->user();
        $pnfUser = $user?->pnf;

        // 2. Consulta base con relaciones
        $query = AnoAcademico::with(['lapsos.tipoLapso']);

        // 3. Filtro opcional por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $anos = $query->orderByDesc('ano')->get();

        // 4. Generar PDF
        $pdf = Pdf::loadView('pdf.anos_academicos', compact('anos', 'pnfUser'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('ANOS_ACADEMICOS_' . date('Y_m_d_His') . '.pdf');
    }
}
