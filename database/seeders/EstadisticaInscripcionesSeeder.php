<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadisticaInscripcion;
use Illuminate\Support\Facades\DB;

class EstadisticaInscripcionesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener el lapso activo del sistema
        $lapso = DB::table('lapso_academicos')->where('status', 'true')->first();
        $lapsoId = $lapso ? $lapso->id : 1;

        // 2. Obtener los IDs reales buscando por el nombre exacto de tu UnidadCurricularSeeder
        $matematicaId   = DB::table('unidad_curriculars')->where('nombre', 'MATEMÁTICA I')->value('id');
        $electivaId     = DB::table('unidad_curriculars')->where('nombre', 'ELECTIVA I')->value('id');
        $proyectoId     = DB::table('unidad_curriculars')->where('nombre', 'PROYECTO SOCIOTECNOLOGICO I')->value('id');
        $algoritmicaId  = DB::table('unidad_curriculars')->where('nombre', 'ALGORITMICA Y PROGRAMACION I')->value('id');
        $arquitecturaId = DB::table('unidad_curriculars')->where('nombre', 'ARQUITECTURA DEL COMPUTADOR')->value('id');
        $criticaId      = DB::table('unidad_curriculars')->where('nombre', 'FORMACIÓN CRITICA I')->value('id');
        $idiomaId       = DB::table('unidad_curriculars')->where('nombre', 'IDIOMA I')->value('id');
        $acreditableId  = DB::table('unidad_curriculars')->where('nombre', 'UNIDAD ACREDITABLE I')->value('id');

        //--------------------------------------------------------------------------
        // DATOS REALES DE LA SECCIÓN: 751101 (Matrícula lineal de 27 alumnos)
        //--------------------------------------------------------------------------
        if ($matematicaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $matematicaId,
                'seccion'              => '751101',
                'cantidad_estudiantes' => 27
            ]);
        }

        if ($arquitecturaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $arquitecturaId,
                'seccion'              => '751101',
                'cantidad_estudiantes' => 27
            ]);
        }

        if ($criticaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $criticaId,
                'seccion'              => '751101',
                'cantidad_estudiantes' => 27
            ]);
        }

        if ($algoritmicaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $algoritmicaId,
                'seccion'              => '751101',
                'cantidad_estudiantes' => 27
            ]);
        }

        if ($proyectoId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $proyectoId,
                'seccion'              => '751101',
                'cantidad_estudiantes' => 27
            ]);
        }

        if ($idiomaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $idiomaId,
                'seccion'              => '751101',
                'cantidad_estudiantes' => 27
            ]);
        }

        if ($electivaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $electivaId,
                'seccion'              => '751101',
                'cantidad_estudiantes' => 27
            ]);
        }

        if ($acreditableId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $acreditableId,
                'seccion'              => '751101',
                'cantidad_estudiantes' => 27
            ]);
        }


        //--------------------------------------------------------------------------
        // DATOS REALES DE LA SECCIÓN: 751301 (Matrícula variable de 34 y 35 alumnos)
        //--------------------------------------------------------------------------
        if ($matematicaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $matematicaId,
                'seccion'              => '751301',
                'cantidad_estudiantes' => 35
            ]);
        }

        if ($arquitecturaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $arquitecturaId,
                'seccion'              => '751301',
                'cantidad_estudiantes' => 34
            ]);
        }

        if ($criticaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $criticaId,
                'seccion'              => '751301',
                'cantidad_estudiantes' => 34
            ]);
        }

        if ($algoritmicaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $algoritmicaId,
                'seccion'              => '751301',
                'cantidad_estudiantes' => 35
            ]);
        }

        if ($proyectoId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $proyectoId,
                'seccion'              => '751301',
                'cantidad_estudiantes' => 34
            ]);
        }

        if ($idiomaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $idiomaId,
                'seccion'              => '751301',
                'cantidad_estudiantes' => 34
            ]);
        }

        if ($electivaId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $electivaId,
                'seccion'              => '751301',
                'cantidad_estudiantes' => 34
            ]);
        }

        if ($acreditableId) {
            EstadisticaInscripcion::firstOrCreate([
                'lapso_id'             => $lapsoId,
                'unidad_curricular_id' => $acreditableId,
                'seccion'              => '751301',
                'cantidad_estudiantes' => 34
            ]);
        }
    }
}