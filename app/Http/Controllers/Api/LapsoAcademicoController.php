<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLapsoAcademicoRequest;
use App\Http\Requests\UpdateLapsoAcademicoRequest;
use App\Models\LapsoAcademico;
use App\Models\TipoLapso;
use Illuminate\Support\Facades\DB;

class LapsoAcademicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Seleccionando datos del Lapso Academico
        $lapsos = LapsoAcademico::with('tipolapso')->orderBy('nombre_lapso', 'desc')->get();

        // enviar fecha en forma DMY en lugar de Y-m-d
        foreach ($lapsos as $lapso) {
            $lapso->fecha_inicio = date('d/m/Y', strtotime($lapso->fecha_inicio));
            $lapso->fecha_fin = date('d/m/Y', strtotime($lapso->fecha_fin));
        }


        // Retornando los datos al Frontend
        return response()->json($lapsos);
    }

    public function lapsosActivos()
    {
        // Seleccionando datos del Lapso Academico
        $lapsos = LapsoAcademico::with('tipolapso')->where('status', true)->get();

        // Retornando los datos al Frontend
        return response()->json($lapsos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLapsoAcademicoRequest $request)
    {
        // Creando registro
        LapsoAcademico::create($request->all());

        // Enviando respuesta al frontend
        return response()->json(["message" => "Lapso Académico creado exitosamente"]);
    }

    /**
     * Display the specified resource. 
     */
    public function show(LapsoAcademico $lapso_academico)
    {
        // Enviando registro al forntend
        return response()->json($lapso_academico);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLapsoAcademicoRequest $request, LapsoAcademico $lapso_academico)
    {
        // Editando Registro
        $lapso_academico->update($request->all());

        return response()->json(["message" => "Lapso Académico editado exitosamente"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LapsoAcademico $lapso_academico)
    {
        try {
            DB::beginTransaction();

            // Eliminando registro
            $lapso_academico->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lapso Académico eliminado exitosamente'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Código de error de restricción de clave foránea en PostgreSQL
            if ($e->getCode() == '23503') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el lapso académico porque tiene registros relacionados',
                    'error_type' => 'foreign_key_constraint'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el lapso académico',
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

    // Obtener los tipos de lapsos
    public function get_tipoLapsos()
    {
        $tipoLapsos = TipoLapso::select('id', 'nombre')->get();

        return response()->json($tipoLapsos); 
    }
}
