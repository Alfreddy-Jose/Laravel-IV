<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoDocumento::firstOrCreate(['nombre' => 'Plan de Evaluación', 'alcance' => 'seccion']);
        TipoDocumento::firstOrCreate(['nombre' => 'Declaración Jurada', 'alcance' => 'docente']);
        TipoDocumento::firstOrCreate(['nombre' => 'Ficha de Proyecto', 'alcance' => 'seccion']);
    }
}
