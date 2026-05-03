<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
/*           EstadoSeeder::class,
            MunicipioSeeder::class,
            RolesAndPermissionsSeeder::class,
            UsersSeeder::class,
            UniversidadSeeder::class,
            PnfSeeder::class,*/

            EstadoSeeder::class,
            MunicipioSeeder::class,
            UniversidadSeeder::class,
            // TrayectoSeeder::class,
            MatriculaSeeder::class,
            TipoLapsosSeeder::class,
            // PersonaSeeder::class,
            // UnidadCurricularSeeder::class,
            LapsoSeeder::class,
            PnfSeeder::class,
            // SedeSeeder::class,
            // EspaciosSeeder::class,
            RolesAndPermissionsSeeder::class,
            UsersSeeder::class,
            // DocenteSeeder::class, 
            // SeccionSeeder::class 
        ]);
    }
}
