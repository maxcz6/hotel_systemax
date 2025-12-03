<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios con diferentes roles
        
        // 1. Usuario Administrador
        User::firstOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password123'),
                'role' => 'administrador',
            ]
        );

        // 2. Usuario Gerente
        User::firstOrCreate(
            ['email' => 'gerente@hotel.com'],
            [
                'name' => 'Gerente General',
                'password' => Hash::make('password123'),
                'role' => 'gerente',
            ]
        );

        // 3. Usuario Recepción
        User::firstOrCreate(
            ['email' => 'recepcion@hotel.com'],
            [
                'name' => 'Recepcionista 1',
                'password' => Hash::make('password123'),
                'role' => 'recepcion',
            ]
        );

        // 4. Usuario Limpieza
        User::firstOrCreate(
            ['email' => 'limpieza@hotel.com'],
            [
                'name' => 'Personal Limpieza',
                'password' => Hash::make('password123'),
                'role' => 'limpieza',
            ]
        );

        // 5. Usuario Mantenimiento
        User::firstOrCreate(
            ['email' => 'mantenimiento@hotel.com'],
            [
                'name' => 'Personal Mantenimiento',
                'password' => Hash::make('password123'),
                'role' => 'mantenimiento',
            ]
        );

        $this->command->info('✓ Usuarios creados:');
        $this->command->info('  - admin@hotel.com (password: password123) - ADMINISTRADOR');
        $this->command->info('  - gerente@hotel.com (password: password123) - GERENTE');
        $this->command->info('  - recepcion@hotel.com (password: password123) - RECEPCIÓN');
        $this->command->info('  - limpieza@hotel.com (password: password123) - LIMPIEZA');
        $this->command->info('  - mantenimiento@hotel.com (password: password123) - MANTENIMIENTO');
    }
}
