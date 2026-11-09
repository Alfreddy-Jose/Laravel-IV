<?php

namespace Database\Seeders;

use App\Models\AnoAcademico;
use Illuminate\Database\Seeder;

class AnoAcademicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $anos = [
            [
                'ano' => 2025,
                'fecha_inicio' => '2025-01-15',
                'fecha_fin' => '2025-12-15',
                // Forzamos manualmente a cerrado para blindar el histórico del año pasado
                'status' => 'cerrado', 
            ],
            [
                'ano' => 2026,
                'fecha_inicio' => '2026-01-15',
                'fecha_fin' => '2026-12-15',
                // Lo dejamos en 'cronograma' para que el sistema use la automatización por fechas.
                // Como hoy es junio de 2026, el modelo calculará automáticamente que está 'activo'.
                'status' => 'cronograma', 
            ],
            [
                'ano' => 2027,
                'fecha_inicio' => '2027-01-15',
                'fecha_fin' => '2027-12-15',
                // Al estar en 'cronograma' y ser una fecha futura, el modelo calculará 'planificacion'
                'status' => 'cronograma', 
            ],
        ];

        foreach ($anos as $ano) {
            AnoAcademico::updateOrCreate(
                ['ano' => $ano['ano']], // Evita duplicados si corres el seeder más de una vez
                [
                    'fecha_inicio' => $ano['fecha_inicio'],
                    'fecha_fin' => $ano['fecha_fin'],
                    'status' => $ano['status'],
                ]
            );
        }
    }
}