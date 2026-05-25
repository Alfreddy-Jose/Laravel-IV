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
        // Obtener todas las unidades curriculares con sus trimestres y el trayecto,
        // pero el trayecto esta relaciona es con los trimestres
        $unidades = UnidadCurricular::with('trimestres')->get();

        $unidades = $unidades->map(function ($unidad) {
            return [
                'id' => $unidad->id,
                'nombre' => $unidad->nombre,
                'hora_total_est' => $unidad->hora_total_est,
                'hora_practica' => $unidad->hora_practica,
                'periodo' => $unidad->periodo,
                'hora_teorica' => $unidad->hora_teorica,
                'unidad_credito' => $unidad->unidad_credito,
                'descripcion' => $unidad->descripcion,
                'trimestres' => $unidad->trimestres
                    ->map(function ($trimestre) {
                        return [
                            'id' => $trimestre->id,
                            'trayecto_id' => $trimestre->trayecto_id,
                            'nombre' => $trimestre->nombre_relativo,
                            'trayecto' => $trimestre->trayecto->nombre,
                        ];
                    })
            ];
        });

        return response()->json($unidades);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnidadCurricularRequest $request)
    {
        // Guardando el registro
        $unidadCurricular = UnidadCurricular::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'unidad_credito' => $request->unidad_credito,
            'hora_teorica' => $request->hora_teorica,
            'hora_practica' => $request->hora_practica,
            'hora_total_est' => $request->hora_total_est,
            'periodo' => $request->periodo
        ]);

        // Asociar trimestres
        if ($request->periodo === 'trimestral') {
            // Para trimestral: un solo trimestre (número)
            $unidadCurricular->trimestres()->sync([$request->trimestre_id]);
        } else {
            // Para semestral y anual: array de trimestres
            $unidadCurricular->trimestres()->sync($request->trimestre_id);
        }

        return response()->json(['message' => 'Unidad Curricular Registrada']);
    }

    public function exportarPDF()
    {
        $unidades = UnidadCurricular::with('trimestres')->get();

        $pdf = Pdf::loadView('pdf.unidad_curricular', compact('unidades'));

        return $pdf->download('unidades_curriculares.pdf');
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
        // Actualizar la unidad curricular
        $unidad_curricular->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'unidad_credito' => $request->unidad_credito,
            'hora_teorica' => $request->hora_teorica,
            'hora_practica' => $request->hora_practica,
            'hora_total_est' => $request->hora_total_est,
            'periodo' => $request->periodo
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
}
