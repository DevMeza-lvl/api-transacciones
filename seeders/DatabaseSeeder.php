<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'password' => Hash::make('password123'),
                'saldo_inicial' => 1000.00,
                'saldo_actual' => 925.00,
            ],
            [
                'name' => 'María García',
                'email' => 'maria@example.com',
                'password' => Hash::make('password123'),
                'saldo_inicial' => 2500.50,
                'saldo_actual' => 2650.50,
            ],
            [
                'name' => 'Carlos López',
                'email' => 'carlos@example.com',
                'password' => Hash::make('password123'),
                'saldo_inicial' => 750.25,
                'saldo_actual' => 1025.75,
            ],
            [
                'name' => 'Ana Martínez',
                'email' => 'ana@example.com',
                'password' => Hash::make('password123'),
                'saldo_inicial' => 3200.00,
                'saldo_actual' => 2950.00,
            ],
            [
                'name' => 'Pedro Rodríguez',
                'email' => 'pedro@example.com',
                'password' => Hash::make('password123'),
                'saldo_inicial' => 1850.75,
                'saldo_actual' => 2100.75,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $transactions = [
            [
                'usuario_emisor_id' => 1,
                'usuario_receptor_id' => 2,
                'monto' => 150.00,
                'descripcion' => 'Pago de servicio',
                'estado' => 'completada',
                'fecha_transaccion' => today()->subDays(1),
            ],
            [
                'usuario_emisor_id' => 2,
                'usuario_receptor_id' => 3,
                'monto' => 300.00,
                'descripcion' => 'Transferencia personal',
                'estado' => 'completada',
                'fecha_transaccion' => today()->subDays(1),
            ],
            [
                'usuario_emisor_id' => 3,
                'usuario_receptor_id' => 1,
                'monto' => 75.50,
                'descripcion' => 'Reembolso',
                'estado' => 'pendiente',
                'fecha_transaccion' => today(),
            ],
            [
                'usuario_emisor_id' => 4,
                'usuario_receptor_id' => 5,
                'monto' => 500.00,
                'descripcion' => 'Pago de préstamo',
                'estado' => 'completada',
                'fecha_transaccion' => today(),
            ],
            [
                'usuario_emisor_id' => 5,
                'usuario_receptor_id' => 4,
                'monto' => 250.00,
                'descripcion' => 'Devolución parcial',
                'estado' => 'completada',
                'fecha_transaccion' => today(),
            ],
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }
    }
}
