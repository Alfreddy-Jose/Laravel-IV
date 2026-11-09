<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\UnidadCurricular;
use App\Models\Pnf;
use App\Exports\CensosExport;
use App\Http\Requests\StoreCensoRequest;
use App\Models\LapsoAcademico;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Censo;

class CensoController extends Controller
{
    /**
     * Listar todos los censos del lapso académico activo actual.
     * Carga las relaciones necesarias para mostrar en la DataTable de React.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Obtener el pnf_id del usuario autenticado
        $pnfUser = $user->pnf;
        $pnfId = $pnfUser?->id;

        // Captura el ID del lapso desde query parameters, cuerpo, o cabecera
        $lapsoId = $request->query('lapso_id') ?? $request->header('X-Lapso-Id') ?? $request->lapso_id;

        $censos = Censo::with([
            'estudiante.persona',
            'estudiante.secciones' => function ($query) use ($pnfId) {
                if ($pnfId) {
                    $query->where('seccions.pnf_id', $pnfId);
                }
            }
        ])
        // 1. Filtrar por PNF del usuario (vía Estudiante -> Secciones)
        ->when($pnfId, function ($query) use ($pnfId) {
            $query->whereHas('estudiante.secciones', function ($q) use ($pnfId) {
                $q->where('seccions.pnf_id', $pnfId);
            });
        })
        // 2. Aplica el filtro del lapso de manera dinámica
        ->when($lapsoId, function ($query) use ($lapsoId) {
            return $query->where('lapso_id', $lapsoId);
        })
        ->latest()
        ->get();

        // 2. Extraemos todos los IDs de asignaturas pendientes
        $asignaturasIds = $censos->pluck('detalles.asignaturas_pendientes')
            ->flatten()
            ->filter()
            ->unique()
            ->toArray();

        // 3. Consultamos los nombres de esas unidades curriculares
        $unidadesCurriculares = method_exists(UnidadCurricular::class, 'query')
            ? UnidadCurricular::whereIn('id', $asignaturasIds)->get(['id', 'nombre'])->keyBy('id')
            : collect();

        // 4. Inyectamos los nombres estructurados
        $censos->transform(function ($censo) use ($unidadesCurriculares) {
            if ($censo->tipo_censo === 'ACADEMICO' && isset($censo->detalles['asignaturas_pendientes'])) {

                $detallesActuales = $censo->detalles;

                $detallesActuales['asignaturas_detalladas'] = collect($censo->detalles['asignaturas_pendientes'])
                    ->map(function ($id) use ($unidadesCurriculares) {
                        $unidad = $unidadesCurriculares->get($id);
                        return [
                            'id' => $id,
                            'nombre' => $unidad ? $unidad->nombre : 'UNIDAD NO ENCONTRADA'
                        ];
                    })->all();

                $censo->detalles = $detallesActuales;
            }
            return $censo;
        });

        return response()->json($censos, 200);
    }

    /**
     * Registrar una nueva ficha de censo.
     * Absorbe el objeto de Formik directamente en la columna JSONB.
     */
    public function store(StoreCensoRequest $request)
    {
        // Si llega aquí, la data ya está validada y es segura
        $validated = $request->validated();

        // Regla de Negocio: Evitar duplicados del mismo tipo de censo para un estudiante en el mismo lapso
        $existeCenso = Censo::where('estudiante_id', $validated['estudiante_id'])
            ->where('lapso_id', $validated['lapso_id'])
            ->where('tipo_censo', $validated['tipo_censo'])
            ->exists();

        if ($existeCenso) {
            return response()->json([
                'message' => 'El estudiante ya posee un registro activo para este tipo de censo en el lapso actual.'
            ], 422);
        }

        $censo = Censo::create($validated);

        return response()->json([
            'message' => 'Censo estudiantil registrado con éxito',
            'censo'   => $censo->load('estudiante.persona')
        ], 201);
    }

    /**
     * Mostrar los detalles de un censo en específico.
     */
    public function show($id)
    {
        $censo = Censo::with(['estudiante.persona', 'estudiante.secciones', 'lapso'])->findOrFail($id);
        return response()->json($censo, 200);
    }

    /**
     * Actualizar una ficha de censo (Modificar el JSON interno).
     */
    public function update(StoreCensoRequest $request, $id)
    {
        $censo = Censo::findOrFail($id);
        $validated = $request->validated();

        $existeDuplicado = Censo::where('estudiante_id', $censo->estudiante_id)
            ->where('lapso_id', $censo->lapso_id)
            ->where('tipo_censo', $validated['tipo_censo'])
            ->where('id', '!=', $id)
            ->exists();

        if ($existeDuplicado) {
            return response()->json([
                'message' => 'Ya existe otra ficha de este tipo de censo para el alumno en este periodo.'
            ], 422);
        }

        $censo->update($validated);

        return response()->json([
            'message' => 'Ficha de censo actualizada correctamente',
            'censo'   => $censo
        ], 200);
    }

    /**
     * Eliminar una ficha de censo del sistema.
     */
    public function destroy($censo)
    {
        $censoE = Censo::findOrFail($censo);

        $censoE->delete();

        return response()->json([
            'message' => 'Ficha de censo eliminada correctamente',
        ], 200);
    }

    // =========================================================================
    // MÉTODOS DE FILTRADO AVANZADO (PROCESAMIENTO DE REPORTES PARA EL COORDINADOR)
    // =========================================================================

    /**
     * Reporte por Sección: Retorna los censados de una sección específica (vía voceros)
     */
    public function reportePorSeccion(Request $request, $seccionId)
    {
        $lapsoId = $request->header('X-Lapso-Id') ?? $request->lapso_id;

        $censos = Censo::with(['estudiante.persona'])
            ->deLaSeccion($seccionId)
            ->when($lapsoId, function ($query) use ($lapsoId) {
                return $query->delLapso($lapsoId);
            })
            ->get();

        return response()->json($censos, 200);
    }

    /**
     * Reporte de Políticas Estudiantiles: Extrae subcategorías específicas del JSONB
     * Filtra dinámicamente si el coordinador solicita: deportistas, embarazadas, etc.
     */
    public function reportePoliticasEstudiantiles(Request $request)
    {
        $request->validate([
            'categoria' => 'required|string|in:deportista,musico,embarazada,discapacidad'
        ]);

        $categoria = $request->query('categoria');
        $lapsoId = $request->header('X-Lapso-Id') ?? $request->lapso_id;

        // Utilizamos los Query Scopes dinámicos sobre el campo JSONB
        $censos = Censo::with(['estudiante.persona', 'estudiante.secciones'])
            ->porCategoria($categoria)
            ->when($lapsoId, function ($query) use ($lapsoId) {
                return $query->delLapso($lapsoId);
            })
            ->get();

        return response()->json($censos, 200);
    }

    public function getEstudiantes(Request $request)
    {
        $user = auth()->user();

        // Obtener el pnf_id del usuario autenticado
        $pnfUser = $user->pnf;
        $pnfId = $pnfUser?->id;

        // Captura el ID del lapso desde query parameters, cuerpo, o cabecera
        $lapsoId = $request->query('lapso_id') ?? $request->header('X-Lapso-Id') ?? $request->lapso_id;

        $estudiantes = Estudiante::with([
            'persona:id,nombre,apellido,cedula_persona',

            // 1. Cargar las secciones filtradas por lapso y PNF (si aplica)
            'secciones' => function ($query) use ($lapsoId, $pnfId) {
                $query->select('seccions.id', 'seccions.nombre', 'seccions.pnf_id', 'seccions.lapso_id');
                if ($lapsoId) {
                    $query->where('seccions.lapso_id', $lapsoId);
                }
                if ($pnfId) {
                    $query->where('seccions.pnf_id', $pnfId);
                }
            },

            // 2. Cargar el PNF de la sección
            'secciones.pnf' => function ($query) {
                $query->select('pnfs.id', 'pnfs.nombre');
            }
        ])
        // 3. Filtrar para retornar ÚNICAMENTE a los estudiantes del PNF del usuario (vía Seccion)
        ->when($pnfId, function ($query) use ($pnfId) {
            $query->whereHas('secciones', function ($q) use ($pnfId) {
                $q->where('seccions.pnf_id', $pnfId);
            });
        })
        // 4. Filtrar para retornar ÚNICAMENTE a los estudiantes inscritos en alguna sección de ese lapso
        ->when($lapsoId, function ($query) use ($lapsoId) {
            $query->whereHas('secciones', function ($q) use ($lapsoId) {
                $q->where('seccions.lapso_id', $lapsoId);
            });
        })
        ->get();

        return response()->json($estudiantes, 200);
    }

    /**
     * Exporta el censo total de bienestar a Excel con formato institucional.
     */
    public function exportarExcel(Request $request)
    {
        $lapsoId = $request->query('lapso_id');
        $categoria = $request->query('categoria');
        $seccionId = $request->query('seccion_id');

        // Capturamos el tipo de censo (Por defecto: POLITICAS ESTUDIANTILES)
        $tipoCenso = $request->query('tipo_censo', 'POLITICAS ESTUDIANTILES');

        $lapso = LapsoAcademico::find($lapsoId);
        $lapsoNombre = $lapso ? $lapso->nombre_lapso : '';
        $lapsoNombreSeguro = str_replace('-', '_', (string) $lapsoNombre);

        // Construimos la consulta base según el tipo seleccionado
        $query = Censo::with([
            'estudiante.persona.municipio',
            'estudiante.secciones'
        ])
        ->where('tipo_censo', $tipoCenso);

        if ($lapsoId) {
            $query->where('lapso_id', $lapsoId);
        }

        if ($categoria) {
            $categoriaBD = strtoupper($categoria);
            $query->where('detalles->categoria', $categoriaBD);
        }

        if ($seccionId) {
            $query->whereHas('estudiante.secciones', function ($q) use ($seccionId) {
                $q->where('seccions.id', $seccionId);
            });
        }

        $censos = $query->get();

        // Si es académico, inyectamos los nombres de las asignaturas detalladas exactamente igual que en el index()
        if ($tipoCenso === 'ACADEMICO') {
            $asignaturasIds = $censos->pluck('detalles.asignaturas_pendientes')->flatten()->filter()->unique()->toArray();
            $unidadesCurriculares = \App\Models\UnidadCurricular::whereIn('id', $asignaturasIds)->get(['id', 'nombre'])->keyBy('id');

            $censos->transform(function ($censo) use ($unidadesCurriculares) {
                if (isset($censo->detalles['asignaturas_pendientes'])) {
                    $detallesActuales = $censo->detalles;
                    $detallesActuales['asignaturas_detalladas'] = collect($censo->detalles['asignaturas_pendientes'])
                        ->map(function ($id) use ($unidadesCurriculares) {
                            return [
                                'id' => $id,
                                'nombre' => $unidadesCurriculares->get($id)?->nombre ?? 'UNIDAD NO ENCONTRADA'
                            ];
                        })->all();
                    $censo->detalles = $detallesActuales;
                }
                return $censo;
            });
        }

        $censosArray = $censos->toArray();

        // --- OBTENCIÓN DINÁMICA DE DATOS DEL USUARIO ---
        $user = auth()->user();

        // Verificamos si es coordinador basándonos en la estructura de tu JSON
        $esCoordinador = $user && collect($user->roles)->contains('name', 'COORDINADOR');

        if ($esCoordinador) {
            // Se extrae el nombre abreviado del PNF y el nombre del coordinador en mayúsculas
            $pnfNombre = $user->pnf['abreviado'] ?? 'PNFI';
            $coordinador = strtoupper($user->name);
            $prefijoArchivo = $pnfNombre;
        } else {
            // Valores por defecto para ADMINISTRADOR o roles globales sin PNF asignado
            $pnfNombre = '';
            $coordinador = '';
            $prefijoArchivo = 'GENERAL';
        }
        // -----------------------------------------------

        // Si es académico, exportamos con el nuevo archivo de exportación
        if ($tipoCenso === 'ACADEMICO') {
            $nombreArchivo = "CENSO_ACADEMICO_" . $prefijoArchivo . "_" . $lapsoNombreSeguro . ".xlsx";
            return Excel::download(
                new \App\Exports\CensosAcademicoExport($censosArray, $lapsoNombre, $pnfNombre, $coordinador),
                $nombreArchivo
            );
        }

        // Si es políticas estudiantiles, seguimos usando el original
        $sufijoFiltro = $categoria ? "_" . strtoupper($categoria) : "";
        $nombreArchivo = "CENSO" . $sufijoFiltro . "_BIENESTAR_" . $prefijoArchivo . "_" . $lapsoNombreSeguro . ".xlsx";

        return Excel::download(
            new CensosExport($censosArray, $lapsoNombre, $pnfNombre, $coordinador),
            $nombreArchivo
        );
    }
}
