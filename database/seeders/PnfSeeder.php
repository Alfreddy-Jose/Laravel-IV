<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pnf;

class PnfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pnf::firstOrCreate([
            'codigo' => '07',
            'nombre' => 'Programa Nacional de Formación en Ingeniería en Informática',
            'abreviado' => 'PNFI',
            'abreviado_coord' => 'NFI',
        ]);
        Pnf::firstOrCreate([
            'codigo' => '08',
            'nombre' => 'Programa Nacional de Formación en Administración',
            'abreviado' => 'PNFA',
            'abreviado_coord' => 'NFA',
        ]);
        Pnf::firstOrCreate([
            'codigo' => '09', 
            'nombre' => 'Programa Nacional de Formación en Procesos Químicos',
            'abreviado' => 'PNFPQ',
            'abreviado_coord' => 'NFPQ',
        ]);
    }
}