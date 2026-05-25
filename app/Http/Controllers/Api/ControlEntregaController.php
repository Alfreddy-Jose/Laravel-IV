<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GestionAcademicaService;
use App\Models\ControlEntrega;
use App\Http\Requests\StoreEntregaRequest;
use App\Models\TipoDocumento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $pnfId = $request->user()->pnf_id ?? 1; // Ajusta según tu lógica de Auth
        $lapsoId = $request->lapso_id ?? 1; // Ajusta para recibir el lapso activo

        // Obtenemos los documentos activos desde el modelo
        $documentos = TipoDocumento::select('id', 'nombre', 'alcance')->get();

        // 2. LAS FILAS Y LOS CHECKS: Llamamos al servicio que hace toda la magia
        $docentesMatriz = $this->gestionService->obtenerMatrizDashboard($lapsoId, $pnfId);

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
        // Aplicamos el updateOrCreate (Idempotencia)
        $entrega = ControlEntrega::updateOrCreate(
            [
                'docente_id' => $request->docente_id,
                'lapso_academico_id' => $request->lapso_academico_id,
                'tipo_documento_id' => $request->tipo_documento_id,
                'seccion_id' => $request->seccion_id,
            ],
            [
                'entregado' => $request->entregado,
                'fecha_entrega' => $request->entregado ? now() : null,
                'observaciones' => $request->observaciones
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Control de entrega actualizado correctamente.',
            'data' => $entrega
        ], 200);
    }

}
