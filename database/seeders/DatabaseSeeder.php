<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Caja;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        $users = [
            ['name' => 'Admin User', 'email' => 'admin@test.com', 'role' => 'admin'],
            ['name' => 'Deposito User', 'email' => 'deposito@test.com', 'role' => 'deposito'],
            ['name' => 'Tecnico User', 'email' => 'tecnico@test.com', 'role' => 'tecnico'],
            ['name' => 'Consumo User', 'email' => 'consumo@test.com', 'role' => 'consumo'],
            ['name' => 'Acondicionador User', 'email' => 'acondicionador@test.com', 'role' => 'acondicionador'],
        ];

        foreach ($users as $u) {
            User::create([
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => Hash::make('password'),
                'role' => $u['role'],
            ]);
        }

        // Boxes
        $boxes = [
            ['nombre' => 'Caja Instrumental 1', 'codigo_interno' => 'INST-001', 'estado' => 'DISPONIBLE'],
            ['nombre' => 'Caja Instrumental 2', 'codigo_interno' => 'INST-002', 'estado' => 'DISPONIBLE'],
            ['nombre' => 'Motor Quirúrgico A', 'codigo_interno' => 'MOT-001', 'estado' => 'DISPONIBLE'],
            ['nombre' => 'Set Traumatología', 'codigo_interno' => 'TRAU-001', 'estado' => 'DISPONIBLE'],
        ];

        foreach ($boxes as $b) {
            Caja::create($b);
        }
    }
}
