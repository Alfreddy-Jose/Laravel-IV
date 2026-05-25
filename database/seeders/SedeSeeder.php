<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sede;

class SedeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sede1 = Sede::firstOrCreate([
            'nro_sede' => '500',
            'nombre_sede' => 'Sede Central',
            'nombre_abreviado' => 'IDN',
            'direccion' => 'Av Ravell',
            'municipio_id' => '409',
            'universidad_id' => '1',
        ]);
        $sede1->pnfs()->sync([1, 2, 3]);

        $sede2 = Sede::firstOrCreate([ 
            'nro_sede' => '100',
            'nombre_sede' => 'Sede Peña',
            'nombre_abreviado' => 'PEÑ',
            'direccion' => 'Independencia',
            'municipio_id' => '414',
            'universidad_id' => '1',
        ]);
        $sede2->pnfs()->sync([1, 2, 3]);

        $sede4 = Sede::firstOrCreate([
            'nro_sede' => '300',
            'nombre_sede' => 'Sede Bruzual',
            'nombre_abreviado' => 'BRU',
            'direccion' => 'Av -----Yaritagua',
            'municipio_id' => '413',
            'universidad_id' => '1',
        ]); 
        $sede4->pnfs()->sync([1, 2, 3]);
    }
}
