<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCoordinadorRequest;
use App\Models\Coordinador;
use App\Models\Docente;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class CoordinadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        // Solo coordinadores activos
        $coordinadores = Coordinador::with([
            'docente:id,persona_id,pnf_id',
            'docente.persona:id,cedula_persona,nombre,apellido',
            'docente.pnf:id,nombre'
        ])
            ->activo() // Solo activos
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($coordinadores);
    }
/*     public function index()
    {
        // Obtener todos los Coordinadores
        $coordinadores = Coordinador::with(['docente:id,persona_id,pnf_id', 'docente.persona:id,cedula_persona,nombre,apellido', 'docente.pnf:id,nombre'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($coordinadores);
    } */

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCoordinadorRequest $request): JsonResponse
    {
        try {
            $coordinador = Coordinador::create($request->validated());

            $coordinador->load([
                'docente.persona:id,cedula_persona,nombre,apellido',
                'docente.pnf:id,nombre',
                'pnf:id,nombre,codigo'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Coordinador registrado exitosamente',
                'data' => $coordinador
            ], 201);
        } catch (\Exception $e) {
            /*             \Log::error('Error al registrar coordinador: ' . $e->getMessage(), [
                'request' => $request->except(['password', 'token']),
                'user_id' => auth()->id() ?? 'no autenticado'
            ]); */

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al registrar el coordinador',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coordinador $coordinador)
    {
        try {
            DB::beginTransaction();

            // Eliminando el registro
            $coordinador->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Coordinador Eliminado'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Código de error de restricción de clave foránea en PostgreSQL
            if ($e->getCode() == '23503') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el coordinador porque tiene registros relacionados',
                    'error_type' => 'foreign_key_constraint'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el coordinador',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDocentes($boolean)
    {
        $Docentes = Docente::with(['persona:id,cedula_persona,nombre,apellido', 'pnf:id,nombre'])->whereDoesntHave('coordinador')->get();


        $Docentes = $Docentes->map(function ($docente) {
            return [
                'id' => $docente->id,
                'nombre' => $docente->persona->nombre . ' ' . $docente->persona->apellido . ' - ' . $docente->persona->cedula_persona,
                'nombre_pnf' => $docente->pnf->nombre,
                'id_pnf' => $docente->pnf->id,
            ];
        });

        if ($boolean) {
            $DocentesIds = Docente::with(['persona:id,cedula_persona,nombre,apellido'])->get();
            $DocentesEdit = $DocentesIds->map(function ($docente) {
                return [
                    'id' => $docente->id,
                    'nombre' => $docente->persona->nombre . ' ' . $docente->persona->apellido . ' - ' . $docente->persona->cedula_persona,
                ];
            });
        }

        return response()->json([
            'docentes' => $Docentes,
            'docentesEdit' => $boolean ? $DocentesIds : null,
        ], 200);
    }
}
