<?php

namespace Database\Seeders;

use App\Models\CondicionContrato;
use App\Models\Docente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $docente1 = Docente::firstOrCreate([
            'persona_id' => 1,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR',
            'horas_dedicacion' => 18
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 1,
            'fecha_inicio' => '2023-01-01',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o CONTRATADO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente1->unidades_curriculares()->sync([1, 18, 24]);

        $docente2 = Docente::firstOrCreate([
            'persona_id' => 2,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 2,
            'fecha_inicio' => '2022-02-02',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente2->unidades_curriculares()->sync([5, 2, 14, 20, 25, 19]);

        $docente3 = Docente::firstOrCreate([
            'persona_id' => 3,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 3,
            'fecha_inicio' => '2018-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente3->unidades_curriculares()->sync([3, 21]);

        $docente4 = Docente::firstOrCreate([
            'persona_id' => 4,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 4,
            'fecha_inicio' => '2018-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente4->unidades_curriculares()->sync([5, 25, 19, 27]);

        $docente5 = Docente::firstOrCreate([
            'persona_id' => 5,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 5,
            'fecha_inicio' => '2017-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente5->unidades_curriculares()->sync([30, 6]);

        $docente6 = Docente::firstOrCreate([
            'persona_id' => 6,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 6,
            'fecha_inicio' => '2017-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente6->unidades_curriculares()->sync([5, 6, 9, 13]);

        $docente7 = Docente::firstOrCreate([
            'persona_id' => 7,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 7,
            'fecha_inicio' => '2015-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente7->unidades_curriculares()->sync([4, 12, 10, 16, 15]);

        $docente8 = Docente::firstOrCreate([
            'persona_id' => 8,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 8,
            'fecha_inicio' => '2014-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente8->unidades_curriculares()->sync([1, 9,]);

        $docente9 = Docente::firstOrCreate([
            'persona_id' => 9,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 9,
            'fecha_inicio' => '2014-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente9->unidades_curriculares()->sync([4, 6, 13, 4]);

        $docente10 = Docente::firstOrCreate([
            'persona_id' => 10,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 10,
            'fecha_inicio' => '2013-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente10->unidades_curriculares()->sync([2, 6, 12, 10, 16, 22]);

        $docente11 = Docente::firstOrCreate([
            'persona_id' => 11,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 11,
            'fecha_inicio' => '2012-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente11->unidades_curriculares()->sync([1, 34, 33, 29]);

        $docente12 = Docente::firstOrCreate([
            'persona_id' => 12,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 12,
            'fecha_inicio' => '2012-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente12->unidades_curriculares()->sync([13, 15, 14, 35]);


        $docente13 = Docente::firstOrCreate([
            'persona_id' => 13,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 13,
            'fecha_inicio' => '2011-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente13->unidades_curriculares()->sync([5]);

        $docente14 = Docente::firstOrCreate([
            'persona_id' => 14,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 14,
            'fecha_inicio' => '2010-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente14->unidades_curriculares()->sync([2, 4]);

        $docente15 = Docente::firstOrCreate([
            'persona_id' => 15,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 15,
            'fecha_inicio' => '2010-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente15->unidades_curriculares()->sync([4, 2, 12, 10, 16]);

        $docente16 = Docente::firstOrCreate([
            'persona_id' => 16,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 16,
            'fecha_inicio' => '2015-01-16',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente16->unidades_curriculares()->sync([2, 3, 11]);

        $docente17 = Docente::firstOrCreate([
            'persona_id' => 17,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 17,
            'fecha_inicio' => '2017-01-10',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente17->unidades_curriculares()->sync([11, 21, 28]);

        $docente18 = Docente::firstOrCreate([
            'persona_id' => 18,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 18,
            'fecha_inicio' => '2014-01-18',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente18->unidades_curriculares()->sync([3, 11, 21]);

        $docente19 = Docente::firstOrCreate([
            'persona_id' => 19,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 19,
            'fecha_inicio' => '2018-01-18',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente19->unidades_curriculares()->sync([4]);

        $docente20 = Docente::firstOrCreate([
            'persona_id' => 20,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 20,
            'fecha_inicio' => '2010-01-20',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente20->unidades_curriculares()->sync([4, 6, 26, 36]);

        $docente21 = Docente::firstOrCreate([
            'persona_id' => 21,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 21,
            'fecha_inicio' => '2019-01-25',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente21->unidades_curriculares()->sync([7, 31]);

        $docente22 = Docente::firstOrCreate([
            'persona_id' => 22,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 22,
            'fecha_inicio' => '2019-01-24',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente22->unidades_curriculares()->sync([15]);

        $docente23 = Docente::firstOrCreate([
            'persona_id' => 23,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 23,
            'fecha_inicio' => '2010-01-16',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente23->unidades_curriculares()->sync([3, 11]);

        $docente24 = Docente::firstOrCreate([
            'persona_id' => 24,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 24,
            'fecha_inicio' => '2009-01-18',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente24->unidades_curriculares()->sync([13, 23]);

        $docente25 = Docente::firstOrCreate([
            'persona_id' => 25,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 25,
            'fecha_inicio' => '2011-01-17',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente25->unidades_curriculares()->sync([4, 6, 20, 27]);

        $docente26 = Docente::firstOrCreate([
            'persona_id' => 26,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 18 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 26,
            'fecha_inicio' => '2015-01-17',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'TIEMPO COMPLETO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente26->unidades_curriculares()->sync([5]);

        $docente27 = Docente::firstOrCreate([
            'persona_id' => 27,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 27,
            'fecha_inicio' => '2010-01-17',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente27->unidades_curriculares()->sync([12, 10, 16, 15, 23]);

        $docente28 = Docente::firstOrCreate([
            'persona_id' => 28,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 28,
            'fecha_inicio' => '2008-01-17',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente28->unidades_curriculares()->sync([8, 17]);

        $docente29 = Docente::firstOrCreate([
            'persona_id' => 29,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 29,
            'fecha_inicio' => '2011-01-18',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente29->unidades_curriculares()->sync([8]);

        $docente30 = Docente::firstOrCreate([
            'persona_id' => 30,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 30,
            'fecha_inicio' => '2015-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente30->unidades_curriculares()->sync([8, 17]);

        $docente31 = Docente::firstOrCreate([
            'persona_id' => 31,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 31,
            'fecha_inicio' => '2016-01-15',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente31->unidades_curriculares()->sync([13]);

        $docente32 = Docente::firstOrCreate([
            'persona_id' => 32,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 32,
            'fecha_inicio' => '2007-01-16',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente32->unidades_curriculares()->sync([1, 14]);

        $docente33 = Docente::firstOrCreate([
            'persona_id' => 33,
            'pnf_id' => 1,
            'categoria' => 'ASISTENTE', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 33,
            'fecha_inicio' => '2005-01-17',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'CONTRATADO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente33->unidades_curriculares()->sync([8, 17]);

        $docente34 = Docente::firstOrCreate([
            'persona_id' => 34,
            'pnf_id' => 1,
            'categoria' => 'INSTRUCTOR', // <-- Puede ser INSTRUCTOR o ASISTENTE
            'horas_dedicacion' => 12 // <-- Si es TIEMPO COMPLETO debe ser 18, sino 12
        ]);
        CondicionContrato::firstOrCreate([
            'docente_id' => 34,
            'fecha_inicio' => '2015-01-18',
            'fecha_fin' => '2025-12-31',
            'dedicacion' => 'MEDIO TIEMPO', // <-- Puede ser TIEMPO COMPLETO o MEDIO TIEMPO
            'tipo' => 'FIJO', // <-- Puede ser FIJO o CONTRATADO
        ]);
        $docente34->unidades_curriculares()->sync([6]);
    }
}
