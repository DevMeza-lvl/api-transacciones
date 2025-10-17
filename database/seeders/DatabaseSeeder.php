<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@ejemplo.cl',
            'password' => '123',
            'saldo_inicial' => 10000,
            'saldo_actual' => 10000,
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Co-Admin',
            'email' => 'coadmin@ejemplo.cl',
            'password' => '1234',
            'saldo_inicial' => 10000,
            'saldo_actual' => 10000,
            'role' => 'admin',
        ]);
    }
}
