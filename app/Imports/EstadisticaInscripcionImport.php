<?php

namespace App\Imports;

use App\Models\EstadisticaInscripcion;
use App\Models\UnidadCurricular;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use App\Models\Sede;

class EstadisticaInscripcionImport implements ToCollection
{
    protected int $lapsoId;
    protected ?int $pnfId;
    protected array $errores = [];
    protected array $seccionesProcesadas = [];
    protected string $trayectoActual = 'Trayecto I';

    public function __construct(int $lapsoId, ?int $pnfId = null)
    {
        $this->lapsoId = $lapsoId;
        $this->pnfId = $pnfId;
    }



    /**
 * Normaliza el código de sección, extrae el código base de la sede
 * y consulta el registro dinámicamente en la base de datos.
 */
    private function obtenerSedePorSeccion(string $codigoSeccion): ?Sede
    {
        // Normalizar a 7 dígitos agregando el '0' inicial si falta (ej: '751101' -> '0751101')
        $codigoPadded = str_pad($codigoSeccion, 7, '0', STR_PAD_LEFT);

        // Extraer el bloque de 3 cifras a partir de la 5ta posición (índice 4):
        // En '0751101', la subcadena a partir de la pos 4 es '101'
        $tresCifras = (int) substr($codigoPadded, 4, 3);

        // Obtener el código base de la sede redondeando a la centena (ej: 101 -> 100, 301 -> 300)
        $codigoSedeBase = (int) (floor($tresCifras / 100) * 100);

        // Buscar dinámicamente en la tabla de sedes
        return Sede::where('nro_sede', $codigoSedeBase)
            ->first();
    }


    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $numFila = $index + 1;

            // 1. Detectar si la fila es un encabezado de trayecto (ej. "TRAYECTO I")
            $col0 = $row[0] ?? null;
            if (!empty($col0) && is_string($col0) && str_contains(strtoupper($col0), 'TRAYECTO')) {
                $this->trayectoActual = trim($col0);
                continue;
            }

            // 2. Omitir filas vacías o no numéricas en la columna de materia
            $col1 = $row[1] ?? null;
            if (empty($col1) || !is_numeric($col1)) {
                continue;
            }

            $codigoSeccion = trim((string)($row[0] ?? ''));
            $codigoMateria = (int)$col1;
            $nombreMateria = trim((string)($row[2] ?? ''));
            $matricula     = (int)($row[3] ?? 0);

            // 3. Buscar Unidad Curricular
            $uc = UnidadCurricular::where('codigo', $codigoMateria)->first();

            if (!$uc) {
                $this->errores[] = "Fila {$numFila}: No se encontró la UC con código {$codigoMateria}.";
                continue;
            }

            // Resolver la Sede dinámicamente desde la BD
            $sedeModel  = $this->obtenerSedePorSeccion($codigoSeccion);
            $nombreSede = $sedeModel ? ($sedeModel->nombre ?? $sedeModel->nombre_sede) : 'Sede Principal';

            // 4. Guardar o actualizar registro en Base de Datos
            $registro = EstadisticaInscripcion::updateOrCreate(
                [
                    'lapso_id'             => $this->lapsoId,
                    'unidad_curricular_id' => $uc->id,
                    'seccion'              => $codigoSeccion,
                ],
                [
                    'cantidad_estudiantes'            => $matricula,
                    'sede_id'              => $sedeModel?->id,
                    'pnf_id'                 => $this->pnfId,
                ]
            );

            // 5. Agregar objeto formateado para el Frontend
            $this->seccionesProcesadas[] = [
                'id'                   => $registro->id,
                'codigo'               => $codigoSeccion,
                'nombre'               => "Sección {$codigoSeccion}",
                'unidad_curricular'    => $uc->nombre,
                'sede'                 => $nombreSede,
                'sede_id'              => $sedeModel?->id,
                'trayecto'             => $this->trayectoActual,
                'matricula'            => $matricula,
                'unidad_curricular_id' => $uc->id,
            ];
        }
    }

    public function getResumen(): array
    {
        return [
            'procesados' => count($this->seccionesProcesadas),
            'errores'    => $this->errores,
            'secciones'  => $this->seccionesProcesadas,
        ];
    }
}
