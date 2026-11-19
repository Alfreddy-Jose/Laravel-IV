<?php

namespace Database\Seeders;

use App\Models\AnoAcademico;
use App\Models\LapsoAcademico;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LapsoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Recuperamos los años académicos padres para asociar las llaves foráneas
        $ano2026 = AnoAcademico::where('ano', 2026)->first();
        $ano2027 = AnoAcademico::where('ano', 2027)->first();

        // Asumiremos IDs genéricos de ejemplo para 'tipo_lapso_id' (ej: 1 para Regular, 2 para Intensivo)
        // Ajusta estos IDs según los registros reales que tengas en tu tabla 'tipo_lapsos'

        if ($ano2026) {
            // Lapso 20264 (Activo por ejemplo, o según tu flujo del scope)
            LapsoAcademico::firstOrcreate([
                'nombre_lapso' => '20264',
                'ano_academico_id' => $ano2026->id,
                'tipo_lapso_id' => '4',
                'fecha_inicio' => Carbon::create(2026, 1, 15),
                'fecha_fin' => Carbon::create(2026, 11, 30),
                'status' => 'activo',
            ]);

            // Lapso 20265 (En planificación)
            LapsoAcademico::firstOrcreate([
                'nombre_lapso' => '20265',
                'ano_academico_id' => $ano2026->id,
                'tipo_lapso_id' => '5',
                'fecha_inicio' => Carbon::create(2026, 6, 15),
                'fecha_fin' => Carbon::create(2026, 11, 30),
                'status' => 'planificacion',
            ]);
        }

        if ($ano2027) {
            // Lapso 20274 (En planificación)
            LapsoAcademico::firstOrcreate([
                'nombre_lapso' => '20274',
                'ano_academico_id' => $ano2027->id,
                'tipo_lapso_id' => '4',
                'fecha_inicio' => Carbon::create(2027, 1, 15),
                'fecha_fin' => Carbon::create(2027, 11, 30),
                'status' => 'planificacion',
            ]);

            // Lapso 20275 (En planificación)
            LapsoAcademico::firstOrcreate([
                'nombre_lapso' => '20275',
                'ano_academico_id' => $ano2027->id,
                'tipo_lapso_id' => '5',
                'fecha_inicio' => Carbon::create(2027, 6, 15),
                'fecha_fin' => Carbon::create(2027, 11, 30),
                'status' => 'planificacion',
            ]);
        }
    }
}
