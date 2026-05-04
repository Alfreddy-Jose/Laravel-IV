<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMatriculaRequest;
use App\Http\Requests\UpdateMatriculaRequest;
use App\Models\Matricula;
use Illuminate\Support\Facades\DB;

class MatriculaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Seleccionando todos los tipos de matricula
        $matriculas = Matricula::select('id', 'nombre', 'numero')->get();

        // enviando datos a la api
        return response()->json($matriculas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMatriculaRequest $request)
    {
        // Creando Tipo de Matricula
        Matricula::create($request->all());

        // Enviando respuesta a la api
        return response()->json(['message' => 'Matrícula registrada exitosamente'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Matricula $matricula)
    {
        return response()->json($matricula);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMatriculaRequest $request, Matricula $matricula)
    {

        // Actualizando registro
        $matricula->update($request->all()); // <-- variable $id es el registro captado

        return response()->json(["message" => "Matrícula editada exitosamente"], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Matricula $matricula)
    {
        try {
            DB::beginTransaction();
            
            // Eliminando el registro
            $matricula->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Matrícula eliminada exitosamente'
            ], 200);
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Código de error de restricción de clave foránea en MySQL
            if ($e->getCode() == '23503') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la matrícula porque tiene registros relacionados',
                    'error_type' => 'foreign_key_constraint'
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la matrícula',
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
}
