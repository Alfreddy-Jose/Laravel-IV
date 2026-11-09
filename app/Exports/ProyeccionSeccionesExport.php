<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProyeccionSeccionesExport implements WithMultipleSheets
{
    protected $detalles;
    protected $anoProyectado;
    protected $anoOrigenHistorico;

    public function __construct(array $detalles, string $anoProyectado = "2026", string $anoOrigenHistorico = "2025")
    {
        $this->detalles = $detalles;
        $this->anoProyectado = $anoProyectado;
        $this->anoOrigenHistorico = $anoOrigenHistorico;
    }

    public function sheets(): array
    {
        return [
            // Instanciamos las 3 pestañas (crearemos estas clases en el siguiente paso)
            'SECCIONES ORIGEN' => new SeccionesOrigenSheet($this->detalles, $this->anoOrigenHistorico),
            'SECCIONES DESTINO' => new SeccionesDestinoSheet($this->detalles, $this->anoProyectado, $this->anoOrigenHistorico),
            'DEFINITIVA' => new DefinitivaSheet($this->detalles, $this->anoProyectado, $this->anoOrigenHistorico),
        ];
    }
}
