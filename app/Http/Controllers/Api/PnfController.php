<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePnfRequest;
use App\Http\Requests\UpdatePnfRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pnf;

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

    public function store(StorePnfRequest $request)
    {
        try {
            // Procesar el logo si existe
            // $logoPath = null;
            // if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            //     $logoPath = $request->file('logo')->store('pnf_logos', 'public');
            // }

            // Crear el PNF con el logo
            $pnf = Pnf::create([
                'codigo' => $request['codigo'],
                'nombre' => $request['nombre'],
                'abreviado' => $request['abreviado'],
                'abreviado_coord' => $request['abreviado_coord'],
                // 'logo' => $logoPath,
            ]);

            return response()->json([
                "message" => "PNF creado exitosamente",
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
        return response()->json($pnf, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePnfRequest $request, Pnf $pnf)
    {
        try {
            DB::beginTransaction();

            // Procesar eliminación del logo
/*            if ($request->has('remove_logo') && $request->remove_logo == '1') {
                // Eliminar logo existente
                if ($pnf->logo) {
                    Storage::disk('public')->delete($pnf->logo);
                    $pnf->logo = null;
                }
            } 
            // Procesar nuevo logo
            elseif ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                // Eliminar logo anterior si existe
                if ($pnf->logo) {
                    Storage::disk('public')->delete($pnf->logo);
                }
                
                // Guardar nuevo logo
                $logoPath = $request->file('logo')->store('pnf_logos', 'public');
                $pnf->logo = $logoPath;
            }*/

            // Actualizar datos del PNF
            $pnf->update([
                'codigo' => $request['codigo'],
                'nombre' => $request['nombre'],
                'abreviado' => $request['abreviado'],
                'abreviado_coord' => $request['abreviado_coord'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'PNF editado exitosamente',
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
                'message' => 'PNF eliminado exitosamente'
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
}
