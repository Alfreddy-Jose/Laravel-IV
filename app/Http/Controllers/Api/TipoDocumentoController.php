<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumento;
use App\Http\Requests\StoreTipoDocumentoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TipoDocumentoController extends Controller
{
    /**
     * Listar todos los tipos de documentos registrados.
     */
    public function index(): JsonResponse
    {
        $documentos = TipoDocumento::select('id', 'nombre', 'alcance')->get();

        return response()->json([
            'status' => 'success',
            'data' => $documentos
        ], 200);
    }

    /**
     * Registrar un nuevo tipo de documento.
     */
    public function store(StoreTipoDocumentoRequest $request): JsonResponse
    {
        $documento = TipoDocumento::create([
            'nombre' => $request->nombre,
            'alcance' => $request->alcance,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tipo de documento registrado exitosamente.',
            'data' => $documento
        ], 201);
    }

    /**
     * Eliminar un tipo de documento.
     */
    public function destroy($id): JsonResponse
    {
        // Buscamos el documento
        $documento = TipoDocumento::find($id);

        if (!$documento) {
            return response()->json([
                'status' => 'error',
                'message' => 'El tipo de documento no existe.'
            ], 404);
        }

        // Usamos una transacción para asegurar consistencia
        DB::transaction(function () use ($id, $documento) {
            // 1. Limpiamos cualquier registro de entrega asociado a este tipo de documento
            DB::table('control_entregas')->where('tipo_documento_id', $id)->delete();

            // 2. Eliminamos el tipo de documento
            $documento->delete();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Documento eliminado y registros actualizados correctamente.'
        ], 200);
    }
}
