<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegistroPerRequest;
use App\Http\Requests\UpdateRegistroPerRequest;
use App\Models\Docente;
use App\Models\RegistroPer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class RegistroPerController extends Controller
{
    /**
     * Listar todos los registros de PER con relaciones y filtros.
     * Útil para las vistas de Control de Estudio y la Coordinación.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $pnfUser = $user?->pnf;
        $pnfId = $pnfUser?->id;

        // Captura el ID del lapso desde query parameters, cabeceras (X-Lapso-Id o lapso_id), o cuerpo
        $lapsoId = $request->query('lapso_id')
                ?? $request->query('lapso_academico_id')
                ?? $request->header('X-Lapso-Id')
                ?? $request->header('lapso_id')
                ?? $request->lapso_academico_id;

        $query = RegistroPer::with([
            'estudiante.persona',
            'unidadCurricular',
            'docente.persona',
            'lapsoAcademico',
        ])
        // 🆕 FILTRO AUTOMÁTICO POR PNF DEL USUARIO (vía docente)
            ->when($pnfId, function ($q) use ($pnfId) {
                return $q->whereHas('docente', function ($sub) use ($pnfId) {
                    $sub->where('pnf_id', $pnfId);
                });
            })
        // Aplica el filtro del lapso de manera dinámica
            ->when($lapsoId, function ($q) use ($lapsoId) {
                return $q->where('lapso_academico_id', $lapsoId);
            })
        // Buscador por Cédula del Estudiante
            ->when($request->filled('cedula'), function ($q) use ($request) {
                return $q->whereHas('estudiante.persona', function ($subQ) use ($request) {
                    $subQ->where('cedula_persona', $request->cedula);
                });
            });

        $registros = $query->latest()->get();

        return response()->json($registros, 200);
    }

    /**
     * Registrar un nuevo PER (Solicitud / Inscripción realizada en Control de Estudio).
     * Las validaciones de límites (máx 3 PER, no Proyecto, no Anual) se ejecutan en StoreRegistroPerRequest.
     */
    public function store(StoreRegistroPerRequest $request)
    {
        // Captura el ID del lapso desde el Header o desde el body (si existiera)
        $lapsoId = $request->header('X-Lapso-Id')
                ?? $request->header('lapso_id')
                ?? $request->lapso_academico_id;

        $registro = RegistroPer::create([
            'estudiante_id' => $request->estudiante_id,
            'unidad_curricular_id' => $request->unidad_curricular_id,
            'docente_id' => $request->docente_id,
            'lapso_academico_id' => $lapsoId,
            'calificacion' => $request->calificacion,
        ]);

        return response()->json([
            'message' => 'PER inscrito exitosamente.',
            'data' => $registro,
        ], 201);
    }

    /**
     * Consultar el detalle de un PER específico por su ID.
     */
    public function show($id)
    {
        $registro = RegistroPer::with([
            'estudiante.persona',
            'unidadCurricular',
            'docente.persona',
            'lapsoAcademico',
        ])->findOrFail($id);

        return response()->json($registro);
    }

    /**
     * Actualizar el Refistro Per
     */
    public function update(UpdateRegistroPerRequest $request, $id)
    {
        // 1. Buscar el registro PER
        $registroPer = RegistroPer::findOrFail($id);

        // 2. Capturar el ID del lapso (Header o Body), si no viene se conserva el actual
        $lapsoId = $request->header('X-Lapso-Id')
                ?? $request->header('lapso_id')
                ?? $request->lapso_academico_id
                ?? $registroPer->lapso_academico_id;

        // 3. Actualizar los campos del registro
        $registroPer->update([
            'estudiante_id' => $request->estudiante_id,
            'unidad_curricular_id' => $request->unidad_curricular_id,
            'docente_id' => $request->docente_id,
            'lapso_academico_id' => $lapsoId,
            'calificacion' => $request->calificacion,
        ]);

        // 4. Retornar la respuesta con el objeto actualizado y cargando sus relaciones
        return response()->json([
            'message' => 'PER actualizado exitosamente.',
            'data' => $registroPer->fresh([
                'estudiante.persona',
                'unidadCurricular',
                'docente.persona',
                'lapsoAcademico',
            ]),
        ], 200);
    }

    /**
     * Eliminar un registro del PER.
     */
    public function destroy($id)
    {
        try {
            // 1. Buscar el registro por su ID
            $registroPer = RegistroPer::findOrFail($id);

            // 2. Eliminar el registro de la base de datos
            $registroPer->delete();

            // 3. Responder con mensaje exitoso
            return response()->json([
                'message' => 'Registro PER eliminado exitosamente.',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El registro PER que intenta eliminar no existe o ya fue removido.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al intentar eliminar el registro PER.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Método para traer todos los docentes según el pnf del Usuario
    public function getDocentes()
    {
        $user = auth()->user();

        // Obtener el pnf_id del usuario (si tiene un PNF asociado)
        $pnfUser = $user->pnf;
        $pnfId = $pnfUser?->id;

        // 1. Consulta para docentes segun el pnf del usuario
        $docentesQuery = Docente::with(['persona:id,cedula_persona,nombre,apellido', 'pnf:id,nombre'])
            ->when($pnfId, function ($q) use ($pnfId) {
                $q->where('pnf_id', $pnfId);
            });

        $docentes = $docentesQuery->get()->map(function ($docente) {
            return [
                'id' => $docente->id,
                'nombre' => 'V-'.$docente->persona->cedula_persona.' - '.$docente->persona->nombre.' '.$docente->persona->apellido,
                'nombre_pnf' => $docente->pnf?->nombre,
                'id_pnf' => $docente->pnf?->id,
            ];
        });

        return response()->json([
            'docentes' => $docentes,
        ], 200);
    }

    // Método para obtener las unidades curriculares segun el Docente Seleccionado
    public function getUnidadesCurricularesPorDocente($docenteId)
    {
        $user = auth()->user();

        // 1. Buscar al docente
        $docente = Docente::select('id', 'pnf_id')->find($docenteId);

        if (! $docente) {
            return response()->json([
                'message' => 'Docente no encontrado',
            ], 404);
        }

        // 2. Validar permisos de PNF si no es ADMINISTRADOR
        if (! $user->hasRole('ADMINISTRADOR')) {
            $pnfId = $user->pnf?->id;

            if (! $pnfId || $docente->pnf_id !== $pnfId) {
                return response()->json([
                    'message' => 'No tiene permisos para consultar las unidades de este docente',
                ], 403);
            }
        }

        // 3. Calificar 'unidad_curriculars.id' para resolver la ambigüedad en PostgreSQL
        $unidadesCurriculares = $docente->unidades_curriculares()
            ->select('unidad_curriculars.id', 'nombre')
            ->get();

        return response()->json([
            'unidadesCurriculares' => $unidadesCurriculares,
        ], 200);
    }

    /**
     * Genera el reporte PDF de los registros PER.
     */
    public function generarPdf(Request $request)
    {
        // 1. Obtener lapso_id (por query, headers o input)
        $lapsoId = $request->query('lapso_id')
                ?? $request->query('lapso_academico_id')
                ?? $request->header('X-Lapso-Id')
                ?? $request->header('lapso_id');

        // 2. Usuario autenticado y su PNF (para aplicar filtro institucional si aplica)
        $user = auth()->user();
        $pnfUser = $user?->pnf;
        $pnfId = $pnfUser?->id;

        // 3. Construir consulta con sus relaciones
        $query = RegistroPer::with([
            'estudiante.persona',
            'unidadCurricular',
            'docente.persona',
            'lapsoAcademico',
        ])
            ->when($lapsoId, function ($q) use ($lapsoId) {
                return $q->where('lapso_academico_id', $lapsoId);
            });

        // Opcional: Filtrar por PNF a través de las unidades curriculares o docentes si tu estructura lo requiere,
        // o traer todos si es administrador general. (Se evalúa si el docente pertenece al pnf del usuario)
        if ($pnfId && ! $user->hasRole('ADMINISTRADOR')) {
            $query->whereHas('docente', function ($q) use ($pnfId) {
                $q->where('pnf_id', $pnfId);
            });
        }

        $registros = $query->latest()->get();

        // 4. Renderizar vista en PDF con Dompdf
        $pdf = Pdf::loadView('pdf.per', compact('registros', 'pnfUser'));

        $nombreArchivo = 'REPORTE_PER_'.date('Y_m_d_His').'.pdf';

        return $pdf->download($nombreArchivo);
    }
}
