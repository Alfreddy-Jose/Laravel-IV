<?php

namespace Database\Seeders;

use App\Models\Seccion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 1,
            'matricula_id' => 1,
            'numero_seccion' => 1,
            'nombre' => '0751501'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 1,
            'matricula_id' => 1,
            'numero_seccion' => 2,
            'nombre' => '0751502'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 1,
            'matricula_id' => 1,
            'numero_seccion' => 3,
            'nombre' => '0751503'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 1,
            'matricula_id' => 1,
            'numero_seccion' => 3,
            'nombre' => '0751503'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 1,
            'matricula_id' => 1,
            'numero_seccion' => 4,
            'nombre' => '0751504'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 2,
            'matricula_id' => 1,
            'numero_seccion' => 1,
            'nombre' => '0752501'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 2,
            'matricula_id' => 1,
            'numero_seccion' => 2,
            'nombre' => '0752502'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 2,
            'matricula_id' => 1,
            'numero_seccion' => 3,
            'nombre' => '0752503'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 3,
            'matricula_id' => 1,
            'numero_seccion' => 1,
            'nombre' => '0753501'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 3,
            'matricula_id' => 2,
            'numero_seccion' => 2,
            'nombre' => '0783502'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 1,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 4,
            'matricula_id' => 1,
            'numero_seccion' => 1,
            'nombre' => '0754501'
        ]);

        Seccion::firstOrCreate([
            'pnf_id' => 2,
            'lapso_id' => 1,
            'sede_id' => 1,
            'trayecto_id' => 4,
            'matricula_id' => 1,
            'numero_seccion' => 1,
            'nombre' => '0854501'
        ]);
    }
}
