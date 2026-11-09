<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PlantillaEstadisticaExport implements FromCollection, ShouldAutoSize, WithEvents
{
    /**
     * Retorna la estructura oficial de datos directamente sin encabezados superiores.
     */
    public function collection()
    {
        return collect([
            ['TRAYECTO I', '', '', ''],
            ['751101', '7004', 'MATEMATICA I', 25],
            ['751101', '7005', 'ARQUITECTURA DEL COMPUTADOR', 30],
            ['751101', '7006', 'FORMACION CRITICA I', 28],
            ['TRAYECTO II', '', '', ''],
            ['752101', '7011', 'MATEMATICA II', 20],
            ['752101', '7012', 'ALGORITMICA Y PROGRAMACION II', 18],
        ]);
    }

    /**
     * Aplica la combinación de celdas (A:D), centrado y formato para los trayectos.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // 1. Combinar rango de celdas para TRAYECTO I (Fila 1)
                $event->sheet->mergeCells('A1:D1');
                $event->sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A1:D1')->getFont()->setBold(true);

                // 2. Combinar rango de celdas para TRAYECTO II (Fila 5)
                $event->sheet->mergeCells('A5:D5');
                $event->sheet->getStyle('A5:D5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A5:D5')->getFont()->setBold(true);
            },
        ];
    }
}
