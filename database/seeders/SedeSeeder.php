<?php

namespace Database\Seeders;

use App\Models\Sede;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SedeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sede1 = Sede::firstOrcreate([
            'nro_sede' => '500',
            'nombre_sede' => 'SEDE CENTRAL',
            'nombre_abreviado' => 'IDN',
            'direccion' => 'AV RAVELL',
            'municipio_id' => '409',
            'universidad_id' => '1',
        ]);
        $sede1->pnfs()->sync([1, 2, 3]);

        $sede2 = Sede::firstOrcreate([ 
            'nro_sede' => '100',
            'nombre_sede' => 'SEDE PEÑA',
            'nombre_abreviado' => 'PEÑ',
            'direccion' => 'INDEPENDENCIA',
            'municipio_id' => '414',
            'universidad_id' => '1',
        ]);
        $sede2->pnfs()->sync([1, 2, 3]);

        $sede4 = Sede::firstOrcreate([
            'nro_sede' => '300',
            'nombre_sede' => 'SEDE BRUZUAL',
            'nombre_abreviado' => 'BRU',
            'direccion' => 'AV -----YARITAGUA',
            'municipio_id' => '413',
            'universidad_id' => '1',
        ]); 
        $sede4->pnfs()->sync([1, 2, 3]);
    }
}
