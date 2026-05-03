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
            'nombre' => 'PROGRAMA NACIONAL DE FORMACIÓN EN INGENIERÍA EN INFORMÁTICA',
            'abreviado' => 'PNFI',
            'abreviado_coord' => 'NFI',
        ]);
        Pnf::firstOrCreate([
            'codigo' => '08',
            'nombre' => 'PROGRAMA NACIONAL DE FORMACIÓN EN ADMINISTRACIÓN',
            'abreviado' => 'PNFA',
            'abreviado_coord' => 'NFA',
        ]);
        Pnf::firstOrCreate([
            'codigo' => '09', 
            'nombre' => 'PROGRAMA NACIONAL DE FORMACIÓN EN PROCESOS QUIMICOS',
            'abreviado' => 'PNFPQ',
            'abreviado_coord' => 'NFPQ',
        ]);
    }
}
