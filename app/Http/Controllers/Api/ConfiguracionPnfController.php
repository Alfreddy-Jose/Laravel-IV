<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConfiguracionPnfRequest;
use App\Models\configuracion_pnf;
use Exception;
use Illuminate\Support\Facades\DB;

class ConfiguracionPnfController extends Controller
{
    /* public function show($pnfId)
    {
        // Traemos todos los trayectos asociados al PNF y su configuración actual
        $configuraciones = DB::table('pnf_trayecto')
            ->join('trayactes', 'pnf_trayecto.trayecto_id', '=', 'trayectos.id')
            ->leftJoin('configuracion_pnfs', 'pnf_trayecto.id', '=', 'configuracion_pnfs.pnf_trayecto_id')
            ->where('pnf_trayecto.pnf_id', $pnfId)
            ->select(
                'pnf_trayecto.id as pnf_trayecto_id',
                'trayectos.nombre as trayecto_nombre',
                // Si es null en la BD, la consulta devuelve 35 como valor inicial estándar
                DB::raw('COALESCE(configuracion_pnfs.max_estudiantes_seccion, 35) as max_estudiantes_seccion')
            )
            ->orderBy('trayectos.id', 'asc')
            ->get();

        return response()->json($configuraciones, 200);
    } */

    // Guardar o Actualizar de forma segura con transacciones
    public function store(StoreConfiguracionPnfRequest $request)
    {

        DB::beginTransaction();
        try {
            foreach ($request->configuraciones as $config) {
                // Si ya existe registro para ese pnf_trayecto_id lo actualiza, sino lo crea
                configuracion_pnf::updateOrCreate(
                    ['pnf_trayecto_id' => $config['pnf_trayecto_id']],
                    ['max_estudiantes_seccion' => $config['max_estudiantes_seccion']]
                );
            }

            DB::commit();
            return response()->json(['message' => 'Configuración de secciones actualizada con éxito.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Fallo al guardar la configuración: ' . $e->getMessage()], 500);
        }
    }
}
