<?php

namespace Database\Seeders;

use App\Models\Matricula;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MatriculaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Matricula::firstOrcreate([
            'numero' => '5',
            'nombre' => 'REGULAR'
        ]);

        Matricula::firstOrcreate([
            'numero' => '8', 
            'nombre' => 'PROSECUCION'
        ]);
    }
}
