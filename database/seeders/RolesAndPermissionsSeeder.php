<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Permisos de Usuario
        Permission::firstOrCreate(['name' => 'usuario.crear']);
        Permission::firstOrCreate(['name' => 'usuario.editar']);
        Permission::firstOrCreate(['name' => 'usuario.eliminar']);
        Permission::firstOrCreate(['name' => 'usuario.ver']);
        // Permisos rol
        Permission::firstOrCreate(['name' => 'rol.crear']);
        Permission::firstOrCreate(['name' => 'rol.editar']);
        Permission::firstOrCreate(['name' => 'rol.eliminar']);
        Permission::firstOrCreate(['name' => 'rol.ver']);
        // Permisos pnf
        Permission::firstOrCreate(['name' => 'pnf.crear']);
        Permission::firstOrCreate(['name' => 'pnf.editar']);
        Permission::firstOrCreate(['name' => 'pnf.eliminar']);
        Permission::firstOrCreate(['name' => 'pnf.ver']);
        Permission::firstOrCreate(['name' => 'pnf.pdf']);
        // Permisos sede
        Permission::firstOrCreate(['name' => 'sede.crear']);
        Permission::firstOrCreate(['name' => 'sede.editar']);
        Permission::firstOrCreate(['name' => 'sede.eliminar']);
        Permission::firstOrCreate(['name' => 'sede.ver']);
        Permission::firstOrCreate(['name' => 'sede.pdf']);
        //Permisos lapso
        Permission::firstOrCreate(['name' => 'lapso.crear']);
        Permission::firstOrCreate(['name' => 'lapso.editar']);
        Permission::firstOrCreate(['name' => 'lapso.eliminar']);
        Permission::firstOrCreate(['name' => 'lapso.ver']);
        Permission::firstOrCreate(['name' => 'lapso.pdf']);
        Permission::firstOrCreate(['name' => 'lapso.cambiar estado']);
        //Permisos trayecto
        Permission::firstOrCreate(['name' => 'trayecto.crear']);
        Permission::firstOrCreate(['name' => 'trayecto.editar']);
        Permission::firstOrCreate(['name' => 'trayecto.eliminar']);
        Permission::firstOrCreate(['name' => 'trayecto.ver']);
        Permission::firstOrCreate(['name' => 'trayecto.pdf']);
        //Permisos unidad curricular
        Permission::firstOrCreate(['name' => 'unidad Curricular.crear']);
        Permission::firstOrCreate(['name' => 'unidad Curricular.editar']);
        Permission::firstOrCreate(['name' => 'unidad Curricular.eliminar']);
        Permission::firstOrCreate(['name' => 'unidad Curricular.ver']);
        Permission::firstOrCreate(['name' => 'unidad Curricular.pdf']);
        // Permisos tipo de matricula
        Permission::firstOrCreate(['name' => 'Tipo Matricula.crear']);
        Permission::firstOrCreate(['name' => 'Tipo Matricula.editar']);
        Permission::firstOrCreate(['name' => 'Tipo Matricula.eliminar']);
        Permission::firstOrCreate(['name' => 'Tipo Matricula.ver']);
        Permission::firstOrCreate(['name' => 'Tipo Matricula.pdf']);
        // Permisos seccion
        Permission::firstOrCreate(['name' => 'seccion.crear']);
        Permission::firstOrCreate(['name' => 'seccion.editar']);
        Permission::firstOrCreate(['name' => 'seccion.eliminar']);
        Permission::firstOrCreate(['name' => 'seccion.ver']);
        Permission::firstOrCreate(['name' => 'seccion.pdf']);
        // Permisos aulas
        Permission::firstOrCreate(['name' => 'aula.crear']);
        Permission::firstOrCreate(['name' => 'aula.editar']);
        Permission::firstOrCreate(['name' => 'aula.eliminar']);
        Permission::firstOrCreate(['name' => 'aula.ver']);
        Permission::firstOrCreate(['name' => 'aula.pdf']);
        Permission::firstOrCreate(['name' => 'aula.importar excel']);
        // Permisos laboratorios
        Permission::firstOrCreate(['name' => 'laboratorio.crear']);
        Permission::firstOrCreate(['name' => 'laboratorio.editar']);
        Permission::firstOrCreate(['name' => 'laboratorio.eliminar']);
        Permission::firstOrCreate(['name' => 'laboratorio.ver']);
        Permission::firstOrCreate(['name' => 'laboratorio.pdf']);
        Permission::firstOrCreate(['name' => 'laboratorio.importar excel']);
        // Permisos turno
        Permission::firstOrCreate(['name' => 'turno.crear']);
        Permission::firstOrCreate(['name' => 'turno.editar']);
        Permission::firstOrCreate(['name' => 'turno.eliminar']);
        Permission::firstOrCreate(['name' => 'turno.ver']);
        Permission::firstOrCreate(['name' => 'turno.pdf']);
        // Permisos personas
        Permission::firstOrCreate(['name' => 'persona.crear']);
        Permission::firstOrCreate(['name' => 'persona.editar']);
        Permission::firstOrCreate(['name' => 'persona.eliminar']);
        Permission::firstOrCreate(['name' => 'persona.ver']);
        Permission::firstOrCreate(['name' => 'persona.pdf']);
        // Permisos docentes
        Permission::firstOrCreate(['name' => 'docente.crear']);
        Permission::firstOrCreate(['name' => 'docente.editar']);
        Permission::firstOrCreate(['name' => 'docente.eliminar']);
        Permission::firstOrCreate(['name' => 'docente.ver']);
        Permission::firstOrCreate(['name' => 'docente.pdf']);
        // Permisos coordinador municipales
        Permission::firstOrCreate(['name' => 'coordinador.crear']);
        Permission::firstOrCreate(['name' => 'coordinador.editar']);
        Permission::firstOrCreate(['name' => 'coordinador.eliminar']);
        Permission::firstOrCreate(['name' => 'coordinador.ver']);
        Permission::firstOrCreate(['name' => 'coordinador.pdf']);
        // Permisos de Universisdad
        Permission::firstOrCreate(['name' => 'universidad.crear']);
        Permission::firstOrCreate(['name' => 'universidad.editar']);
        Permission::firstOrCreate(['name' => 'universidad.ver']);
        // Permisos vocero
        Permission::firstOrCreate(['name' => 'vocero.crear']);
        Permission::firstOrCreate(['name' => 'vocero.editar']);
        Permission::firstOrCreate(['name' => 'vocero.eliminar']);
        Permission::firstOrCreate(['name' => 'vocero.ver']);
        Permission::firstOrCreate(['name' => 'vocero.pdf']);
        // Permisos de Horarios
        Permission::firstOrCreate(['name' => 'horario.crear']);
        Permission::firstOrCreate(['name' => 'horario.editar']);
        Permission::firstOrCreate(['name' => 'horario.eliminar']);
        Permission::firstOrCreate(['name' => 'horario.ver']);
        Permission::firstOrCreate(['name' => 'horario.pdf']);
        // Permisos de Horarios de Docentes
        Permission::firstOrCreate(['name' => 'horario_docente.crear']);
        Permission::firstOrCreate(['name' => 'horario_docente.editar']);
        Permission::firstOrCreate(['name' => 'horario_docente.eliminar']);
        Permission::firstOrCreate(['name' => 'horario_docente.ver']);
        Permission::firstOrCreate(['name' => 'horario_docente.pdf']);

        // Permisos de Inventario
        Permission::firstOrCreate(['name' => 'inventario.crear']);
        Permission::firstOrCreate(['name' => 'inventario.editar']);
        Permission::firstOrCreate(['name' => 'inventario.eliminar']);
        Permission::firstOrCreate(['name' => 'inventario.ver']);

        // Permisos de Años Académicos
        Permission::firstOrCreate(['name' => 'anoAcademico.crear']);
        Permission::firstOrCreate(['name' => 'anoAcademico.editar']);
        Permission::firstOrCreate(['name' => 'anoAcademico.eliminar']);
        Permission::firstOrCreate(['name' => 'anoAcademico.ver']);

        // Permisos de Proyección Académica
        Permission::firstOrCreate(['name' => 'proyeccion.crear']);
        Permission::firstOrCreate(['name' => 'proyeccion.editar']);
        Permission::firstOrCreate(['name' => 'proyeccion.eliminar']);
        Permission::firstOrCreate(['name' => 'proyeccion.ver']);

        // Permisos de Parametros Proyección
        Permission::firstOrCreate(['name' => 'parametro.crear']);
        Permission::firstOrCreate(['name' => 'parametro.editar']);
        Permission::firstOrCreate(['name' => 'parametro.eliminar']);
        Permission::firstOrCreate(['name' => 'parametro.ver']);

        // Permisos de Documentos
        Permission::firstOrCreate(['name' => 'documento.ver']);
        Permission::firstOrCreate(['name' => 'documento.crear']);
        Permission::firstOrCreate(['name' => 'documento.editar']);
        Permission::firstOrCreate(['name' => 'documento.eliminar']);

        // Permisos seccion
        Permission::firstOrCreate(['name' => 'censo.crear']);
        Permission::firstOrCreate(['name' => 'censo.editar']);
        Permission::firstOrCreate(['name' => 'censo.eliminar']);
        Permission::firstOrCreate(['name' => 'censo.ver']);
        Permission::firstOrCreate(['name' => 'censo.pdf']);

        Permission::firstOrCreate(['name' => 'entrega.crear']);
        Permission::firstOrCreate(['name' => 'entrega.editar']);
        Permission::firstOrCreate(['name' => 'entrega.eliminar']);
        Permission::firstOrCreate(['name' => 'entrega.ver']);
        Permission::firstOrCreate(['name' => 'entrega.pdf']);

        // Permisos de Per
        Permission::firstOrCreate(['name' => 'per.crear']);
        Permission::firstOrCreate(['name' => 'per.editar']);
        Permission::firstOrCreate(['name' => 'per.eliminar']);
        Permission::firstOrCreate(['name' => 'per.ver']);
        Permission::firstOrCreate(['name' => 'per.pdf']);

        // Permisos de Parametros Estadistica inscripcion
        Permission::firstOrCreate(['name' => 'estadistica.crear']);
        Permission::firstOrCreate(['name' => 'estadistica.editar']);
        Permission::firstOrCreate(['name' => 'estadistica.eliminar']);
        Permission::firstOrCreate(['name' => 'estadistica.ver']);

        // Permisos para ver Bitacora
        Permission::firstOrCreate(['name' => 'bitacora.ver']);

        // Crear Roles y asignar permisos

        //  Crear rol de  Administrador
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR']);
        // Asignar todos los permisos al rol de Administrador
        $adminRole->givePermissionTo(Permission::all());

        // Rol de Asistente
        $asistenteRole = Role::firstOrCreate(['name' => 'ASISTENTE']);
        // Asignar permisos al rol de Asistente
        $asistenteRole->givePermissionTo([
                'pnf.ver',
                'sede.ver',
                'lapso.ver',
                'lapso.ver',
                'trayecto.ver',
                'unidad Curricular.ver',
                'Tipo Matricula.ver',
                'seccion.ver',
                'aula.ver',
                'laboratorio.ver',
                'turno.ver',
                'persona.ver',
                'docente.ver',
                'coordinador.ver',
                'universidad.ver',
                'vocero.ver',
                'horario.ver',
                'horario_docente.ver',
        ]);

        // Rol de coordinador
        $coordinadorRole = Role::firstOrCreate(['name' => 'COORDINADOR']);
        // Asignar permisos al rol de coordinador podra hacer todo menos gestionar usuarios y roles
        $coordinadorRole->givePermissionTo([
                'unidad Curricular.crear',
                'unidad Curricular.editar',
                'unidad Curricular.eliminar',
                'unidad Curricular.ver',
                'unidad Curricular.pdf',
                'seccion.crear',
                'seccion.editar',
                'seccion.eliminar',
                'seccion.ver',
                'seccion.pdf',
                'aula.crear',
                'aula.editar',
                'aula.eliminar',
                'aula.ver',
                'aula.pdf',
                'laboratorio.crear',
                'laboratorio.editar',
                'laboratorio.eliminar',
                'laboratorio.ver',
                'laboratorio.pdf',
                'turno.crear',
                'turno.editar',
                'turno.eliminar',
                'turno.ver',
                'turno.pdf',
                'persona.crear',
                'persona.editar',
                'persona.eliminar',
                'persona.ver',
                'persona.pdf',
                'docente.crear',
                'docente.editar',
                'docente.eliminar',
                'docente.ver',
                'docente.pdf',
                'universidad.crear',
                'universidad.editar',
                'universidad.ver',
                'vocero.crear',
                'vocero.editar',
                'vocero.eliminar',
                'vocero.ver',
                'vocero.pdf',
                'horario.crear',
                'horario.editar',
                'horario.eliminar',
                'horario.ver',
                'horario.pdf',
                'horario_docente.crear',
                'horario_docente.editar',
                'horario_docente.eliminar',
                'horario_docente.ver',
                'horario_docente.pdf',
                'inventario.crear',
                'inventario.editar',
                'inventario.eliminar',
                'inventario.ver',
                'censo.crear',
                'censo.editar',
                'censo.eliminar',
                'censo.ver',
                'censo.pdf',
                'coordinador.crear',
                'coordinador.editar',
                'coordinador.eliminar',
                'coordinador.ver',
                'coordinador.pdf',
                'entrega.crear',
                'entrega.ver',
                'entrega.editar',
                'entrega.eliminar',
                'entrega.pdf',
                'estadistica.crear',
                'estadistica.ver',
                'estadistica.editar',
                'estadistica.eliminar',
                'proyeccion.crear',
                'proyeccion.editar',
                'proyeccion.eliminar',
                'proyeccion.ver',
        ]);
    }
}
