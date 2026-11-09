<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CensosAcademicoExport implements FromArray, WithStyles, WithColumnWidths, WithEvents, WithDrawings
{
    protected $censos;
    protected $lapso;
    protected $pnfNombre;
    protected $coordinador;

    /**
     * Tabla de sinónimos normalizados -> clave canónica.
     * Cubre los casos con "DE", guiones, espacios y mayúsculas/minúsculas.
     */
    private const SINONIMOS = [
        'PROSECUCION'                 => 'PROSECUCION',
        'PROSECUCION_DE_ESTUDIOS'     => 'PROSECUCION',
        'PROSECUCION_ESTUDIOS'        => 'PROSECUCION',
        'CAMBIO_PNF'                  => 'CAMBIO_PNF',
        'CAMBIO_DE_PNF'               => 'CAMBIO_PNF',
        'CAMBIO_PNF_DE_ESTUDIOS'      => 'CAMBIO_PNF',
        'CAMBIO_SECCION'              => 'CAMBIO_SECCION',
        'CAMBIO_DE_SECCION'           => 'CAMBIO_SECCION',
        'PLAN_MULTIMODAL'             => 'PLAN_MULTIMODAL',
        'MULTIMODAL'                  => 'PLAN_MULTIMODAL',
        'PLAN_DE_ESTUDIOS_MULTIMODAL' => 'PLAN_MULTIMODAL',
    ];

    private const ETIQUETAS = [
        'PROSECUCION'     => 'PROSECUCIÓN DE ESTUDIOS',
        'CAMBIO_PNF'      => 'CAMBIO DE PNF',
        'CAMBIO_SECCION'  => 'CAMBIO DE SECCIÓN',
        'PLAN_MULTIMODAL' => 'PLAN MULTIMODAL',
    ];

    public function __construct($censos, $lapso, $pnfNombre, $coordinador)
    {
        $this->censos = $censos;
        $this->lapso = $lapso;
        $this->pnfNombre = $pnfNombre;
        $this->coordinador = $coordinador;
    }

    public function drawings()
    {
        $imgPath = public_path('images/cabecera exel.png');

        if (file_exists($imgPath)) {
            $drawing = new Drawing();
            $drawing->setName('Cabecera UPTYAB');
            $drawing->setDescription('Cabecera del Reporte de Censo Académico');
            $drawing->setPath($imgPath);

            // Permite estirar la imagen a lo ancho sin depender del alto
            $drawing->setResizeProportional(false);

            $drawing->setHeight(75);     // Mantiene el alto deseado
            $drawing->setWidth(1535);    // Se estira para cubrir las columnas B a F

            $drawing->setCoordinates('B1'); // Comienza justo en la columna B
            $drawing->setOffsetX(0);        // 0 para que empiece AL RAS del borde de la columna B
            $drawing->setOffsetY(2);

            return [$drawing];
        }

        return [];
    }

    public function array(): array
    {
        $data = [];

        // Filas 1-5: encabezado institucional vacío (reservado para la imagen)
        for ($i = 1; $i <= 5; $i++) {
            $data[] = [null];
        }

        // Fila 6: título principal
        $data[] = ['CENSO DE TRÁMITES ACADÉMICOS POR SECCIÓN'];
        $data[] = [null]; // Fila 7 vacía

        // Filas 8-10: metadatos
        $data[] = [null, 'PNF:', $this->pnfNombre, null, 'FECHA REPORTADA:', date('d/m/Y')];
        $data[] = [null, 'COORDINADOR:', $this->coordinador];
        $data[] = [null, 'LAPSO:', $this->lapso];

        // Filas 11-12: espaciadores
        $data[] = [null];
        $data[] = [null];

        // Clasificamos UNA sola vez todos los censos en las 4 categorías canónicas.
        $agrupados = [
            'PROSECUCION'     => [],
            'CAMBIO_PNF'      => [],
            'CAMBIO_SECCION'  => [],
            'PLAN_MULTIMODAL' => [],
        ];

        foreach ($this->censos as $censo) {
            $censo = is_array($censo) ? $censo : ((array) $censo);

            $clave = $this->obtenerClaveSubtramite($censo);
            if ($clave !== null && isset($agrupados[$clave])) {
                $agrupados[$clave][] = $censo;
            }
        }

        // Fila 13 en adelante: una sección por subtipo.
        foreach ($agrupados as $clave => $grupo) {
            $data[] = [self::ETIQUETAS[$clave]];
            $data[] = ['NRO', 'CEDULA', 'NOMBRE Y APELLIDO', 'SECCION ORIGEN', 'DETALLE DEL TRAMITE', 'TELEFONO'];

            $contador = 1;
            if (count($grupo) > 0) {
                foreach ($grupo as $censo) {
                    $data[] = $this->formatearFila($censo, $contador++);
                }
            } else {
                // Sin contador numérico para no confundir al evento AfterSheet.
                $data[] = [null, 'SIN TRÁMITES REGISTRADOS', null, null, null, null];
            }

            $data[] = [null];
            $data[] = [null];
        }

        return $data;
    }

    /**
     * Devuelve la clave canónica (PROSECUCION, CAMBIO_PNF, ...) de un censo,
     * buscando entre varias claves candidatas del JSON y resolviendo sinónimos.
     */
    private function obtenerClaveSubtramite(array $censo): ?string
    {
        $detalles = $censo['detalles'] ?? null;

        // El cast 'array' de Eloquent puede dejarlo como string si viene crudo.
        if (is_string($detalles)) {
            $detalles = json_decode($detalles, true);
        }
        if (!is_array($detalles)) {
            $detalles = [];
        }

        // Claves candidatas donde el frontend pudo guardar el sub-trámite.
        // Ajusta esta lista tras correr el dd() de diagnóstico con tu clave real.
        $camposCandidatos = [
            'subtipo_academico',
            'tipo_academico',
        ];

        foreach ($camposCandidatos as $campo) {
            if (!empty($detalles[$campo])) {
                $clave = $this->normalizarYAsignar((string) $detalles[$campo]);
                if ($clave !== null) {
                    return $clave;
                }
            }
        }

        // Fallback: si el subtipo se guarda en el nombre de la ficha.
        if (!empty($censo['nombre'])) {
            $clave = $this->normalizarYAsignar((string) $censo['nombre']);
            if ($clave !== null) {
                return $clave;
            }
        }

        return null;
    }

    /**
     * Convierte un valor de sub-trámite (con acentos, espacios, guiones,
     * mayúsculas minúsculas) a su clave canónica.
     */
    private function normalizarYAsignar(string $valor): ?string
    {
        $valor = trim($valor);
        if ($valor === '') {
            return null;
        }

        $valor = mb_strtoupper($valor, 'UTF-8');
        $valor = str_replace(
            ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü', 'À', 'È', 'Ì', 'Ò', 'Ù', 'Â', 'Ê', 'Î', 'Ô', 'Û'],
            ['A', 'E', 'I', 'O', 'U', 'N', 'U', 'A', 'E', 'I', 'O', 'U', 'A', 'E', 'I', 'O', 'U'],
            $valor
        );
        // Cualquier cosa que no sea letra o número se vuelve "_".
        $valor = preg_replace('/[^A-Z0-9]+/', '_', $valor);
        $valor = trim($valor, '_');

        return self::SINONIMOS[$valor] ?? null;
    }

    private function formatearFila(array $censo, int $nro): array
    {
        $estudiante = $censo['estudiante'] ?? [];
        $persona = $estudiante['persona'] ?? [];
        $secciones = $estudiante['secciones'] ?? [];

        $seccionOrigen = $secciones[0]['nombre'] ?? 'N/A';

        $detalles = $censo['detalles'] ?? [];
        if (is_string($detalles)) {
            $detalles = json_decode($detalles, true) ?: [];
        }
        if (!is_array($detalles)) {
            $detalles = [];
        }

        $detalle = $detalles['observaciones'] ?? ($censo['observaciones'] ?? 'Sin observaciones');

        // Para PLAN_MULTIMODAL mostramos las materias en vez de observaciones.
        $valorSubtipo = '';
        foreach (['subtipo_academico', 'subtipo_tramite'] as $campo) {
            if (!empty($detalles[$campo])) {
                $valorSubtipo = (string) $detalles[$campo];
                break;
            }
        }

        if ($this->normalizarYAsignar($valorSubtipo) === 'PLAN_MULTIMODAL') {
            $asignaturas = $detalles['asignaturas_detalladas'] ?? $detalles['asignaturas_pendientes'] ?? [];

            if (is_array($asignaturas) && count($asignaturas) > 0) {
                $nombres = array_map(function ($a) {
                    return is_array($a) ? ($a['nombre'] ?? '') : (string) $a;
                }, $asignaturas);

                $nombres = array_values(array_filter($nombres, fn ($n) => $n !== ''));

                if (count($nombres) > 0) {
                    $detalle = implode(', ', $nombres);
                }
            }
        }

        return [
            $nro,
            $persona['cedula_persona'] ?? '',
            trim(($persona['nombre'] ?? '') . ' ' . ($persona['apellido'] ?? '')),
            $seccionOrigen,
            $detalle,
            $persona['telefono'] ?? '',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 24,
            'C' => 45,
            'D' => 40,
            'E' => 85,
            'F' => 32
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Ajusta 'F2000' si esperas más de 2000 filas.
            'A1:F2000' => [
                'font' => [
                    'name' => 'Calibri',
                    'size' => 14
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Título principal
                $sheet->mergeCells('A6:F6');
                $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(16)->setName('Calibri');
                $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Metadatos
                $sheet->getStyle('B8:B10')->getFont()->setBold(true);
                $sheet->getStyle('B8:B10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('C8:C10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('E8')->getFont()->setBold(true);
                $sheet->getStyle('E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('F8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $highestRow = $sheet->getHighestRow();

                for ($row = 13; $row <= $highestRow; $row++) {
                    $valA = $sheet->getCell("A{$row}")->getValue();

                    if (in_array($valA, array_values(self::ETIQUETAS), true)) {
                        $sheet->mergeCells("A{$row}:B{$row}");

                        $sheet->getStyle("A{$row}:B{$row}")->getFont()
                            ->setBold(true)->setSize(14)->setName('Calibri')
                            ->setColor(new Color(Color::COLOR_BLACK));
                        $sheet->getStyle("A{$row}:B{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('9dc3e6');
                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A{$row}:B{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                        $headerRow = $row + 1;
                        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->getFont()->setBold(true)->setSize(14)->setName('Calibri');
                        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->getFill()->setFillType(Fill::FILL_NONE);
                        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    }

                    if (is_numeric($valA) && $valA > 0) {
                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                        $sheet->getStyle("A{$row}:F{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    }
                }
            }
        ];
    }
}
