<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trayecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrayectoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trayectos = Trayecto::select('id', 'nombre')->get();

        return response()->json($trayectos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validando
        $request->validate([
            'nombre' => 'required|string|unique:trayectos,nombre'
        ]);
        
        // guardando el trayecto
        Trayecto::create($request->all());

        return response()->json(['message' => 'Trayecto registrado exitosamente'], 201);
    }

    /**
     * Display the specified resource.
     */
/*    public function show(Trayecto $trayecto)
    {
        return response()->json($trayecto);
    }
*/
    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $trayecto)
    // {
    //     $request->validate([
    //         'nombre' => 'required|string|unique:trayectos,nombre'
    //     ]);
    //     // editar trayecto validado
    //     $trayecto = Trayecto::findOrFail($trayecto);
    //     $trayecto->update($request->all());
        
    //     return response()->json(['message' => 'Trayecto ediado exitosamente'], 201);
        
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trayecto $trayecto)
    {
        try {
            DB::beginTransaction();
            
            // Eliminando el registro
            $trayecto->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Trayecto eliminado exitosamente'
            ], 200);
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Código de error de restricción de clave foránea en PostgreSQL
            if ($e->getCode() == '23503') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el trayecto porque tiene registros relacionados',
                    'error_type' => 'foreign_key_constraint'
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el trayecto',
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
