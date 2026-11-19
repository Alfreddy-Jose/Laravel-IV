<?php

namespace Database\Seeders;

use App\Models\Trayecto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrayectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Trayecto::firstOrcreate([
            'nombre' => '1'
        ]);

        Trayecto::firstOrcreate([
            'nombre' => '2'
        ]);

        Trayecto::firstOrcreate([
            'nombre' => '3'
        ]); 

        Trayecto::firstOrcreate([
            'nombre' => '4'
        ]);
    }
}
