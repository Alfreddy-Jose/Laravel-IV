<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SeccionesOrigenSheet implements FromView, WithTitle, ShouldAutoSize, WithDrawings, WithColumnWidths
{
    protected $detalles;
    protected $anoOrigenHistorico;

    public function __construct(array $detalles, string $anoOrigenHistorico)
    {
        $this->detalles = $detalles;
        $this->anoOrigenHistorico = $anoOrigenHistorico;
    }

    public function title(): string
    {
        return 'SECCIONES ORIGEN';
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
        ];
    }

    public function view(): View
    {
        $agrupado = [];

        // Diccionario para asegurar los nombres correctos de los trayectos
        $nombresTrayecto = [
            '0' => 'PIU',
            '1' => 'I',
            '2' => 'II',
            '3' => 'III',
            '4' => 'IV',
            '5' => 'V',
        ];

        // 1. Recorrer y agrupar por Trayecto -> Sede -> Secciones
        foreach ($this->detalles as $detalle) {
            // Si viene vacío o es 0, lo tratamos como PIU
            $tNum = (string)($detalle['trayecto_origen_nombre'] ?? '0');
            $sede = $detalle['sede_nombre'] ?? 'CENTRAL';

            if (!isset($agrupado[$tNum])) {
                $agrupado[$tNum] = [
                    'nombre_trayecto' => $nombresTrayecto[$tNum] ?? $tNum,
                    'sedes' => []
                ];
            }

            if (!isset($agrupado[$tNum]['sedes'][$sede])) {
                $agrupado[$tNum]['sedes'][$sede] = [];
            }

            // Realizamos el cálculo matemático: Grupos * 5
            $gAprob = (int)($detalle['grupos_aprobados'] ?? 0);
            $gReprob = (int)($detalle['grupos_reprobados'] ?? 0);

            $agrupado[$tNum]['sedes'][$sede][] = [
                'seccion' => $detalle['seccion_origen'] ?? '',
                'g_aprob' => $gAprob,
                'e_aprob' => $gAprob * 5,
                'g_reprob' => $gReprob,
                'e_reprob' => $gReprob * 5,
            ];
        }

        // 2. Ordenar por Trayecto (ksort garantiza que el 0/PIU quede de primero, seguido del 1, 2...)
        ksort($agrupado);

        // 3. Ordenar alfabéticamente las secciones dentro de cada sede para mayor limpieza
        foreach ($agrupado as $tNum => &$dataTrayecto) {
            foreach ($dataTrayecto['sedes'] as $sede => &$secciones) {
                usort($secciones, function($a, $b) {
                    return strcmp($a['seccion'], $b['seccion']);
                });
            }
        }

        return view('exports.reporte_origen', [
            'trayectos' => $agrupado,
            'anoOrigenHistorico' => $this->anoOrigenHistorico,
        ]);
    }
}
