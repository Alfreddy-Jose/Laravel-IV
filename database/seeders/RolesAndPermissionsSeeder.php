<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

        // Permisos de PNF
        Permission::firstOrCreate(['name' => 'pnf.crear']);
        Permission::firstOrCreate(['name' => 'pnf.editar']);
        Permission::firstOrCreate(['name' => 'pnf.eliminar']);
        Permission::firstOrCreate(['name' => 'pnf.ver']);

        // Permisos de Sede
        Permission::firstOrCreate(['name' => 'sede.crear']);
        Permission::firstOrCreate(['name' => 'sede.editar']);
        Permission::firstOrCreate(['name' => 'sede.eliminar']);
        Permission::firstOrCreate(['name' => 'sede.ver']);

        // Permisos de Lapso
        Permission::firstOrCreate(['name' => 'lapso.crear']);
        Permission::firstOrCreate(['name' => 'lapso.editar']);
        Permission::firstOrCreate(['name' => 'lapso.eliminar']);
        Permission::firstOrCreate(['name' => 'lapso.ver']);

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

        // Pemisos de Trayecto
        Permission::firstOrCreate(['name' => 'trayecto.crear']);
        Permission::firstOrCreate(['name' => 'trayecto.editar']);
        Permission::firstOrCreate(['name' => 'trayecto.eliminar']);
        Permission::firstOrCreate(['name' => 'trayecto.ver']);

        // Permisos de Inventario
        Permission::firstOrCreate(['name' => 'inventario.crear']);
        Permission::firstOrCreate(['name' => 'inventario.editar']);
        Permission::firstOrCreate(['name' => 'inventario.eliminar']);
        Permission::firstOrCreate(['name' => 'inventario.ver']);

        // Permisos de Seccion
        Permission::firstOrCreate(['name' => 'seccion.crear']);
        Permission::firstOrCreate(['name' => 'seccion.editar']);
        Permission::firstOrCreate(['name' => 'seccion.eliminar']);
        Permission::firstOrCreate(['name' => 'seccion.ver']);

        // Permisos de Persona
        Permission::firstOrCreate(['name' => 'persona.crear']);
        Permission::firstOrCreate(['name' => 'persona.editar']);
        Permission::firstOrCreate(['name' => 'persona.eliminar']);
        Permission::firstOrCreate(['name' => 'persona.ver']);

        // Permisos de Docente
        Permission::firstOrCreate(['name' => 'docente.crear']);
        Permission::firstOrCreate(['name' => 'docente.editar']);
        Permission::firstOrCreate(['name' => 'docente.eliminar']);
        Permission::firstOrCreate(['name' => 'docente.ver']);

        // Permisos de Coordinador
        Permission::firstOrCreate(['name' => 'coordinador.crear']);
        Permission::firstOrCreate(['name' => 'coordinador.editar']);
        Permission::firstOrCreate(['name' => 'coordinador.eliminar']);
        Permission::firstOrCreate(['name' => 'coordinador.ver']);

        // Permisos de Vocero
        Permission::firstOrCreate(['name' => 'vocero.crear']);
        Permission::firstOrCreate(['name' => 'vocero.editar']);
        Permission::firstOrCreate(['name' => 'vocero.eliminar']);
        Permission::firstOrCreate(['name' => 'vocero.ver']);

        //  Crear rol de super-admin
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        // Asignar todos los permisos al rol de super-admin
        $adminRole->givePermissionTo(Permission::all());
    }
}
