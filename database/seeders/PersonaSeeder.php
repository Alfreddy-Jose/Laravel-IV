<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Persona;
use App\Models\Estudiante;
use App\Models\Seccion;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Persona::firstOrCreate([
            'cedula_persona' => '18098081',
            'nombre' => 'Carla',
            'apellido' => 'Alvarez',
            'telefono' => '04123728129',
            'email' => 'carla@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 409,
            'direccion' => 'El Arco'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '17321234',
            'nombre' => 'Carlos',
            'apellido' => 'Estephan',
            'telefono' => '04143823849',
            'email' => 'carlos@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 408,
            'direccion' => 'Casita Blanca'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '20938532',
            'nombre' => 'Dora',
            'apellido' => 'Mendoza',
            'telefono' => '04242841923',
            'email' => 'dora@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'DOCTORA',
            'municipio_id' => 415,
            'direccion' => 'En Frente Del Escuela'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '15273829',
            'nombre' => 'Tom',
            'apellido' => 'Zambrano',
            'telefono' => '04123838493',
            'email' => 'tom@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 413,
            'direccion' => 'Calle 13 Diagonal Al Zamán'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '15273830',
            'nombre' => 'Francisco',
            'apellido' => 'Garzo',
            'telefono' => '04123838495',
            'email' => 'francisco@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 414,
            'direccion' => 'Calle 15 Diagonal A La Plaza'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '20938535',
            'nombre' => 'Gustavo',
            'apellido' => 'Reina',
            'telefono' => '04242841927',
            'email' => 'gustavo@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'DOCTOR',
            'municipio_id' => 408,
            'direccion' => 'En Frente Del Liceo'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '192038429',
            'nombre' => 'Heimys',
            'apellido' => 'Alvarado',
            'telefono' => '04123728135',
            'email' => 'heimys@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 409,
            'direccion' => 'El Arco Nuevo'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '17901234',
            'nombre' => 'Hernan',
            'apellido' => 'Silvera',
            'telefono' => '04143823858',
            'email' => 'hernan@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 415,
            'direccion' => 'Casita Roja'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '14829421',
            'nombre' => 'Hernel',
            'apellido' => 'Mendez',
            'telefono' => '04124028129',
            'email' => 'hernel@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADO',
            'municipio_id' => 413,
            'direccion' => 'El Arco Grande'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '19321234',
            'nombre' => 'Isabel',
            'apellido' => 'Coronel',
            'telefono' => '04144523849',
            'email' => 'isabel@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'DOCTORA',
            'municipio_id' => 414,
            'direccion' => 'Lumar'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '182038421',
            'nombre' => 'Jackeline',
            'apellido' => 'Sevilla',
            'telefono' => '04125428129',
            'email' => 'jackeline@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 408,
            'direccion' => 'La Paz'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '17121534',
            'nombre' => 'Jennireef',
            'apellido' => 'Paiva',
            'telefono' => '04143878849',
            'email' => 'paiva@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 409,
            'direccion' => 'La Palma'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '202038421',
            'nombre' => 'Jessika',
            'apellido' => 'Hernandez',
            'telefono' => '04123728129',
            'email' => 'jessika@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 408,
            'direccion' => 'Calle Alberto'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '17401234',
            'nombre' => 'Juselly',
            'apellido' => 'Subero',
            'telefono' => '04143953849',
            'email' => 'juselly@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 409,
            'direccion' => 'Calle Roma'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '192238421',
            'nombre' => 'Leydis',
            'apellido' => 'Sequera',
            'telefono' => '04123776129',
            'email' => 'leydis@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 415,
            'direccion' => 'Guabina'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '19690234',
            'nombre' => 'Lidia',
            'apellido' => 'Cordero',
            'telefono' => '04143823999',
            'email' => 'lidia@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 413,
            'direccion' => 'Casa Blanca'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '192008421',
            'nombre' => 'Noraima',
            'apellido' => 'Merlo',
            'telefono' => '04123722129',
            'email' => 'noraima@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 414,
            'direccion' => 'La Aldea'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '19311234',
            'nombre' => 'Nulibet',
            'apellido' => 'Figueroa',
            'telefono' => '04143003849',
            'email' => 'nulibet@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 408,
            'direccion' => 'Las Cayenas'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '192038111',
            'nombre' => 'Oscar',
            'apellido' => 'Ochoa',
            'telefono' => '04163728129',
            'email' => 'oscar@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADO',
            'municipio_id' => 409,
            'direccion' => 'Aristides Bastidas'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '15621234',
            'nombre' => 'Pedro',
            'apellido' => 'Concepción',
            'telefono' => '04243823849',
            'email' => 'pedro@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 415,
            'direccion' => 'Casita Azul'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '192348421',
            'nombre' => 'Ranmer',
            'apellido' => 'Marchan',
            'telefono' => '04123728187',
            'email' => 'ramner@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADO',
            'municipio_id' => 413,
            'direccion' => 'La Aldeita'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '17322534',
            'nombre' => 'Roberth',
            'apellido' => 'Mujica',
            'telefono' => '04143822849',
            'email' => 'roberth@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 414,
            'direccion' => 'Camino Largo'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '213038421',
            'nombre' => 'Rosmery',
            'apellido' => 'Sanchez',
            'telefono' => '04241728129',
            'email' => 'rosmery@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 408,
            'direccion' => 'El Arco'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '18121234',
            'nombre' => 'Rover',
            'apellido' => 'Monserrat',
            'telefono' => '04143822749',
            'email' => 'rover@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 409,
            'direccion' => 'Agua Blanca'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '192038400',
            'nombre' => 'Yanys',
            'apellido' => 'Leon',
            'telefono' => '04263728129',
            'email' => 'yanys@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADO',
            'municipio_id' => 415,
            'direccion' => 'La Laguna'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '17329234',
            'nombre' => 'Yenny',
            'apellido' => 'Guevara',
            'telefono' => '04143823879',
            'email' => 'yenny@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 413,
            'direccion' => 'Casita Blanca'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '192038421',
            'nombre' => 'Yorfre',
            'apellido' => 'Gianbioni',
            'telefono' => '04126628129',
            'email' => 'yorfre@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 414,
            'direccion' => 'El Arco'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '19301234',
            'nombre' => 'Daymar',
            'apellido' => 'Rivero',
            'telefono' => '04143824049',
            'email' => 'daymar@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 408,
            'direccion' => 'Casita Blanca'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '201038421',
            'nombre' => 'Guiserly',
            'apellido' => 'Lagea',
            'telefono' => '04123799129',
            'email' => 'guiserly@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 415,
            'direccion' => 'La Cascada'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '18911234',
            'nombre' => 'Henry',
            'apellido' => 'Monterrey',
            'telefono' => '04243827749',
            'email' => 'henry@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 413,
            'direccion' => 'Agua Negra'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '192031121',
            'nombre' => 'Jeidys',
            'apellido' => 'Garrido',
            'telefono' => '04123008129',
            'email' => 'jeidys@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 414,
            'direccion' => 'El Arco'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '17321664',
            'nombre' => 'Kimberlyn',
            'apellido' => 'Pernia',
            'telefono' => '04143824449',
            'email' => 'kimberlyn@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 408,
            'direccion' => 'Calle La Palma Negra'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '22038421',
            'nombre' => 'Neyla',
            'apellido' => 'Parra',
            'telefono' => '04123728155',
            'email' => 'neyla@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'LICENCIADA',
            'municipio_id' => 415,
            'direccion' => 'El Arco'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '17122134',
            'nombre' => 'Yuleydi',
            'apellido' => 'Sanchez',
            'telefono' => '04240833849',
            'email' => 'yuleydi@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 413,
            'direccion' => 'Casita Amarilla'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '17122100',
            'nombre' => 'José',
            'apellido' => 'Alvarez',
            'telefono' => '04240831449',
            'email' => 'jose@gmail.com',
            'tipo_persona' => 'ESTUDIANTE',
            'grado_inst' => 'BACHILLER',
            'municipio_id' => 413,
            'direccion' => 'Casita Verde'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '19122100',
            'nombre' => 'Miguel',
            'apellido' => 'Gonzales',
            'telefono' => '04290831449',
            'email' => 'miguel@gmail.com',
            'tipo_persona' => 'ESTUDIANTE',
            'grado_inst' => 'BACHILLER',
            'municipio_id' => 413,
            'direccion' => 'Casita Azul'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '19002100',
            'nombre' => 'Jesus',
            'apellido' => 'Bermuda',
            'telefono' => '04290111449',
            'email' => 'jesus@gmail.com',
            'tipo_persona' => 'DOCENTE',
            'grado_inst' => 'INGENIERO',
            'municipio_id' => 413,
            'direccion' => 'Casita Frente Su Casa'
        ]);

        Persona::firstOrCreate([
            'cedula_persona' => '30968595',
            'nombre' => 'Alfredo',
            'apellido' => 'Colina',
            'telefono' => '04126209176',
            'email' => 'alfredo@gmail.com',
            'tipo_persona' => 'ESTUDIANTE',
            'grado_inst' => 'BACHILLER',
            'municipio_id' => 413,
            'direccion' => 'Esquina diagonal al escuela'
        ]);
        $estudiante3 = Estudiante::firstOrCreate([
            'persona_id' => 38,
        ]);
        $estudiante3->secciones()->syncWithoutDetaching([
            10 => [
                'estatus' => 'regular',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);


        Persona::firstOrCreate([
            'cedula_persona' => '3070446',
            'nombre' => 'Benjamín',
            'apellido' => 'Morillo',
            'telefono' => '04128251031',
            'email' => 'benjamin@gmail.com',
            'tipo_persona' => 'ESTUDIANTE',
            'grado_inst' => 'BACHILLER',
            'municipio_id' => 415,
            'direccion' => 'Al lado de la ferreteria Don quijote con av 5'
        ]);
        $estudiante1 = Estudiante::firstOrCreate([
            'persona_id' => 39,
        ]);
        $estudiante1->secciones()->syncWithoutDetaching([
            11 => [
                'estatus' => 'regular',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);


        Persona::firstOrCreate([
            'cedula_persona' => '30333212',
            'nombre' => 'Marcelo',
            'apellido' => 'Hurtado',
            'telefono' => '04123221112',
            'email' => 'marcelo@gmail.com',
            'tipo_persona' => 'ESTUDIANTE',
            'grado_inst' => 'BACHILLER',
            'municipio_id' => 415,
            'direccion' => '5 Av. con calle 21 casa #332'
        ]);
        $estudiante2 = Estudiante::firstOrCreate([
            'persona_id' => 40,
        ]);
        $estudiante2->secciones()->syncWithoutDetaching([
            11 => [
                'estatus' => 'regular',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

    }
}
