<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoceroRequest;
use App\Http\Requests\UpdateVoceroRequest;
use App\Models\Persona;
use App\Models\Seccion;
use App\Models\Vocero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoceroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los voceros con sus relaciones
        $voceros = Vocero::with(['persona', 'seccion'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($voceros);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVoceroRequest $request)
    {
        Vocero::create($request->all());

        return response()->json(['message' => 'Vocero Registrado'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($vocero)
    {
        $vocero = Vocero::findOrFail($vocero);

        return response()->json($vocero);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVoceroRequest $request, Vocero $vocero)
    {
        $vocero->update($request->validated());

        return response()->json(['message' => 'Vocero Editado'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vocero $vocero)
    {
        try {
            DB::beginTransaction();
            
            // Eliminando el registro
            $vocero->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Vocero Eliminado'
            ], 200);
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Código de error de restricción de clave foránea en PostgreSQL
            if ($e->getCode() == '23503') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el vocero porque tiene registros relacionados',
                    'error_type' => 'foreign_key_constraint'
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el vocero',
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

    public function getDataSelect()
    {
        $secciones = Seccion::select('id', 'nombre')->get();
        
        $vocerosIds = Persona::where('tipo_persona', 'ESTUDIANTE')->select('id', 'cedula_persona', 'nombre', 'apellido')->get();
        $voceros = Persona::where('tipo_persona', 'ESTUDIANTE')
            ->whereDoesntHave('vocero')
            ->get();

        $vocerosEdit = $vocerosIds->map(function ($persona) {
            return [
                'id' => $persona->id,
                'nombre' => $persona->nombre . ' ' . $persona->apellido . ' - ' . $persona->cedula_persona,
            ];
        });
        $voceros = $voceros->map(function ($vocero) {
            return [
                'id' => $vocero->id,
                'nombre' => $vocero->nombre . ' ' . $vocero->apellido . ' - ' . $vocero->cedula_persona,
            ];
        });

        return response()->json([
            'secciones' => $secciones,
            'voceros' => $voceros,
            'vocerosEdit' => $vocerosEdit,
        ], 200);
    }
}
