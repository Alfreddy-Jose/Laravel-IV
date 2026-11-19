<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['id_estado' => 1, 'estado' => 'AMAZONAS', 'iso_3166' => 'VE-X'],
            ['id_estado' => 2, 'estado' => 'ANZOÁNTEGUI', 'iso_3166' => 'VE-B'],
            ['id_estado' => 3, 'estado' => 'APURE', 'iso_3166' => 'VE-C'],
            ['id_estado' => 4, 'estado' => 'ARAGUA', 'iso_3166' => 'VE-D'],
            ['id_estado' => 5, 'estado' => 'BARINAS', 'iso_3166' => 'VE-E'],
            ['id_estado' => 6, 'estado' => 'BOLÍVAR', 'iso_3166' => 'VE-F'],
            ['id_estado' => 7, 'estado' => 'CARABOBO', 'iso_3166' => 'VE-G'],
            ['id_estado' => 8, 'estado' => 'COJEDES', 'iso_3166' => 'VE-H'],
            ['id_estado' => 9, 'estado' => 'DELTA AMACURO', 'iso_3166' => 'VE-Y'],
            ['id_estado' => 10, 'estado' => 'FALCÓN', 'iso_3166' => 'VE-I'],
            ['id_estado' => 11, 'estado' => 'GUÁRICO', 'iso_3166' => 'VE-J'],
            ['id_estado' => 12, 'estado' => 'LARA', 'iso_3166' => 'VE-K'],
            ['id_estado' => 13, 'estado' => 'MÉRIDA', 'iso_3166' => 'VE-L'],
            ['id_estado' => 14, 'estado' => 'MIRANDA', 'iso_3166' => 'VE-M'],
            ['id_estado' => 15, 'estado' => 'MONAGAS', 'iso_3166' => 'VE-N'],
            ['id_estado' => 16, 'estado' => 'NUEVA ESPARTA', 'iso_3166' => 'VE-O'],
            ['id_estado' => 17, 'estado' => 'PORTUGUESA', 'iso_3166' => 'VE-P'],
            ['id_estado' => 18, 'estado' => 'SUCRE', 'iso_3166' => 'VE-R'],
            ['id_estado' => 19, 'estado' => 'TÁCHIRA', 'iso_3166' => 'VE-S'],
            ['id_estado' => 20, 'estado' => 'TRUJILLO', 'iso_3166' => 'VE-T'],
            ['id_estado' => 21, 'estado' => 'LA GUAIRA', 'iso_3166' => 'VE-W'],
            ['id_estado' => 22, 'estado' => 'YARACUY', 'iso_3166' => 'VE-U'],
            ['id_estado' => 23, 'estado' => 'ZULIA', 'iso_3166' => 'VE-V'],
            ['id_estado' => 24, 'estado' => 'DISTRITO CAPITAL', 'iso_3166' => 'VE-A'],
            ['id_estado' => 25, 'estado' => 'DEPENDENCIAS FEDERALES', 'iso_3166' => 'VE-Z'],
        ];
        
        DB::table('estados')->insert($estados);
    }
}
