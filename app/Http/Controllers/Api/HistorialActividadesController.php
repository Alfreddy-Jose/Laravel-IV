<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ControlEntrega;
use App\Models\HistorialActividades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistorialActividadesController extends Controller
{
    /**
     * OBTENER DATOS PARA LA VISTA (Timeline)
     * Envía las últimas actividades registradas en el sistema al Frontend.
     */
    public function index()
    {
        // Traemos las últimas 10 actividades con sus relaciones anidadas
        // Cargamos: el usuario, el control de entrega, el tipo de documento, el docente y su persona.
        $actividades = HistorialActividades::with([
            'user',
            'controlEntrega.docente.persona',
            'controlEntrega.tipoDocumento'
        ])
        ->latest()
        ->limit(10)
        ->get();

        // Mapeamos los datos para enviarle al React un JSON limpio y fácil de leer
        $timeline = $actividades->map(function ($actividad) {
            // Validaciones de seguridad por si algún registro fue eliminado en cascada
            $usuarioNombre = $actividad->user ? $actividad->user->name : 'Usuario';
            $documento = $actividad->controlEntrega && $actividad->controlEntrega->tipoDocumento
                ? $actividad->controlEntrega->tipoDocumento->nombre
                : 'Documento';

            $docentePersona = $actividad->controlEntrega && $actividad->controlEntrega->docente
                ? $actividad->controlEntrega->docente->persona
                : null;

            $docenteNombre = $docentePersona
                ? $docentePersona->nombre . ' ' . $docentePersona->apellido
                : 'Docente';

            return [
                'id' => $actividad->id,
                'usuario' => $usuarioNombre,
                'accion' => $actividad->accion,
                'documento' => $documento,
                'docente' => $docenteNombre,
                'hace_cuanto' => $actividad->created_at->diffForHumans(),
                'fecha_completa' => $actividad->created_at->toIso8601String()
            ];
        });

        return response()->json($timeline, 200);
    }

    /**
     * GUARDAR Y REGISTRAR ENTREGA + ACTIVIDAD
     * Procesa la petición POST del checkbox de React.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos que vienen del Frontend (con las llaves corregidas)
        $request->validate([
            'entregado' => 'required|boolean',
            'tipo_documento_id' => 'required|integer',
            'docente_id' => 'required|integer',
            'lapso_academico_id' => 'required|integer',
            'seccion_id' => 'nullable|integer',
            'observaciones' => 'nullable|string'
        ]);


        // Si algo falla, no se guarda ni la entrega ni la actividad.
        DB::beginTransaction();

        try {
            // 2. Crear o actualizar el estado del documento en control_entregas
            $controlEntrega = ControlEntrega::updateOrCreate(
                [
                    'lapso_academico_id' => $request->lapso_academico_id,
                    'docente_id' => $request->docente_id,
                    'tipo_documento_id' => $request->tipo_documento_id,
                    'seccion_id' => $request->seccion_id,
                ],
                [
                    'entregado' => $request->entregado,
                    'observaciones' => $request->observaciones
                ]
            );

            // 3. Registrar la acción de manera persistente en la Línea de Tiempo
            HistorialActividades::create([
                'control_entrega_id' => $controlEntrega->id,
                'user_id' => auth()->id() ?? 1, // Usa el usuario logueado, fallback al ID 1 si estás probando sin auth
                'accion' => $request->entregado ? 'marcó' : 'desmarcó',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Control de entrega y actividad registrados correctamente.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar los datos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
