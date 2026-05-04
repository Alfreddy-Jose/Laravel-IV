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

        //  Crear rol de super-admin
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        // Asignar todos los permisos al rol de super-admin
        $adminRole->givePermissionTo(Permission::all());
    }
}
