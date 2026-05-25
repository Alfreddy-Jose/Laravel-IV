<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSeccionRequest;
use App\Http\Requests\UpdateSeccionRequest;
use App\Models\LapsoAcademico;
use App\Models\Matricula;
use App\Models\Pnf;
use App\Models\Seccion;
use App\Models\Sede;
use App\Models\Trayecto;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Seccion::query();

        // Filtrar por lapso
        if ($request->lapso) {
            $query->where('lapso_id', $request->lapso);
        }
        // Filtrar por sede
        if ($request->sede) {
            $query->where('sede_id', $request->sede);
        }
        // Filtrar por pnf
        if ($request->pnf) {
            $query->where('pnf_id', $request->pnf);
        }
        // Filtrar por trayecto
        if ($request->trayecto) {
            $query->where('trayecto_id', $request->trayecto);
        }
        // Filtrar por tipo de matrícula
        if ($request->matricula) {
            $query->where('matricula_id', $request->matricula);
        }

        // Relaciona los datos para la tabla
        $secciones = $query->with(['pnf', 'matricula', 'trayecto', 'sede', 'lapso'])->get();

        return response()->json($secciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSeccionRequest $request)
    {
        // Buscar los datos relacionados usando los IDs recibidos
        $pnf = Pnf::findOrFail($request->pnf_id); // Para obtener el id (ya lo tienes)
        $matricula = Matricula::findOrFail($request->matricula_id); // Para obtener el numero
        $trayecto = Trayecto::findOrFail($request->trayecto_id); // Para obtener el nombre
        $sede = Sede::findOrFail($request->sede_id); // Para obtener el nro_sede

        /* Generar numero de la seccion dependiendo del trayecto y la sede
            si no existe una seccion para ese trayecto, pnf, sede y lapso, se crea la primera seccion
            si existe una seccion para ese trayecto, pnf, sede y lapso, se incrementa el numero de la seccion
        */
        $secciones = Seccion::where('trayecto_id', $request->trayecto_id)
            ->where('sede_id', $request->sede_id)
            ->where('lapso_id', $request->lapso_id)
            ->where('pnf_id', $request->pnf_id)
            ->get();

        if ($secciones->isEmpty()) {
            $numero_seccion = 1;
        } else {
            $ultima_seccion = $secciones->last();
            $numero_seccion = $ultima_seccion->numero_seccion + 1;
        }

        // numero de seccion compuesto con sede
        $numero_final = $sede->nro_sede + $numero_seccion;

        // Construir el nombre de la sección
        $nombre = $pnf->codigo . '' . $matricula->numero . '' . $trayecto->nombre . '' . $numero_final;

        // Crear la sección
        $seccion = new Seccion();
        $seccion->pnf_id = $request->pnf_id;
        $seccion->matricula_id = $request->matricula_id;
        $seccion->trayecto_id = $request->trayecto_id;
        $seccion->sede_id = $request->sede_id;
        $seccion->lapso_id = $request->lapso_id;
        $seccion->numero_seccion = $numero_seccion;
        $seccion->nombre = $nombre;
        $seccion->save();

        return response()->json(['message' => 'Sección Registrada'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Seccion $seccion)
    {
        return response()->json($seccion);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateSeccionRequest $request, Seccion $seccion)
    {

        // actualizando la sección
        $seccion->update(
            [
                //"lapso_id" => $request->lapso_id,
                "pnf_id" => $request->pnf_id,
                "matricula_id" => $request->matricula_id,
                "trayecto_id" => $request->trayecto_id,
                "sede_id" => $request->sede_id,
            ]
        );
        $pnf = Pnf::findOrFail($request->pnf_id);
        $matricula = Matricula::findOrFail($request->matricula_id);
        $trayecto = Trayecto::findOrFail($request->trayecto_id);
        $sede = Sede::findOrFail($request->sede_id);

        /* Generar numero de la seccion dependiendo del trayecto y sede, pnf y lapso
            si no existe una seccion para ese trayecto, pnf, sede y lapso, se crea la primera seccion
            si existe una seccion para ese trayecto, pnf, sede y lapso, se incrementa el numero de la seccion
        */
        $secciones = Seccion::where('trayecto_id', $request->trayecto_id)
            ->where('sede_id', $request->sede_id)
            ->where('lapso_id', $request->lapso_id)
            ->where('pnf_id', $request->pnf_id)
            ->where('id', '!=', $seccion->id)
            ->orderBy('numero_seccion', 'desc')
            ->get();

        if ($secciones->isEmpty()) {
            $numero_seccion = 1;
        } else {
            $ultima_seccion = $secciones->first();
            $numero_seccion = $ultima_seccion->numero_seccion + 1;
        }

        // numero de seccion compuesto con sede
        $numero_final = $sede->nro_sede + $numero_seccion;

        // Construir el nombre de la sección
        $nombre = $pnf->codigo . $matricula->numero . $trayecto->nombre . $numero_final;

        // actualizando el nombre
        $seccion->nombre = $nombre;
        $seccion->numero_seccion = $numero_seccion;
        $seccion->save();

        return response()->json(["message" => "Sección Editada"], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seccion $seccion)
    {
        try {
            DB::beginTransaction();

            // Eliminando registro
            $seccion->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sección Eliminada'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Código de error de restricción de clave foránea en PostgreSQL
            if ($e->getCode() == '23503') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la sección porque tiene registros relacionados',
                    'error_type' => 'foreign_key_constraint'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la sección',
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
        $trayectos = Trayecto::select('id', 'nombre')->get();
        $tipo_matricula = Matricula::select('id', 'nombre', 'numero')->get();
        $lapsos = LapsoAcademico::select('id', 'nombre_lapso', 'ano')->get();
        $sedes = Sede::select('id', 'nombre_sede')->get();
        $pnf = Pnf::select('id', 'nombre')->get();

        return response()->json([
            "trayectos" => $trayectos,
            "tipo_matricula" => $tipo_matricula,
            "lapsos" => $lapsos,
            "sedes" => $sedes,
            "pnfs" => $pnf,
        ]);
    }

        /**
     * Obtener Pnfs basados en sede seleccionada
     */
    public function getPnfs($sedeId)
    {
        $sede = Sede::findOrFail($sedeId);


        $pnfs = $sede->pnfs()->get(['pnfs.id', 'pnfs.nombre', 'pnfs.abreviado']);


        return response()->json($pnfs);
    }

    // Metodo para Genera PDF
    public function pdf(Request $request)
    {
        // Puedes recibir parámetros para filtrar igual que en index
        $query = Seccion::query();

        if ($request->lapso) {
            $query->where('lapso_id', $request->lapso);
        }
        if ($request->sede) {
            $query->where('sede_id', $request->sede);
        }
        if ($request->pnf) {
            $query->where('pnf_id', $request->pnf);
        }
        if ($request->trayecto) {
            $query->where('trayecto_id', $request->trayecto);
        }
        if ($request->matricula) {
            $query->where('matricula_id', $request->matricula);
        }

        $secciones = $query->with(['pnf', 'matricula', 'trayecto', 'sede', 'lapso'])->get();

        // Renderiza una vista blade para el PDF
        $pdf = Pdf::loadView('pdf.secciones', compact('secciones'));

        // Descarga el PDF
        //return $pdf->download('secciones.pdf');


        // Descargar con el nombre personalizado
        return $pdf->stream("Secciones_" . $request->lapso . "pdf", [ 
            'Attachment' => true // Fuerza la descarga
        ]);
    }
}
