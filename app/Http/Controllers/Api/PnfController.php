<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePnfRequest;
use App\Http\Requests\UpdatePnfRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pnf;
use Barryvdh\DomPDF\Facade\Pdf;

class PnfController extends Controller
{
    public function index()
    {
        // Seleccionar los pnf
        $pnf = Pnf::select('id', 'codigo', 'nombre', 'abreviado', 'abreviado_coord')
            ->get();

        // Si no hay registros, devuelve un array vacío para que el frontend lo maneje
        if ($pnf->isEmpty()) {
            return response()->json([], 200);
        }

        // Enviar a la vista del listado de PNF con la variable
        return response()->json($pnf, 200);
    }

    // Metodo para crear un nuevo pnf
    public function store(StorePnfRequest $request)
    {
        try {

            // Crear el PNF con el logo
            $pnf = Pnf::create([
                'codigo' => $request['codigo'],
                'nombre' => $request['nombre'],
                'abreviado' => $request['abreviado'],
                'abreviado_coord' => $request['abreviado_coord'],
                // 'logo' => $logoPath,
            ]);

            // si hay trayectos se asocian al pnf
            if ($request->has('trayectos_id')) {
                $pnf->trayectos()->sync($request->trayectos_id);
            }

            return response()->json([
                "message" => "Pnf creado exitosamente",
                "pnf" => $pnf
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el PNF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pnf $pnf)
    {
        $pnf->load(['trayectos' => function ($query) {
            $query->select('trayecto_id', 'nombre');
        }]);

        return response()->json($pnf, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePnfRequest $request, Pnf $pnf)
    {
        try {
            DB::beginTransaction();

            // Actualizar datos del PNF
            $pnf->update([
                'codigo' => $request['codigo'],
                'nombre' => $request['nombre'],
                'abreviado' => $request['abreviado'],
                'abreviado_coord' => $request['abreviado_coord'],
            ]);

            // asignar trayectos al pnf
            if ($request->has('trayectos_id')) {
                $pnf->trayectos()->sync($request->trayectos_id);
            }

            DB::commit();

            return response()->json([
                'message' => 'Pnf editado exitosamente',
                'pnf' => $pnf
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al actualizar el PNF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /* Remove the specified resource from storage.*/
    public function destroy(Pnf $pnf)
    {
        try {
            DB::beginTransaction();

            // Eliminando pnf
            $pnf->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pnf eliminado exitosamente'
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Código de error de restricción de clave foránea en MySQL
            if ($e->getCode() == '23503') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el PNF porque tiene registros relacionados',
                    'error_type' => 'foreign_key_constraint'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el PNF',
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

    /**
     * Genera el reporte PDF del catálogo de PNF.
     */
    public function generarPdf(Request $request)
    {
        // 1. Usuario autenticado y su PNF (para filtrar por coordinador)
        $user = auth()->user();
        $pnfUser = $user?->pnf;
        $pnfId = $pnfUser?->id;

        // 2. Consulta base
        $query = Pnf::query();

        // 3. Un coordinador solo visualiza su propio PNF
        if ($pnfId && !$user->hasRole('ADMINISTRADOR')) {
            $query->where('id', $pnfId);
        }

        $pnfs = $query->orderBy('nombre')->get();

        // 4. Generar PDF
        $pdf = Pdf::loadView('pdf.pnf', compact('pnfs', 'pnfUser'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('PNFS_' . date('Y_m_d_His') . '.pdf');
    }
}
