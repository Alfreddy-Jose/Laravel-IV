<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class DefinitivaSheet implements FromView, WithTitle, ShouldAutoSize, WithDrawings, WithColumnWidths, WithEvents
{
    protected $detalles;
    protected $lapsoAcademico;
    protected $lapsoAnterior;
    protected $anoProyectado;
    protected $anoOrigenHistorico;

    protected static $romanMap = [
        '0' => 'PIU', '1' => 'I', '2' => 'II', '3' => 'III', '4' => 'IV', '5' => 'V',
    ];

    public function __construct(array $detalles, string $anoProyectado, string $anoOrigenHistorico)
    {
        $this->detalles           = $detalles;
        $this->anoProyectado      = $anoProyectado;
        $this->anoOrigenHistorico = $anoOrigenHistorico;
    }

    public function title(): string { return 'DEFINITIVA'; }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Cabecera UPTYAB');
        $drawing->setDescription('Cabecera del Reporte de Proyección');
        $drawing->setPath(public_path('images/cabecera exel.png'));
        $drawing->setHeight(75);
        $drawing->setCoordinates('A1');
        return [$drawing];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'E' => 5]; // Col A: spacer, Col E (VAN): narrow for icon
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $realizadoPath   = public_path('images/Realizado.png');
                $noRealizadoPath = public_path('images/No_realizado.png');

                for ($row = 8; $row <= $highestRow; $row++) {
                    $vanVal = $sheet->getCell("E{$row}")->getValue();
                    if ($vanVal === '__VAN_SI__' || $vanVal === '__VAN_NO__') {
                        $sheet->setCellValue("E{$row}", '');

                        // Increase row height so icon can be centered
                        $sheet->getRowDimension($row)->setRowHeight(18);

                        $imgPath = ($vanVal === '__VAN_SI__') ? $realizadoPath : $noRealizadoPath;
                        if (file_exists($imgPath)) {
                            $drawing = new Drawing();
                            $drawing->setPath($imgPath);
                            $drawing->setHeight(14);
                            $drawing->setCoordinates("E{$row}");
                            // Center the 14px icon inside the ~18px-tall row
                            $drawing->setOffsetX(8);
                            $drawing->setOffsetY(2);
                            $drawing->setWorksheet($sheet);
                        }
                    }
                }
            },
        ];
    }

    public function view(): View
    {
        // 1. Agrupar todos los destinos, ordenados por trayecto -> sede -> sección
        $trayectosAgrupados = []; // trayecto_destino -> [ secciones ]

        foreach ($this->detalles as $d) {
            if (empty($d['seccion_destino'])) continue;

            $tOrigenStr  = (string)($d['trayecto_origen_nombre'] ?? '0');
            $tDestinoInt = (int)$tOrigenStr + 1;
            $tDestinoStr = (string)$tDestinoInt;
            $tDestinoRom = self::$romanMap[$tDestinoStr] ?? $tDestinoStr;

            $sedeId  = $d['sede_id'] ?? '1';
            $destKey = $sedeId . '-' . $d['seccion_destino'];

            if (!isset($trayectosAgrupados[$tDestinoStr])) {
                $trayectosAgrupados[$tDestinoStr] = [
                    'nombre_romano' => $tDestinoRom,
                    'destinos'      => [],
                ];
            }

            if (!isset($trayectosAgrupados[$tDestinoStr]['destinos'][$destKey])) {
                $trayectosAgrupados[$tDestinoStr]['destinos'][$destKey] = [
                    'sede_nombre'     => $d['sede_nombre'] ?? 'CENTRAL',
                    'trayecto_romano' => $tDestinoRom,
                    'seccion_destino' => $d['seccion_destino'],
                    'origenes'        => [],  // [{seccion, van_si}]
                    'mostrar_total'   => false,
                    'total_secciones' => 0,
                    'total_rowspan'   => 0,
                    'tiene_reincorporados' => false,
                ];
            }

            if (!empty($d['tiene_reincorporados'])) {
                $trayectosAgrupados[$tDestinoStr]['destinos'][$destKey]['tiene_reincorporados'] = true;
            }

            $tieneAprobados = (int)($d['estudiantes_aprobados_estimados'] ?? 0) > 0;
            $trayectosAgrupados[$tDestinoStr]['destinos'][$destKey]['origenes'][] = [
                'seccion' => $d['seccion_origen'],
                'van_si'  => $tieneAprobados,
            ];
        }

        // 2. Ordenar trayectos por número
        ksort($trayectosAgrupados);

        // 3. Para cada trayecto, calcular TOTAL SECCIONES por trayecto+sede
        foreach ($trayectosAgrupados as &$trayecto) {
            // Ordenar destinos por sede → sección
            uasort($trayecto['destinos'], fn($a, $b) =>
                strcmp($a['sede_nombre'], $b['sede_nombre']) ?: strcmp($a['seccion_destino'], $b['seccion_destino'])
            );

            // Totales de todo el TRAYECTO (para la columna "Total Secciones")
            $trayecto['total_secciones_trayecto'] = count($trayecto['destinos']);
            $trayecto['total_rowspan_trayecto'] = array_sum(
                array_map(fn($d) => max(count($d['origenes']), 1) + 2, $trayecto['destinos'])
            );

            // Marcar cuál es la primera sección de cada sede y la primera de todo el trayecto
            $mostradoSede = [];
            $esPrimerGrupoTrayecto = true;
            
            foreach ($trayecto['destinos'] as &$g) {
                // Flag para renderizar la celda "Total Secciones" (solo una vez por trayecto)
                $g['mostrar_total_trayecto'] = false;
                if ($esPrimerGrupoTrayecto) {
                    $g['mostrar_total_trayecto'] = true;
                    $esPrimerGrupoTrayecto = false;
                }

                $sede = $g['sede_nombre'];
                $g['mostrar_total'] = false; // por defecto false
                
                if (!isset($mostradoSede[$sede])) {
                    $g['mostrar_total']   = true;
                    // Solo contar las filas de las secciones de ESTA sede (para el rowspan de las Cols B y C)
                    $seccionesDeLaSede = array_filter(
                        $trayecto['destinos'],
                        fn($d) => $d['sede_nombre'] === $sede
                    );
                    $g['total_rowspan'] = array_sum(
                        array_map(fn($d) => max(count($d['origenes']), 1) + 2, $seccionesDeLaSede)
                    );
                    $mostradoSede[$sede]  = true;
                }
            }
            unset($g);
        }
        unset($trayecto);

        return view('exports.reporte_definitivo', [
            'trayectosAgrupados' => $trayectosAgrupados,
            'anoProyectado'      => $this->anoProyectado,
            'anoOrigenHistorico' => $this->anoOrigenHistorico,
        ]);
    }
}
