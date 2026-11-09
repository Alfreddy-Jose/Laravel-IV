<?php

namespace App\Http\Controllers\Api;

use App\Exports\PlantillaEstadisticaExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\EstadisticaInscripcionImportRequest;
use App\Imports\EstadisticaInscripcionImport;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\DividirSeccionRequest;
use App\Http\Requests\FusionarSeccionRequest;
use App\Models\EstadisticaInscripcion;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

class EstadisticaInscripcionController extends Controller
{
    /**
     * Procesa la importación masiva del archivo Excel de Control de Estudio.
     */

    private function obtenerNombreSede(string $codigoSeccion): string
    {
        $codigoPadded   = str_pad($codigoSeccion, 7, '0', STR_PAD_LEFT);
        $tresCifras     = (int) substr($codigoPadded, 4, 3);
        $codigoSedeBase = (int) (floor($tresCifras / 100) * 100);

        $sedeModel = Sede::where('nro_sede', $codigoSedeBase)
            ->first();

        return $sedeModel ? ($sedeModel->nombre ?? $sedeModel->nombre_sede ?? 'Sede Principal') : 'Sede Principal';
    }

    /**
     * Obtiene el listado de secciones con filtros opcionales.
     */
    public function index(Request $request)
    {
        // NOTA: Se quitó 'sede' del with() porque no es una relación de Eloquent
        $query = EstadisticaInscripcion::with(['unidadCurricular']);

        if ($request->filled('lapso_id')) {
            $query->where('lapso_id', $request->lapso_id);
        }

        if ($request->filled('pnf_id')) {
            $query->where('pnf_id', $request->pnf_id);
        }

        $secciones = $query->get()->map(function ($reg) {
            // Calculamos la sede dinámicamente para cada registro usando su código
            $nombreSede = $this->obtenerNombreSede($reg->seccion);

            return [
                'id'                   => $reg->id,
                'codigo'               => $reg->seccion,
                'nombre'               => "Sección {$reg->seccion}",
                'sede'                 => $nombreSede,
                'unidad_curricular'    => $reg->unidadCurricular?->nombre ?? $reg->unidadCurricular?->nombre_uc ?? 'N/A',
                'trayecto'             => $reg->unidadCurricular?->trayecto ?? 'Trayecto I',
                'matricula'            => $reg->cantidad_estudiantes,
                'unidad_curricular_id' => $reg->unidad_curricular_id,
                'pnf_id'               => $reg->pnf_id,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $secciones,
        ], 200);
    }

    public function importar(EstadisticaInscripcionImportRequest $request)
    {

        $confirmar = $request->boolean('confirmar_reemplazo', false);
        $archivo = $request->file('archivo');

        // 1. Verificar si ya existen registros cargados para este lapso y PNF
        $existenRegistros = EstadisticaInscripcion::where('lapso_id', $request->input('lapso_id'))
            ->exists();

        if ($existenRegistros && !$confirmar) {
            return response()->json([
                'success' => false,
                'requiere_confirmacion' => true,
                'message' => 'Ya existen secciones registradas para este lapso. Si importas un nuevo archivo, se sobrescribirán todos los datos actuales (incluyendo divisiones o fusiones realizadas). ¿Deseas continuar?'
            ], 409); // Código 409 Conflict
        }

        if (!$archivo || !$archivo->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'No se recibió un archivo válido bajo el parámetro "archivo".'
            ], 422);
        }

        try {

            // 1. Detectar la extensión original del archivo subido
            $extension = strtolower($request->file('archivo')->getClientOriginalExtension());

            // 2. Mapear la extensión con el tipo de lector de Maatwebsite
            $tipoLector = match ($extension) {
                'csv'   => ExcelFormat::CSV,
                'xls'   => ExcelFormat::XLS,
                default => ExcelFormat::XLSX,
            };

            EstadisticaInscripcion::where('lapso_id', $request->input('lapso_id'))
            ->delete();


            // 2. Instanciar la importación pasándole el lapso académico
            $importador = new EstadisticaInscripcionImport($request->input('lapso_id'));

            // 3. Ejecutar la lectura del Excel
            Excel::import($importador, $request->file('archivo'), null, $tipoLector);

            $resumen = $importador->getResumen();

            // 4. Retornar respuesta exitosa con resumen de registros
            return response()->json([
                'success' => true,
                'message' => "Carga masiva completada exitosamente. Registros procesados: {$resumen['procesados']}.",
                'detalles' => [
                    'procesados' => $resumen['procesados'],
                    'secciones' => $resumen['secciones'],
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el archivo Excel.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function descargarPlantilla()
    {
        $nombreArchivo = 'Plantilla_Estadistica_Inscripcion.xlsx';

        return Excel::download(new PlantillaEstadisticaExport(), $nombreArchivo);
    }

    public function fusionarSeccion(FusionarSeccionRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $origen  = EstadisticaInscripcion::findOrFail($request->seccion_origen_id);
            $destino = EstadisticaInscripcion::findOrFail($request->seccion_destino_id);

            // Validación: Pertenecer a la misma materia, lapso y sede
            if (
                $origen->unidad_curricular_id !== $destino->unidad_curricular_id ||
                $origen->lapso_id !== $destino->lapso_id ||
                $origen->sede_id !== $destino->sede_id
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las secciones a fusionar deben pertenecer a la misma unidad curricular, lapso académico y sede.'
                ], 422);
            }

            $sumaTotal = $origen->cantidad_estudiantes + $destino->cantidad_estudiantes;

            // Validación: Límite máximo de capacidad
            if ($sumaTotal > 45) {
                return response()->json([
                    'success' => false,
                    'message' => "La fusión excede la capacidad máxima permitida (Suma: {$sumaTotal}, Máximo: 45)."
                ], 422);
            }

            // 1. Sumar matrícula en sección destino
            $destino->update(['cantidad_estudiantes' => $sumaTotal]);

            // 2. Eliminar sección origen de las estadísticas
            $codigoOrigen = $origen->seccion;
            $origen->delete();

            return response()->json([
                'success' => true,
                'message' => "Sección '{$codigoOrigen}' fusionada exitosamente en la sección '{$destino->seccion}'. Matrícula resultante: {$sumaTotal}.",
                'data'    => $destino
            ], 200);
        });
    }


    public function dividirSeccion(DividirSeccionRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $origen = EstadisticaInscripcion::findOrFail($request->seccion_origen_id);
            $mover  = (int) $request->estudiantes_a_mover;

            // Validar que la sección origen tenga suficientes estudiantes
            if ($origen->cantidad_estudiantes < $mover) {
                return response()->json([
                    'success' => false,
                    'message' => "No se pueden mover {$mover} estudiantes. La sección origen solo tiene {$origen->cantidad_estudiantes}."
                ], 422);
            }

            // OPCIÓN A: Transferir a sección existente
            if ($request->tipo_accion === 'transferir') {
                $destino = EstadisticaInscripcion::findOrFail($request->seccion_destino_id);

                // Validar misma unidad curricular, lapso y sede
                if (
                    $origen->unidad_curricular_id !== $destino->unidad_curricular_id ||
                    $origen->lapso_id !== $destino->lapso_id ||
                    $origen->sede_id !== $destino->sede_id
                ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La sección destino debe pertenecer a la misma unidad curricular, lapso académico y sede.'
                    ], 422);
                }

                // Descuenta de origen y aumenta en destino
                $origen->decrement('cantidad_estudiantes', $mover);
                $destino->increment('cantidad_estudiantes', $mover);

                return response()->json([
                    'success' => true,
                    'message' => "Se transfirieron {$mover} estudiantes a la sección '{$destino->seccion}'.",
                    'data'    => [
                        'origen'  => $origen->fresh(),
                        'destino' => $destino->fresh()
                    ]
                ], 200);
            }

            // OPCIÓN B: Crear una nueva sección
            if ($request->tipo_accion === 'crear_nueva') {

                // 1. Validar si la sección ya existe para este lapso y unidad curricular
                $seccionExiste = EstadisticaInscripcion::where('lapso_id', $origen->lapso_id)
                    ->where('unidad_curricular_id', $origen->unidad_curricular_id)
                    ->where('seccion', $request->nuevo_codigo)
                    ->exists();

                if ($seccionExiste) {
                    return response()->json([
                        'success' => false,
                        'message' => "La sección '{$request->nuevo_codigo}' ya existe para esta unidad curricular en el lapso actual. Por favor, elija otro código."
                    ], 422);
                }

                // Descuenta de la sección origen
                $origen->decrement('cantidad_estudiantes', $mover);

                // Crea la nueva sección con los estudiantes asignados
                $nuevaSeccion = EstadisticaInscripcion::create([
                    'lapso_id'             => $origen->lapso_id,
                    'unidad_curricular_id' => $origen->unidad_curricular_id,
                    'seccion'              => $request->nuevo_codigo,
                    'cantidad_estudiantes' => $mover,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Se creó la nueva sección '{$request->nuevo_codigo}' con {$mover} estudiantes.",
                    'data'    => [
                        'origen' => $origen->fresh(),
                        'nueva'  => $nuevaSeccion
                    ]
                ], 201);
            }
        });
    }

}
