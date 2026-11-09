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

class SeccionesDestinoSheet implements FromView, WithTitle, ShouldAutoSize, WithDrawings, WithColumnWidths, WithEvents
{
    protected $detalles;
    protected $anoProyectado;
    protected $anoOrigenHistorico;

    public function __construct(array $detalles, string $anoProyectado, string $anoOrigenHistorico = "2025")
    {
        $this->detalles           = $detalles;
        $this->anoProyectado      = $anoProyectado;
        $this->anoOrigenHistorico = $anoOrigenHistorico;
    }

    public function title(): string
    {
        return 'SECCIONES DESTINO';
    }

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
        return [
            'A' => 6,
            'J' => 5, // Para centrar el icono CREAR
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $realizadoPath   = public_path('images/Realizado.png');
                $noRealizadoPath = public_path('images/No_realizado.png');

                for ($row = 8; $row <= $highestRow; $row++) {
                    // Columna J = CREAR
                    $crearVal = $sheet->getCell("J{$row}")->getValue();
                    if ($crearVal === '__CREAR_SI__' || $crearVal === '__CREAR_NO__') {
                        $sheet->setCellValue("J{$row}", '');
                        
                        $sheet->getRowDimension($row)->setRowHeight(18);

                        $imgPath = ($crearVal === '__CREAR_SI__') ? $realizadoPath : $noRealizadoPath;
                        if (file_exists($imgPath)) {
                            $drawing = new Drawing();
                            $drawing->setPath($imgPath);
                            $drawing->setHeight(14);
                            $drawing->setCoordinates("J{$row}");
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
        $nombresTrayecto = [
            '0' => 'PIU', '1' => 'I', '2' => 'II', '3' => 'III', '4' => 'IV', '5' => 'V',
        ];

        /*
         * ESTRUCTURA DEL SHEET SECCIONES DESTINO
         * ========================================
         * Agrupado por Trayecto Destino, luego por Sección Destino.
         *
         * Para cada grupo de Trayecto Destino (TDest):
         *   - Encabezados: SECCION | VIENEN DEL [Trayecto Origen] | NRO.ESTUD. | DEL [año] | NRO.ESTUD. | REPROBADOS TRAYECTO [Tn] | NRO.ESTUD. | TOTAL | APROXIMADO | CREAR
         *
         * Para cada sección destino dentro del grupo:
         *   - Columna SECCION: código de la sección destino (ej: 1501)
         *   - VIENEN DEL Trayecto (Trayecto N-1): cuáles secciones origen van aquí
         *   - NRO.ESTUD. (aprobados del trayecto origen)
         *   - DEL año: secciones reprobadas del mismo trayecto destino (se quedaron)
         *   - NRO.ESTUD. (reprobados)
         *   - REPROBADOS TRAYECTO N: cuáles secciones origen tienen reprobados que se quedan
         *   - NRO.ESTUD. (reprobados que se quedan en el mismo trayecto)
         *   - TOTAL: aprobados + reprobados que se quedan
         *   - APROXIMADO: total redondeado
         *   - CREAR: imagen si/no
         */

        // 1. Construir mapa de secciones por destino
        //    Un "detalle" representa: seccion_origen -> seccion_destino (los aprobados VAN al destino)
        //    Los reprobados de seccion_origen SE QUEDAN en el mismo trayecto

        // Agrupar por trayecto destino
        $trayectosDestino = [];

        foreach ($this->detalles as $d) {
            if (empty($d['seccion_destino'])) continue;

            $tOrigenNum  = (string)($d['trayecto_origen_nombre'] ?? '0');
            $tDestinoNum = (string)((int)$tOrigenNum + 1);

            if (!isset($trayectosDestino[$tDestinoNum])) {
                $trayectosDestino[$tDestinoNum] = [
                    'trayecto_origen_nombre'  => $nombresTrayecto[$tOrigenNum] ?? $tOrigenNum,
                    'trayecto_destino_nombre' => $nombresTrayecto[$tDestinoNum] ?? $tDestinoNum,
                    'trayecto_destino_num'    => $tDestinoNum,
                    'secciones'               => [],
                ];
            }

            // Agrupar por sede + seccion_destino dentro de este trayecto
            $sedeId = $d['sede_id'] ?? '1';
            $key    = $sedeId . '-' . $d['seccion_destino'];

            if (!isset($trayectosDestino[$tDestinoNum]['secciones'][$key])) {
                $trayectosDestino[$tDestinoNum]['secciones'][$key] = [
                    'sede_nombre'      => $d['sede_nombre'] ?? 'CENTRAL',
                    'seccion_destino'  => $d['seccion_destino'],
                    'crear_seccion'    => false,
                    // Aprobados que llegan (de secciones del trayecto anterior)
                    'aprobados' => [],   // [{seccion_origen, cantidad}]
                    // Reprobados que se quedan (del mismo trayecto, buscados via seccion_destino == seccion_origen)
                    'reprobados' => [],  // [{seccion_origen, cantidad}]
                ];
            }

            if (!empty($d['crear_seccion'])) {
                $trayectosDestino[$tDestinoNum]['secciones'][$key]['crear_seccion'] = true;
            }

            // Agregar los aprobados de este origen
            $eAprobados = (int)($d['estudiantes_aprobados_estimados'] ?? 0);
            if ($eAprobados > 0) {
                $trayectosDestino[$tDestinoNum]['secciones'][$key]['aprobados'][] = [
                    'seccion' => $d['seccion_origen'],
                    'cantidad' => $eAprobados,
                ];
            }
        }

        // 2. Buscar reprobados: son los registros donde seccion_origen == seccion_destino
        //    (el mismo nombre) porque esos no avanzaron. Su nuevo destino sigue siendo el mismo trayecto.
        foreach ($this->detalles as $d) {
            if (empty($d['seccion_destino'])) continue;

            $eReprobados = (int)($d['estudiantes_reprobados_estimados'] ?? 0);
            if ($eReprobados <= 0) continue;

            // Los reprobados de seccion_origen se quedan en "seccion_origen" (su trayecto no avanza).
            // Buscar a qué sección destino aportan reprobados: es la sección destino cuyo
            // seccion_destino = seccion_origen de este detalle (se quedan en el mismo nivel).
            $sedeId       = $d['sede_id'] ?? '1';
            $tOrigenNum   = (string)($d['trayecto_origen_nombre'] ?? '0');

            // Los reprobados de este trayecto origen se quedan en ese trayecto (no avanzan)
            // Afectan a la sección destino del mismo trayecto destino que sea igual a seccion_origen
            // Es decir: buscar en trayectosDestino[$tOrigenNum]['secciones'] donde seccion_destino == seccion_origen de $d
            if (isset($trayectosDestino[$tOrigenNum])) {
                foreach ($trayectosDestino[$tOrigenNum]['secciones'] as $key => &$seccion) {
                    if ($seccion['seccion_destino'] === $d['seccion_origen'] && str_starts_with($key, $sedeId . '-')) {
                        $seccion['reprobados'][] = [
                            'seccion' => $d['seccion_origen'],
                            'cantidad' => $eReprobados,
                        ];
                    }
                }
                unset($seccion);
            }
        }

        // 3. Ordenar trayectos por número
        ksort($trayectosDestino);

        // 4. Calcular totales y ordenar secciones por nombre
        foreach ($trayectosDestino as &$trayecto) {
            uasort($trayecto['secciones'], fn($a, $b) => strcmp($a['seccion_destino'], $b['seccion_destino']));
            foreach ($trayecto['secciones'] as &$seccion) {
                $totalAprobados  = array_sum(array_column($seccion['aprobados'], 'cantidad'));
                $totalReprobados = array_sum(array_column($seccion['reprobados'], 'cantidad'));
                $seccion['total_aprobados']  = $totalAprobados;
                $seccion['total_reprobados'] = $totalReprobados;
                $seccion['total']            = $totalAprobados + $totalReprobados;
            }
            unset($seccion);
        }
        unset($trayecto);

        return view('exports.reporte_destino', [
            'trayectosDestino'   => $trayectosDestino,
            'anoProyectado'      => $this->anoProyectado,
            'anoOrigenHistorico' => $this->anoOrigenHistorico,
        ]);
    }
}
