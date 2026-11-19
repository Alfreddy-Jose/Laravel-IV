<?php

namespace Database\Seeders;

use App\Models\UnidadCurricular;
use Illuminate\Database\Seeder;

class UnidadCurricularSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pnfId = 1;

        $unidades = [
            [
                'data' => [
                    'nombre' => 'MATEMÁTICA I',
                    'codigo' => 7004,
                    'descripcion' => '',
                    'unidad_credito' => 9,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [1, 2, 3]
            ],
            [
                'data' => [
                    'nombre' => 'ELECTIVA I',
                    'descripcion' => '',
                    'codigo' => 7010,
                    'unidad_credito' => 3,
                    'hora_teorica' => 3,
                    'hora_practica' => null,
                    'hora_total_est' => 3,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [1, 2, 3]
            ],
            [
                'data' => [
                    'nombre' => 'PROYECTO SOCIOTECNOLOGICO I',
                    'codigo' => 7008,
                    'descripcion' => '',
                    'unidad_credito' => 9,
                    'hora_teorica' => 6,
                    'hora_practica' => null,
                    'hora_total_est' => 6,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [1, 2, 3]
            ],
            [
                'data' => [
                    'nombre' => 'ALGORITMICA Y PROGRAMACION I',
                    'codigo' => 7007,
                    'descripcion' => '',
                    'unidad_credito' => 12,
                    'hora_teorica' => 6,
                    'hora_practica' => 3,
                    'hora_total_est' => 9,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [1, 2, 3]
            ],
            [
                'data' => [
                    'nombre' => 'ARQUITECTURA DEL COMPUTADOR',
                    'codigo' => 7005,
                    'descripcion' => '',
                    'unidad_credito' => 9,
                    'hora_teorica' => 5,
                    'hora_practica' => 3,
                    'hora_total_est' => 8,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [1, 2, 3]
            ],
            [
                'data' => [
                    'nombre' => 'FORMACIÓN CRITICA I',
                    'codigo' => 7006,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 2,
                    'hora_practica' => null,
                    'hora_total_est' => 2,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [1, 2, 3]
            ],
            [
                'data' => [
                    'nombre' => 'IDIOMA I',
                    'codigo' => 7009,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 2,
                    'hora_practica' => null,
                    'hora_total_est' => 2,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [1, 2, 3]
            ],
            [
                'data' => [
                    'nombre' => 'UNIDAD ACREDITABLE I',
                    'codigo' => 7037,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 2,
                    'hora_practica' => null,
                    'hora_total_est' => 2,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [1, 2, 3]
            ],
            [
                'data' => [
                    'nombre' => 'MATEMÁTICA II',
                    'codigo' => 7011,
                    'descripcion' => '',
                    'unidad_credito' => 6,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'SEMESTRAL',
                ],
                'trimestres' => [4, 5]
            ],
            [
                'data' => [
                    'nombre' => 'ELECTIVA II',
                    'codigo' => 7017,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 3,
                    'hora_practica' => null,
                    'hora_total_est' => 3,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [6]
            ],
            [
                'data' => [
                    'nombre' => 'PROYECTO SOCIOTECNOLOGICO II',
                    'codigo' => 7015,
                    'descripcion' => '',
                    'unidad_credito' => 9,
                    'hora_teorica' => 6,
                    'hora_practica' => null,
                    'hora_total_est' => 6,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [4, 5, 6]
            ],
            [
                'data' => [
                    'nombre' => 'BASE DE DATOS',
                    'codigo' => 7016,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [5]
            ],
            [
                'data' => [
                    'nombre' => 'REDES DEL COMPUTADOR',
                    'codigo' => 7012,
                    'descripcion' => '',
                    'unidad_credito' => 6,
                    'hora_teorica' => 5,
                    'hora_practica' => 3,
                    'hora_total_est' => 8,
                    'periodo' => 'SEMESTRAL',
                ],
                'trimestres' => [4, 5]
            ],
            [
                'data' => [
                    'nombre' => 'FORMACIÓN CRITICA II',
                    'codigo' => 751101,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 2,
                    'hora_practica' => null,
                    'hora_total_est' => 2,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [4, 5, 6]
            ],
            [
                'data' => [
                    'nombre' => 'PARADIGMA DE PROGRAMACION II',
                    'codigo' => 7013,
                    'descripcion' => '',
                    'unidad_credito' => 12,
                    'hora_teorica' => 6,
                    'hora_practica' => 3,
                    'hora_total_est' => 9,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [4, 5, 6]
            ],
            [
                'data' => [
                    'nombre' => 'INGENIERIA DEL SOFTWARE I',
                    'codigo' => 7019,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [4]
            ],
            [
                'data' => [
                    'nombre' => 'UNIDAD ACREDITABLE II',
                    'codigo' => 7038,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 2,
                    'hora_practica' => null,
                    'hora_total_est' => 2,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [4, 5, 6]
            ],
            [
                'data' => [
                    'nombre' => 'MATEMÁTICA APLICADA',
                    'codigo' => 7018,
                    'descripcion' => '',
                    'unidad_credito' => 6,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'SEMESTRAL',
                ],
                'trimestres' => [7, 8]
            ],
            [
                'data' => [
                    'nombre' => 'SISTEMA OPERATIVO',
                    'codigo' => 7023,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [7]
            ],
            [
                'data' => [
                    'nombre' => 'FORMACIÓN CRITICA III',
                    'codigo' => 7020,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 2,
                    'hora_practica' => null,
                    'hora_total_est' => 2,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [7, 8, 9]
            ],
            [
                'data' => [
                    'nombre' => 'PROYECTO SOCIOTECNOLOGICO III',
                    'codigo' => 7022,
                    'descripcion' => '',
                    'unidad_credito' => 9,
                    'hora_teorica' => 6,
                    'hora_practica' => null,
                    'hora_total_est' => 6,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [7, 8, 9]
            ],
            [
                'data' => [
                    'nombre' => 'INGENIERIA DEL SOFTWARE II',
                    'codigo' => 7021,
                    'descripcion' => '',
                    'unidad_credito' => 9,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [7, 8, 9]
            ],
            [
                'data' => [
                    'nombre' => 'ELECTIVA III',
                    'codigo' => 7024,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 6,
                    'hora_practica' => null,
                    'hora_total_est' => 6,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [7, 8, 9]
            ],
            [
                'data' => [
                    'nombre' => 'INVESTIGACIÓN DE OPERACIONES',
                    'codigo' => 7078,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [9]
            ],
            [
                'data' => [
                    'nombre' => 'MODELADO DE BASE DE DATOS',
                    'codigo' => 7079,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [8]
            ],
            [
                'data' => [
                    'nombre' => 'UNIDAD ACREDITABLE III',
                    'codigo' => 7039,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 2,
                    'hora_practica' => null,
                    'hora_total_est' => 2,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [7, 8, 9]
            ],
            [
                'data' => [
                    'nombre' => 'FORMACIÓN CRITICA IV',
                    'codigo' => 7027,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 2,
                    'hora_practica' => null,
                    'hora_total_est' => 2,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [10, 11, 12]
            ],
            [
                'data' => [
                    'nombre' => 'PROYECTO SOCIOTECNOLOGICO IV',
                    'codigo' => 7029,
                    'descripcion' => '',
                    'unidad_credito' => 9,
                    'hora_teorica' => 6,
                    'hora_practica' => null,
                    'hora_total_est' => 6,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [10, 11, 12]
            ],
            [
                'data' => [
                    'nombre' => 'SEGURIDAD INFORMATICA',
                    'codigo' => 7026,
                    'descripcion' => '',
                    'unidad_credito' => 4,
                    'hora_teorica' => 6,
                    'hora_practica' => null,
                    'hora_total_est' => 6,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [11]
            ],
            [
                'data' => [
                    'nombre' => 'ADMINISTRACIÓN DE BASE DE DATOS',
                    'codigo' => 7080,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [10]
            ],
            [
                'data' => [
                    'nombre' => 'IDIOMA II',
                    'codigo' => 7081,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 2,
                    'hora_practica' => null,
                    'hora_total_est' => 2,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [10, 11, 12]
            ],
            [
                'data' => [
                    'nombre' => 'REDES AVANZADAS',
                    'codigo' => 7082,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 5,
                    'hora_practica' => null,
                    'hora_total_est' => 5,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [11]
            ],
            [
                'data' => [
                    'nombre' => 'GESTION DE PROYECTO DE INFORMATICA',
                    'codigo' => 7083,
                    'descripcion' => '',
                    'unidad_credito' => 4,
                    'hora_teorica' => 6,
                    'hora_practica' => null,
                    'hora_total_est' => 6,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [10]
            ],
            [
                'data' => [
                    'nombre' => 'AUDITORIA INFORMATICA',
                    'codigo' => 7025,
                    'descripcion' => '',
                    'unidad_credito' => 4,
                    'hora_teorica' => 6,
                    'hora_practica' => null,
                    'hora_total_est' => 6,
                    'periodo' => 'TRIMESTRAL',
                ],
                'trimestres' => [12]
            ],
            [
                'data' => [
                    'nombre' => 'ELECTIVA IV',
                    'codigo' => 7031,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 6,
                    'hora_practica' => null,
                    'hora_total_est' => 6,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [10, 11, 12]
            ],
            [
                'data' => [
                    'nombre' => 'UNIDAD ACREDITABLE IV',
                    'codigo' => 7040,
                    'descripcion' => '',
                    'unidad_credito' => 3,
                    'hora_teorica' => 2,
                    'hora_practica' => null,
                    'hora_total_est' => 2,
                    'periodo' => 'ANUAL',
                ],
                'trimestres' => [10, 11, 12]
            ],
        ];

        foreach ($unidades as $item) {
            $unidad = UnidadCurricular::firstOrCreate(
                [
                    'nombre' => $item['data']['nombre'],
                    'pnf_id' => $pnfId,
                ],
                array_merge($item['data'], ['pnf_id' => $pnfId])
            );

            $unidad->trimestres()->sync($item['trimestres']);
        }
    }
}
