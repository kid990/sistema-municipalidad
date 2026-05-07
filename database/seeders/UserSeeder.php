<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario Administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@muni.com',
            'password' => Hash::make('password'),
            'rol' => UserRole::ADMIN,
            'email_verified_at' => now(),
        ]);

        // Crear usuario Registrador
        User::create([
            'name' => 'Registrador Municipal',
            'email' => 'registrador@muni.com',
            'password' => Hash::make('password'),
            'rol' => UserRole::REGISTRADOR,
            'email_verified_at' => now(),
        ]);

        // Crear usuario Tesorero
        User::create([
            'name' => 'Tesorero Municipal',
            'email' => 'tesorero@muni.com',
            'password' => Hash::make('password'),
            'rol' => UserRole::TESORERO,
            'email_verified_at' => now(),
        ]);
    }
}
