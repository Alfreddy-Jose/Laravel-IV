<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePnfRequest;
use App\Http\Requests\UpdatePnfRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\Pnf;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Persona::query()->with('municipio');

        if ($request->tipo_persona) {
            $query->where('tipo_persona', $request->tipo_persona);
        }
        if ($request->grado_inst) {
            $query->where('grado_inst', $request->grado_inst);
        }

        $personas = $query->get();

        return response()->json($personas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {
        try{
            DB::beginTransaction();

            // Gardando los registros
            Persona::create($request->all());

            DB::commit();

            // retornando confirmacion a la vista
            return response()->json([
                "message" => "Persona creada exitosamente"
            ]);
        }catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error inesperado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        $persona->load(['municipio' => function ($query) {
            $query->select('id_municipio', 'municipio', 'id_estado')
                ->with(['estado' => function ($query) {
                    $query->select('id_estado', 'estado');
                }]);
        }]);
        // enviando datos al frontend
        return response()->json($persona);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonaRequest $request, Persona $persona)
    {
        // Editando registro
        $persona->update($request->all());

        // Enviando respuesta al frontend
        return response()->json(['message' => 'Persona Editada']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona)
    {
        try {
            DB::beginTransaction();

            // Eliminando el registro
            $persona->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Persona Eliminada'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Código de error de restricción de clave foránea en PostgreSQL
            if ($e->getCode() == '23503') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la persona porque tiene registros relacionados',
                    'error_type' => 'foreign_key_constraint'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la persona',
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

    // Metodo para obtener las Personas
    public function getPnf()
    {
        $pnfs = Pnf::select('id', 'nombre')->get();

        return response()->json($pnfs);
    }

/*    public function generarPDF(Request $request)
    {
        // Seleccionar las Personas
        $query = Persona::query()->with('municipio');

        if ($request->tipo_persona) {
            $query->where('tipo_persona', $request->tipo_persona);
        }
        if ($request->grado_inst) {
            $query->where('grado_inst', $request->grado_inst);
        }

        $personas = $query->get();
        // Renderiza una vista blade para el PDF
        $pdf = Pdf::loadView('pdf.personas', compact('personas'));

        return $pdf->download('personas.pdf');
    }*/
}
