<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentoRequest;
use App\Models\Documento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DocumentoController extends Controller
{
    /**
     * Lista los documentos. Si el usuario pertenece a un PNF, solo ve los suyos;
     * el administrador puede filtrar por el PNF enviado en la petición.
     */
    public function index(Request $request): JsonResponse
    {
        $pnfId = $this->pnfDelUsuario($request)
            ?? ($request->filled('pnf_id') ? $request->integer('pnf_id') : null);

        $documentos = Documento::with(['user:id,name,email', 'pnf:id,nombre,abreviado'])
            ->when($pnfId, fn ($query) => $query->where('pnf_id', $pnfId))
            ->orderByDesc('id')
            ->get()
            ->map(fn (Documento $documento) => $this->serializar($documento));

        return response()->json([
            'success' => true,
            'data' => $documentos,
        ], 200);
    }

    /**
     * Almacena un documento y lo asocia al PNF correspondiente.
     */
    public function store(StoreDocumentoRequest $request): JsonResponse
    {
        $pnfId = $this->pnfDelUsuario($request) ?? $request->integer('pnf_id');
        $archivo = $request->file('archivo');
        $ruta = null;

        try {
            $ruta = $archivo->store('documentos', 'public');

            $documento = Documento::create([
                'pnf_id' => $pnfId,
                'user_id' => $request->user()?->id,
                'nombre' => $request->input('nombre') ?: $archivo->getClientOriginalName(),
                'archivo' => $ruta,
                'tipo' => $archivo->getClientMimeType(),
                'tamano' => $archivo->getSize(),
            ]);

            $documento->load(['user:id,name,email', 'pnf:id,nombre,abreviado']);

            return response()->json([
                'success' => true,
                'message' => 'Documento subido correctamente.',
                'data' => $this->serializar($documento),
            ], 201);
        } catch (Throwable $e) {
            if ($ruta) {
                Storage::disk('public')->delete($ruta);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo subir el documento.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra el metadato de un documento.
     */
    public function show(Request $request, Documento $documento): JsonResponse
    {
        $this->autorizarPnf($request, $documento);

        $documento->load(['user:id,name,email', 'pnf:id,nombre,abreviado']);

        return response()->json([
            'success' => true,
            'data' => $this->serializar($documento),
        ], 200);
    }

    /**
     * Descarga el archivo original.
     */
    public function descargar(Request $request, Documento $documento)
    {
        $this->autorizarPnf($request, $documento);

        $disk = Storage::disk('public');

        if (! $disk->exists($documento->archivo)) {
            return response()->json([
                'success' => false,
                'message' => 'El archivo no está disponible en el servidor.',
            ], 404);
        }

        return $disk->download($documento->archivo, $documento->nombre);
    }

    /**
     * Elimina el documento y su archivo del almacenamiento.
     */
    public function destroy(Request $request, Documento $documento): JsonResponse
    {
        $this->autorizarPnf($request, $documento);

        Storage::disk('public')->delete($documento->archivo);
        $documento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Documento eliminado correctamente.',
        ], 200);
    }

    /**
     * PNF asignado al usuario autenticado, si posee uno.
     */
    private function pnfDelUsuario(Request $request): ?int
    {
        return $request->user()?->pnf?->id;
    }

    /**
     * Evita que un usuario acceda a documentos de otro PNF.
     */
    private function autorizarPnf(Request $request, Documento $documento): void
    {
        $pnfId = $this->pnfDelUsuario($request);

        abort_if($pnfId && $documento->pnf_id !== $pnfId, 403, 'No tiene acceso a este documento.');
    }

    /**
     * Estructura de respuesta utilizada por el frontend.
     *
     * @return array<string, mixed>
     */
    private function serializar(Documento $documento): array
    {
        return [
            'id' => $documento->id,
            'pnf_id' => $documento->pnf_id,
            'nombre' => $documento->nombre,
            'tipo' => $documento->tipo,
            'extension' => strtolower(pathinfo($documento->nombre, PATHINFO_EXTENSION)),
            'tamano' => $documento->tamano,
            'created_at' => $documento->created_at,
            'user' => $documento->user ? [
                'id' => $documento->user->id,
                'name' => $documento->user->name,
                'email' => $documento->user->email,
            ] : null,
            'pnf' => $documento->pnf ? [
                'id' => $documento->pnf->id,
                'nombre' => $documento->pnf->nombre,
                'abreviado' => $documento->pnf->abreviado,
            ] : null,
        ];
    }
}
