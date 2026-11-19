<?php

namespace Database\Seeders;

use App\Models\Espacio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EspaciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Espacio::firstOrcreate([

            'nombre_aula' => 'E-7',
            'etapa' => 'E',
            'nro_aula' => '7',
            'tipo_espacio' => 'AULA',
            'sede_id' => '1'
        ]);

        Espacio::firstOrcreate([


            'nombre_aula' => 'B-17',
            'etapa' => 'B',
            'nro_aula' => '17',
            'tipo_espacio' => 'AULA',
            'sede_id' => '1'


        ]);

        Espacio::firstOrcreate([


            'nombre_aula' => 'B-18',
            'etapa' => 'B',
            'nro_aula' => '18',
            'tipo_espacio' => 'AULA',
            'sede_id' => '1'


        ]);

        Espacio::firstOrcreate([


            'nombre_aula' => 'B-19',
            'etapa' => 'B',
            'nro_aula' => '19',
            'tipo_espacio' => 'AULA',
            'sede_id' => '1'


        ]);

        Espacio::firstOrcreate([


            'nombre_aula' => 'B-20',
            'etapa' => 'B',
            'nro_aula' => '20',
            'tipo_espacio' => 'AULA',
            'sede_id' => '1'


        ]);

        Espacio::firstOrcreate([


            'nombre_aula' => 'C-3',
            'etapa' => 'C',
            'nro_aula' => '3',
            'tipo_espacio' => 'AULA',
            'sede_id' => '1'


        ]);

        Espacio::firstOrcreate([


            'nombre_aula' => 'C-4',
            'etapa' => 'C',
            'nro_aula' => '4',
            'tipo_espacio' => 'AULA',
            'sede_id' => '1'


        ]);

        Espacio::firstOrcreate([


            'nombre_aula' => 'SIMÓN BOLÍVAR',
            'etapa' => 'E',
            'nro_aula' => '4',
            'tipo_espacio' => 'LABORATORIO',
            'abreviado_lab' => 'SMB',
            'equipos' => '17',
            'sede_id' => '1'


        ]);

        Espacio::firstOrcreate([


            'nombre_aula' => 'HUGO CHÁVEZ',
            'etapa' => 'E',
            'nro_aula' => '3',
            'tipo_espacio' => 'LABORATORIO',
            'abreviado_lab' => 'HGC',
            'equipos' => '18',
            'sede_id' => '1'
        ]);
    }
}
