<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Trayecto;

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
