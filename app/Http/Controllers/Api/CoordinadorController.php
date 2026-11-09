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
        $user = auth()->user();

        // Obtener el pnf_id del usuario si es coordinador o tiene un PNF asignado
        $pnfUser = $user->pnf;
        $pnfId = $pnfUser?->id;

        $coordinadores = Coordinador::with([
            'docente:id,persona_id,pnf_id',
            'docente.persona:id,cedula_persona,nombre,apellido',
            'docente.pnf:id,nombre'
        ])
            ->activo() // Solo activos
            // Si existe pnfId, se filtra solo los coordinadores cuyo docente pertenezca a ese PNF
            ->when($pnfId, function ($query, $pnfId) {
                $query->whereHas('docente', function ($q) use ($pnfId) {
                    $q->where('pnf_id', $pnfId);
                });
            })
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
        $user = auth()->user();

        // Obtener el pnf_id del usuario (si tiene un PNF asociado)
        $pnfUser = $user->pnf;
        $pnfId = $pnfUser?->id;

        // 1. Consulta para docentes que NO son coordinadores
        $docentesQuery = Docente::with(['persona:id,cedula_persona,nombre,apellido', 'pnf:id,nombre'])
            ->whereDoesntHave('coordinador')
            ->when($pnfId, function ($q) use ($pnfId) {
                $q->where('pnf_id', $pnfId);
            });

        $docentes = $docentesQuery->get()->map(function ($docente) {
            return [
                'id' => $docente->id,
                'nombre' => 'V-' . $docente->persona->cedula_persona . ' - ' . $docente->persona->nombre . ' ' . $docente->persona->apellido,
                'nombre_pnf' => $docente->pnf?->nombre,
                'id_pnf' => $docente->pnf?->id,
            ];
        });

        // 2. Consulta para edición (si $boolean es true)
        $docentesEdit = null;

        if ($boolean) {
            $docentesEditQuery = Docente::with(['persona:id,cedula_persona,nombre,apellido'])
                ->when($pnfId, function ($q) use ($pnfId) {
                    $q->where('pnf_id', $pnfId);
                });

            $docentesEdit = $docentesEditQuery->get()->map(function ($docente) {
                return [
                    'id' => $docente->id,
                    'nombre' => 'V-' . $docente->persona->cedula_persona . ' - ' . $docente->persona->nombre . ' ' . $docente->persona->apellido,
                ];
            });
        }

        return response()->json([
            'docentes' => $docentes,
            'docentesEdit' => $docentesEdit,
        ], 200);
    }

    // Metodo para obtener unidades curriculares segun el docente seleccionado
    public function getUnidadesCurricularesPorDocente($docenteId)
    {
        $user = auth()->user();

        // 1. Buscar directamente al docente con su PNF para validación
        $docente = Docente::select('id', 'pnf_id')->find($docenteId);

        if (!$docente) {
            return response()->json([
                'message' => 'Docente no encontrado'
            ], 404);
        }

        // 2. Control de seguridad: Si no es ADMINISTRADOR, validar que pertenezca al mismo PNF
        if (!$user->hasRole('ADMINISTRADOR')) {
            $pnfId = $user->pnf?->id;

            if (!$pnfId || $docente->pnf_id !== $pnfId) {
                return response()->json([
                    'message' => 'No tiene permisos para consultar las unidades curriculares de este docente'
                ], 403);
            }
        }

        // 3. Obtener solo las Unidades Curriculares asociadas a dicho docente
        $unidadesCurriculares = $docente->unidades_curriculares()
            ->select('unidades_curriculares.id', 'unidades_curriculares.nombre')
            ->get();

        return response()->json([
            'unidadesCurriculares' => $unidadesCurriculares
        ], 200);
    }
}
