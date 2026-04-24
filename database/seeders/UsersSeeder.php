<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario super-admin
        $administrador = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'lastname' => 'Admin',
                'password' => Hash::make('12345678'),
            ]
        );
        $administrador->assignRole('ADMINISTRADOR');

        // Usuario Asistente
/*         $asistente = User::firstOrCreate(
            ['email' => 'ROMULO@GMAIL.COM'],
            [
                'name' => 'ROMULO',
                'password' => Hash::make('romulo123'),
            ]
        );
        $asistente->assignRole('ASISTENTE'); */
    }
}
