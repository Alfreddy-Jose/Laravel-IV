<?php

namespace Database\Seeders;

use App\Models\TipoLapso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoLapsosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoLapso::firstOrCreate([
            'id' => '4',
            'nombre' => 'REGULAR',
        ]);
        TipoLapso::firstOrCreate([
            'id' => '5',
            'nombre' => 'INTENSIVO',
        ]);
        TipoLapso::firstOrCreate([
            'id' => '7',
            'nombre' => 'PIU',
        ]);
    }
}
