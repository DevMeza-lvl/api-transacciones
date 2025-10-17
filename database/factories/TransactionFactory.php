<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'usuario_emisor_id' => User::factory(),
            'usuario_receptor_id' => User::factory(),
            'monto' => $this->faker->randomFloat(2, 1, 1000),
            'descripcion' => $this->faker->sentence,
            'estado' => 'pendiente',
            'fecha_transaccion' => $this->faker->date(),
        ];
    }
}
