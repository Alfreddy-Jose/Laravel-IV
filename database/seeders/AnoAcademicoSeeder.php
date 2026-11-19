<?php

namespace Database\Seeders;

use App\Models\AnoAcademico;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AnoAcademicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Año Académico 2026 (Activo para permitir pruebas en tiempo real)
        AnoAcademico::firstOrcreate([
            'ano' => 2026,
            'fecha_inicio' => Carbon::create(2026, 1, 15),
            'fecha_fin' => Carbon::create(2026, 11, 30),
            'status' => 'activo', // O 'cronograma' según manejes tu lógica física
        ]);

        // Año Académico 2027 (En Planificación)
        AnoAcademico::firstOrcreate([
            'ano' => 2027,
            'fecha_inicio' => Carbon::create(2027, 1, 15),
            'fecha_fin' => Carbon::create(2027, 11, 30),
            'status' => 'planificacion',
        ]);

        // Año Académico 2028 (En Planificación)
        AnoAcademico::firstOrcreate([
            'ano' => 2028,
            'fecha_inicio' => Carbon::create(2028, 1, 15),
            'fecha_fin' => Carbon::create(2028, 11, 30),
            'status' => 'planificacion',
        ]);
    }
}
