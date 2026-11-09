<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GestionAcademicaService;
use App\Models\ControlEntrega;
use App\Http\Requests\StoreEntregaRequest;
use App\Models\TipoDocumento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\HistorialActividades;

class ControlEntregaController extends Controller
{
    protected $gestionService;

    // Inyectamos el servicio en el constructor
    public function __construct(GestionAcademicaService $gestionService)
    {
        $this->gestionService = $gestionService;
    }

    /**
     * Obtiene el panorama completo de entregas (La combinación inteligente)
     */
    public function index(Request $request): JsonResponse
    {
        // Si el usuario autenticado tiene un PNF asociado, se usa.
        // En caso contrario, se toma el pnf_id enviado desde la vista (request).
        $pnfId = $request->user()->pnf->id ?? $request->input('pnf_id') ?? 1;

        // Obtenemos los documentos activos desde el modelo
        $documentos = TipoDocumento::select('id', 'nombre', 'alcance')->get();

        // 2. Llamamos al servicio obtenerMatrizDashboard
        $docentesMatriz = $this->gestionService->obtenerMatrizDashboard((int) $pnfId);

        // 3. Devolvemos la respuesta formateada para React
        return response()->json([
            'status' => 'success',
            'data' => [
                'documentos' => $documentos,
                'docentes' => $docentesMatriz
            ]
        ], 200);
    }

    /**
     * Registra o actualiza (Toggle) el estado de una entrega
     */
    public function store(StoreEntregaRequest $request): JsonResponse
    {

        $entregadoBool = filter_var($request->entregado, FILTER_VALIDATE_BOOLEAN);

        DB::beginTransaction();

        try {
            // Buscar si ya existe un registro previo
            $registroExistente = ControlEntrega::where([
                'lapso_academico_id' => $request->lapso_academico_id,
                'docente_id' => $request->docente_id,
                'tipo_documento_id' => $request->tipo_documento_id,
                'seccion_id' => $request->seccion_id,
            ])->first();

            $path = $registroExistente ? $registroExistente->archivo_path : null;

            // Lógica de almacenamiento físico del archivo adjunto
            if ($request->hasFile('archivo')) {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
                $path = $request->file('archivo')->store('entregas', 'public');
            } elseif (!$request->entregado && $path) {
                Storage::disk('public')->delete($path);
                $path = null;
            }

            // Operación Idempotente
            $controlEntrega = ControlEntrega::updateOrCreate(
                [
                    'lapso_academico_id' => $request->lapso_academico_id,
                    'docente_id' => $request->docente_id,
                    'tipo_documento_id' => $request->tipo_documento_id,
                    'seccion_id' => $request->seccion_id,
                    'unidad_curricular_id' => $request->unidad_curricular_id,
                ],
                [
                    'entregado' => $request->entregado,
                    'fecha_entrega' => $request->entregado ? now() : null,
                    'observaciones' => $request->observaciones,
                    'archivo_path' => $path
                ]
            );

            // Registrar en el Historial
            $accionTexto = $request->input('accion', $request->entregado ? 'marcó' : 'desmarcó');
            HistorialActividades::create([
                'control_entrega_id' => $controlEntrega->id,
                'user_id' => auth()->id() ?? 1,
                'accion' => $accionTexto,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Recaudos, archivo e historial procesados con éxito.',
                'data' => $controlEntrega
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar los datos académicos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
