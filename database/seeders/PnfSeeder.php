<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pnf;
use App\Models\Trayecto;

class PnfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pnfs = [
            ['codigo' => '07', 'nombre' => 'Programa Nacional de Formación en Ingeniería en Informática', 'abreviado' => 'PNFI',  'abreviado_coord' => 'NFI'],
            ['codigo' => '08', 'nombre' => 'Programa Nacional de Formación en Administración',             'abreviado' => 'PNFA',  'abreviado_coord' => 'NFA'],
            ['codigo' => '09', 'nombre' => 'Programa Nacional de Formación en Procesos Químicos',          'abreviado' => 'PNFPQ', 'abreviado_coord' => 'NFPQ'],
        ];

        // Obtener IDs de todos los trayectos existentes
        $trayectoIds = Trayecto::pluck('id');

        foreach ($pnfs as $data) {
            $pnf = Pnf::firstOrCreate(['codigo' => $data['codigo']], $data);

            // Asociar todos los trayectos al PNF (sin eliminar los existentes)
            $pnf->trayectos()->syncWithoutDetaching($trayectoIds);
        }
    }
}