<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUnidadCurricularRequest;
use App\Http\Requests\UpdateUnidadCurricularRequest;
use App\Models\Trimestre;
use App\Models\UnidadCurricular;
use Barryvdh\DomPDF\Facade\Pdf;

class UnidadCurricularController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Resolver pnf_id usando el accessor de User
        $pnfId = $user->pnf?->id;

        $query = UnidadCurricular::with(['trimestres.trayecto']);

        // Filtrar directamente por la clave foránea pnf_id si el usuario tiene uno asignado.
        // Si $pnfId es null (admin sin PNF), no se aplica el filtro y muestra todas.
        if ($pnfId) {
            $query->where('pnf_id', $pnfId);
        }

        $unidades = $query->get()->map(function ($unidad) {
            return [
                'id'             => $unidad->id,
                'codigo'         => $unidad->codigo,
                'pnf_id'         => $unidad->pnf_id,
                'nombre'         => $unidad->nombre,
                'hora_total_est' => $unidad->hora_total_est,
                'hora_practica'  => $unidad->hora_practica,
                'periodo'        => $unidad->periodo,
                'hora_teorica'   => $unidad->hora_teorica,
                'unidad_credito' => $unidad->unidad_credito,
                'descripcion'    => $unidad->descripcion,
                'trimestres'     => $unidad->trimestres->map(function ($trimestre) {
                    return [
                        'id'          => $trimestre->id,
                        'trayecto_id' => $trimestre->trayecto_id,
                        'nombre'      => $trimestre->nombre_relativo,
                        'trayecto'    => $trimestre->trayecto?->nombre,
                    ];
                })
            ];
        });

        return response()->json($unidades, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnidadCurricularRequest $request)
    {
        $user = auth()->user();

        // Obtener PNF asignado al usuario o tomar el del request
        $pnfId = $user->pnf?->id ?? $request->input('pnf_id');

        // Guardando el registro
        $unidadCurricular = UnidadCurricular::create([
            'pnf_id'         => $pnfId, // <- Se asigna el PNF resolviendo la prioridad
            'nombre'         => $request->nombre,
            'codigo'         => $request->codigo,
            'descripcion'    => $request->descripcion,
            'unidad_credito' => $request->unidad_credito,
            'hora_teorica'   => $request->hora_teorica,
            'hora_practica'  => $request->hora_practica,
            'hora_total_est' => $request->hora_total_est,
            'periodo'        => $request->periodo
        ]);

        // Asociar trimestres
        if ($request->periodo === 'trimestral') {
            // Para trimestral: un solo trimestre (número)
            $unidadCurricular->trimestres()->sync([$request->trimestre_id]);
        } else {
            // Para semestral y anual: array de trimestres
            $unidadCurricular->trimestres()->sync($request->trimestre_id);
        }

        return response()->json(['message' => 'Unidad Curricular Registrada'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Obtener datos del registro a actualizar
        $unidad_curricular = UnidadCurricular::with('trimestres')->findOrFail($id);
        return response()->json($unidad_curricular, 200);
    }

    public function horasUnidadCurricular($id)
    {
        // Obtener las horas totales de la unidad curricular (Solo las horas y el nombre)
        $unidad_curricular = UnidadCurricular::get(['id', 'nombre', 'hora_total_est'])->findOrFail($id);
        return response()->json($unidad_curricular, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnidadCurricularRequest $request, UnidadCurricular $unidad_curricular)
    {
        $user = auth()->user();

        // Resolver el PNF: Prioriza el del usuario autenticado; si es null, toma el del request
        $pnfId = $user->pnf?->id ?? $request->input('pnf_id');

        // Actualizar la unidad curricular
        $unidad_curricular->update([
            'pnf_id'         => $pnfId,
            'nombre'         => $request->nombre,
            'codigo'         => $request->codigo,
            'descripcion'    => $request->descripcion,
            'unidad_credito' => $request->unidad_credito,
            'hora_teorica'   => $request->hora_teorica,
            'hora_practica'  => $request->hora_practica,
            'hora_total_est' => $request->hora_total_est,
            'periodo'        => $request->periodo
        ]);

        // Sincronizar trimestres (elimina los antiguos y agrega los nuevos)
        if ($request->periodo === 'trimestral') {
            $unidad_curricular->trimestres()->sync([$request->trimestre_id]);
        } else {
            $unidad_curricular->trimestres()->sync($request->trimestre_id);
        }

        return response()->json(['message' => 'Unidad Curricular Editada']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnidadCurricular $unidad_curricular)
    {
        $unidad_curricular->delete();

        return response()->json(['message' => 'Unidad Curricular Eliminada']);
    }

    // Metodo para obtener los trimestres
    public function get_trimestres()
    {
        $trimestres = Trimestre::select('id', 'nombre', 'trayecto_id', 'numero_relativo')->get();

        $trimestres = $trimestres->map(function ($trimestre) {
            return [
                'id' => $trimestre->id,
                'nombre' => $trimestre->nombre_relativo, // Nombre relativo
                'trayecto_id' => $trimestre->trayecto_id,
            ];
        });

        return response()->json($trimestres);
    }

    public function getTrimestresByTrayecto($trayectoId)
    {
        $trimestres = Trimestre::where('trayecto_id', $trayectoId)
            ->orderBy('numero_relativo')
            ->get(['id', 'nombre', 'numero_relativo']);

        $trimestres = $trimestres->map(function ($trimestre) {
            return [
                'id' => $trimestre->id,
                'nombre' => $trimestre->nombre_relativo, // Mostrar el nombre relativo en lugar del original
            ];
        });

        return response()->json($trimestres);
    }

    public function getUnidadesByPnf($pnfId)
    {
        $unidades = UnidadCurricular::where('pnf_id', $pnfId)
            ->select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();

        return response()->json($unidades);
    }

    /**
     * Genera el reporte PDF del catálogo de unidades curriculares.
     */
    public function generarPdf(Request $request)
    {
        // 1. Usuario autenticado y su PNF (para filtrar por coordinador)
        $user = auth()->user();
        $pnfUser = $user?->pnf;
        $pnfId = $pnfUser?->id;

        // 2. Consulta base con relaciones
        $query = UnidadCurricular::with('trimestres');

        // 3. Filtrar por el PNF del usuario si aplica
        if ($pnfId && !$user->hasRole('ADMINISTRADOR')) {
            $query->where('pnf_id', $pnfId);
        }

        // 4. Filtros opcionales desde el request
        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        if ($request->filled('pnf_id')) {
            $query->where('pnf_id', $request->pnf_id);
        }

        $unidades = $query->orderBy('nombre')->get();

        // 5. Generar PDF
        $pdf = Pdf::loadView('pdf.unidad_curricular', compact('unidades', 'pnfUser'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('UNIDADES_CURRICULARES_' . date('Y_m_d_His') . '.pdf');
    }
}
