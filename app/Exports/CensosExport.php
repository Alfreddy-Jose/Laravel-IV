<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CensosExport implements FromArray, WithStyles, WithColumnWidths, WithEvents, WithDrawings
{
    protected $censos;
    protected $lapso;
    protected $pnfNombre;
    protected $coordinador;

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
            $drawing->setDescription('Cabecera del Reporte de Censo');
            $drawing->setPath($imgPath);

            // Permite estirar la imagen a lo ancho sin depender del alto
            $drawing->setResizeProportional(false);

            $drawing->setHeight(75);     // Mantiene el alto deseado
            $drawing->setWidth(1865);    // Se estira para cubrir exactamente B, C, D, E, F y G

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

        for ($i = 1; $i <= 5; $i++) {
            $data[] = [null];
        }

        $data[] = ['CENSO PARA POLÍTICA ESTUDIANTIL POR SECCIÓN'];
        $data[] = [null];

        $data[] = [null, 'PNF:', $this->pnfNombre, null, null, 'FECHA DEL CENSO:', date('d/m/Y')];
        $data[] = [null, 'COORDINADOR:', $this->coordinador];
        $data[] = [null, 'LAPSO:', $this->lapso];

        $data[] = [null];
        $data[] = [null];

        $categorias = [
            'EMBARAZADAS'  => ['campo_det' => 'semanas_gestacion',  'label_det' => 'GESTACIÓN'],
            'DEPORTISTAS'  => ['campo_det' => 'disciplina',         'label_det' => 'DISCIPLINA'],
            'MUSICOS (AS)' => ['campo_det' => 'instrumento',        'label_det' => 'INSTRUMENTO'],
            'DISCAPACIDAD' => ['campo_det' => 'tipo_condicion',     'label_det' => 'CONDICIÓN ESPECIAL']
        ];

        foreach ($categorias as $tituloCat => $config) {
            $data[] = [$tituloCat];
            $data[] = ['NRO', 'CEDULA', 'NOMBRE Y APELLIDO', 'DIRECCIÓN', 'MUNICIPIO', 'TELÉFONO', $config['label_det'], 'SECCION'];

            $filtrados = array_filter($this->censos, function ($c) use ($tituloCat) {
                $catData = $c['detalles']['categoria'] ?? '';
                if ($tituloCat === 'EMBARAZADAS') {
                    return $catData === 'EMBARAZADA';
                }
                if ($tituloCat === 'DEPORTISTAS') {
                    return $catData === 'DEPORTISTA';
                }
                if ($tituloCat === 'MUSICOS (AS)') {
                    return $catData === 'MUSICO';
                }
                if ($tituloCat === 'DISCAPACIDAD') {
                    return $catData === 'DISCAPACIDAD';
                }
                return false;
            });

            $contador = 1;
            if (count($filtrados) > 0) {
                foreach ($filtrados as $censo) {
                    $persona = $censo['estudiante']['persona'] ?? null;
                    $seccion = $censo['estudiante']['secciones'][0]['nombre'] ?? 'N/A';
                    $detalleValor = $censo['detalles'][$config['campo_det']] ?? '';

                    if ($tituloCat === 'DISCAPACIDAD' && !empty($censo['detalles']['grado_condicion'])) {
                        $detalleValor .= " - " . $censo['detalles']['grado_condicion'];
                    }

                    if ($tituloCat === 'EMBARAZADAS' && !empty($censo['detalles']['semanas_gestacion'])) {
                        $detalleValor .= " Semanas";
                    }

                    $municipioNombre = data_get($persona, 'municipio.municipio', 'S/D');

                    $data[] = [
                        $contador++,
                        $persona['cedula_persona'] ?? '',
                        ($persona['nombre'] ?? '') . ' ' . ($persona['apellido'] ?? ''),
                        $persona['direccion'] ?? 'S/D',
                        $municipioNombre,
                        $persona['telefono'] ?? '',
                        $detalleValor,
                        $seccion
                    ];
                }
            } else {
                for ($j = 1; $j <= 3; $j++) {
                    $data[] = [$contador++, null, null, null, null, null, null, null];
                }
            }

            $data[] = [null];
            $data[] = [null];
        }

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 24,
            'C' => 45,
            'D' => 65,
            'E' => 38,
            'F' => 32,
            'G' => 45,
            'H' => 22
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A1:H1000' => [
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
                $sheet->mergeCells('A6:H6');
                $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(16)->setName('Calibri');
                $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Metadatos
                $sheet->getStyle('B8:B10')->getFont()->setBold(true);
                $sheet->getStyle('B8:B10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('C8:C10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('F8')->getFont()->setBold(true);
                $sheet->getStyle('F8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('G8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $highestRow = $sheet->getHighestRow();

                for ($row = 13; $row <= $highestRow; $row++) {
                    $valA = $sheet->getCell("A{$row}")->getValue();

                    if (in_array($valA, ['EMBARAZADAS', 'DEPORTISTAS', 'MUSICOS (AS)', 'DISCAPACIDAD'])) {
                        $sheet->mergeCells("A{$row}:B{$row}");

                        $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true)->setSize(14)->setName('Calibri')->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));
                        $sheet->getStyle("A{$row}:B{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('9dc3e6');
                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A{$row}:B{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                        $headerRow = $row + 1;
                        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getFont()->setBold(true)->setSize(14)->setName('Calibri');
                        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getFill()->setFillType(Fill::FILL_NONE);
                        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    }

                    if (is_numeric($valA) && $valA > 0) {
                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                        $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    }
                }
            },
        ];
    }
}
