<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vocero;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVoceroRequest;

class VoceroController extends Controller
{
    /**
     * Muestra la lista de voceros.
     * Permite filtrar por lapso_id (a través de secciones) o por tipo.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Obtener el pnf_id del usuario autenticado
        $pnfUser = $user->pnf;
        $pnfId = $pnfUser?->id;

        $query = Vocero::with([
            'estudiante.persona:id,cedula_persona,nombre,apellido,email',
            'seccion.lapso',
            'seccion.pnf:id,nombre' // Carga Eager para devolver la información del PNF
        ])
        // Filtro condicional por PNF del usuario (vía Sección)
        ->when($pnfId, function ($q) use ($pnfId) {
            $q->whereHas('seccion', function ($seccionQuery) use ($pnfId) {
                $seccionQuery->where('pnf_id', $pnfId);
            });
        });

        // Filtro por lapso académico
        if ($request->filled('lapso_id')) {
            $query->whereHas('seccion', function ($q) use ($request) {
                $q->where('lapso_id', $request->lapso_id);
            });
        }

        // Filtro opcional por sección
        if ($request->filled('seccion_id')) {
            $query->where('seccion_id', $request->seccion_id);
        }

        // Filtro opcional por tipo de vocero
        if ($request->filled('tipo_vocero')) {
            $query->where('tipo_vocero', $request->tipo_vocero);
        }

        $voceros = $query->latest()->get();

        return response()->json([
            'data' => $voceros
        ], 200);
    }

    /**
     * Registra/Designa un nuevo vocero.
     */
    public function store(StoreVoceroRequest $request)
    {
        $vocero = Vocero::create($request->validated());

        // Cargar relaciones para devolver el objeto completo a la UI
        $vocero->load(['estudiante.persona', 'seccion.lapso']);

        return response()->json([
            'message' => 'Vocero designado exitosamente.',
            'data'    => $vocero
        ], 201);
    }

    /**
     * Elimina/Remueve la designación de un vocero.
     */
    public function destroy(Vocero $vocero)
    {
        $vocero->delete();

        return response()->json([
            'message' => 'Designación de vocero removida correctamente.'
        ], 200);
    }
}
