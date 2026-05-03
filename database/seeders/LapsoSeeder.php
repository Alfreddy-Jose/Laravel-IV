<?php

namespace Database\Seeders;

use App\Models\LapsoAcademico;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LapsoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LapsoAcademico::firstOrcreate([
            'nombre_lapso' => '20254',
            'ano' => '2025',
            'fecha_inicio' => '2025-01-10',
            'fecha_fin' => '2025-12-10',
            'status' => 'true',
            'tipo_lapso_id' => '4',
        ]);

        LapsoAcademico::firstOrcreate([
            'nombre_lapso' => '20244', 
            'ano' => '2024',
            'fecha_inicio' => '2024-01-10',
            'fecha_fin' => '2024-12-10',
            'status' => 'true',
            'tipo_lapso_id' => '4',
        ]);

        LapsoAcademico::firstOrcreate([
            'nombre_lapso' => '20234',
            'ano' => '2023',
            'fecha_inicio' => '2023-01-10',
            'fecha_fin' => '2023-12-10',
            'status' => 'true',
            'tipo_lapso_id' => '4',
        ]);
    }
}
