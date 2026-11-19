<?php

namespace Database\Seeders;

use App\Models\UnidadCurricular;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            EstadosTableSeeder::class,
            MunicipiosTableSeeder::class,
            UniversidadSeeder::class,
            TrayectoSeeder::class,
            MatriculaSeeder::class,
            TipoLapsosSeeder::class,
            AnoAcademicoSeeder::class,
            LapsoSeeder::class,
            PnfSeeder::class,
            UnidadCurricularSeeder::class,
            SedeSeeder::class,
            EspaciosSeeder::class,
            RolesAndPermissionsSeeder::class,
            UsuarioSeeder::class,
            SeccionSeeder::class,
            PersonaSeeder::class,
            DocenteSeeder::class,
            EstadisticaInscripcionesSeeder::class
        ]);
    }
}
