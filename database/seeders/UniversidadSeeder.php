<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Universidad;

class UniversidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Universidad::firstOrCreate([
            'rif_univ' => 'G200121181',
            'nombre_univ' => 'UNIVERSIDAD POLITECNICA TERRITORIAL DE YARACUY ARISTIDES BASTIDAS',
            'abreviado_univ' => 'UPTYAB',
            'direccion' => 'AV ALBERTO RAVELLCON AV JOSE ANTONIO PAEZ / INDEPENDENCIA / EDO YARACUY',
        ]);

    }
}
