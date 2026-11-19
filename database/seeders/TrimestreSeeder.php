<?php

namespace Database\Seeders;

use App\Models\Trimestre;
use App\Models\Trayecto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrimestreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapeo de nombres de trimestres a sus números relativos y trayectos
        $trimestresData = [
            // Trayecto 1
            ['nombre' => 'I', 'numero_relativo' => 1, 'trayecto_id' => 1],
            ['nombre' => 'II', 'numero_relativo' => 2, 'trayecto_id' => 1],
            ['nombre' => 'III', 'numero_relativo' => 3, 'trayecto_id' => 1],
            
            // Trayecto 2
            ['nombre' => 'IV', 'numero_relativo' => 1, 'trayecto_id' => 2],
            ['nombre' => 'V', 'numero_relativo' => 2, 'trayecto_id' => 2],
            ['nombre' => 'VI', 'numero_relativo' => 3, 'trayecto_id' => 2],
            
            // Trayecto 3
            ['nombre' => 'VII', 'numero_relativo' => 1, 'trayecto_id' => 3],
            ['nombre' => 'VIII', 'numero_relativo' => 2, 'trayecto_id' => 3],
            ['nombre' => 'IX', 'numero_relativo' => 3, 'trayecto_id' => 3],
            
            // Trayecto 4
            ['nombre' => 'X', 'numero_relativo' => 1, 'trayecto_id' => 4],
            ['nombre' => 'XI', 'numero_relativo' => 2, 'trayecto_id' => 4],
            ['nombre' => 'XII', 'numero_relativo' => 3, 'trayecto_id' => 4],
        ];

        foreach ($trimestresData as $trimestreData) {
            Trimestre::updateOrCreate(
                [
                    'nombre' => $trimestreData['nombre'],
                    'trayecto_id' => $trimestreData['trayecto_id']
                ],
                [
                    'numero_relativo' => $trimestreData['numero_relativo']
                ]
            );
        }

        // Si hay más trayectos creados después del 4, crear sus trimestres automáticamente
        $trayectosAdicionales = Trayecto::where('id', '>', 4)->get();
        
        foreach ($trayectosAdicionales as $trayecto) {
            $this->crearTrimestresParaTrayecto($trayecto);
        }
    }

    /**
     * Crear trimestres para un trayecto específico
     */
    private function crearTrimestresParaTrayecto($trayecto)
    {
        $nombresReales = [
            1 => ['I', 'II', 'III'],
            2 => ['IV', 'V', 'VI'],
            3 => ['VII', 'VIII', 'IX'],
            4 => ['X', 'XI', 'XII']
        ];

        // Para trayectos mayores a 4, usar un patrón similar
        $baseIndex = (($trayecto->id - 1) * 3) + 1;
        
        for ($i = 1; $i <= 3; $i++) {
            $nombreReal = $nombresReales[$trayecto->id][$i - 1] ?? "Trimestre " . ($baseIndex + $i - 1);
            
            Trimestre::updateOrCreate(
                [
                    'nombre' => $nombreReal,
                    'trayecto_id' => $trayecto->id
                ],
                [
                    'numero_relativo' => $i
                ]
            );
        }
    }
}