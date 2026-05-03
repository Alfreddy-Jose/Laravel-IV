<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\StoreSedeRequest;
use App\Http\Requests\UpdateSedeRequest;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Pnf;
use App\Models\Universidad;
use Illuminate\Support\Facades\DB;


class SedeController extends Controller
{
     public function index()
    {
        // Obtener todas las sedes
        $sedes = Sede::with('municipio:municipio,id_municipio', 'pnfs:id,nombre')
            ->select('id', 'nro_sede', 'nombre_sede', 'nombre_abreviado', 'direccion', 'municipio_id')
            ->get(); 

        // Enviando datos a la api
        return response()->json($sedes);
    }

        public function store(StoreSedeRequest $request)
    {
        $sede = Sede::create($request->validated());

        // Asignar pnfs a la sede
        if ($request->has('pnf_id')) {
            $sede->pnfs()->sync($request->pnf_id);
        }

        // Enviando respuesta al frontend
        return response()->json(['message' => 'Sede creada exitosamente']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sede $sede)
    {
        // Cargando relaciones con municipios
        // Y con PNF 
            $sede->load(['municipio' => function ($query) {
            $query->select('id_municipio', 'municipio', 'id_estado')
                ->with(['estado' => function ($query) {
                    $query->select('id_estado', 'estado');
                }]);
        }]);

        $sede->load(['pnfs' => function ($query) {
            $query->select('pnf_id', 'nombre');
        }]);

        // enviando datos al frontend
        return response()->json($sede);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSedeRequest $request, Sede $sede)
    {
        // Editando registro
        $sede->update($request->validated());

        // Asignar pnfs a la sede
        if ($request->has('pnf_id')) {
            $sede->pnfs()->sync($request->pnf_id);
        }

        // Enviando respuesta al frontend
        return response()->json(['message' => 'Sede Editada']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sede $sede)
    {

        try {
            DB::beginTransaction();

            // Eliminando registro
            $sede->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sede Eliminada'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Código de error de restricción de clave foránea en PostgreSQL
            if ($e->getCode() == '23503') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la sede porque tiene registros relacionados',
                    'error_type' => 'foreign_key_constraint'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la sede',
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

    // funcion para traer los estados
    public function getEstados()
    {
        $estados = Estado::all();

        return response()->json($estados);
    }

    // Función para Traer los Municipios segun el Estado Seleccionado
    public function getMunicipios($estado)
    {
        // traer todos los municipios de un estado 
        $municipios = Municipio::where('id_estado', $estado)->get();

        return response()->json($municipios);
    }

        public function getUniversidad()
    {
        $universidad = Universidad::first(); // Usar first() en lugar de get()

        if (!$universidad) {
            return response()->json(['message' => 'No se encontró información de la universidad'], 404);
        }

        return response()->json($universidad);
    }

    // Función para traer los PNFS
     public function getPnf()
    {
        $pnf = Pnf::select('id', 'nombre')->get();

        return response()->json($pnf);
    }
}
