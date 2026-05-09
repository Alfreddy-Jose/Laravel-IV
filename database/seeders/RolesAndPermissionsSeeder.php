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

        // Permisos de Matricula
        Permission::firstOrCreate(['name' => 'matricula.crear']);
        Permission::firstOrCreate(['name' => 'matricula.editar']);
        Permission::firstOrCreate(['name' => 'matricula.eliminar']);
        Permission::firstOrCreate(['name' => 'matricula.ver']);

        // Pemisos de Trayecto
        Permission::firstOrCreate(['name' => 'trayecto.crear']);
        Permission::firstOrCreate(['name' => 'trayecto.editar']);
        Permission::firstOrCreate(['name' => 'trayecto.eliminar']);
        Permission::firstOrCreate(['name' => 'trayecto.ver']);

        //  Crear rol de super-admin
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        // Asignar todos los permisos al rol de super-admin
        $adminRole->givePermissionTo(Permission::all());
    }
}
