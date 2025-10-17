<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\Transaction;

class DailyLimitTest extends TestCase
{
    public function test_usuario_excede_limite_diario()
    {
        $user = User::factory()->create(['saldo_actual' => 6000]);
        $receptor = User::factory()->create();

        // Simular transacciones previas hoy con total 4900
        Transaction::factory()->count(2)->create([
            'usuario_emisor_id' => $user->id,
            'usuario_receptor_id' => $receptor->id,
            'monto' => 2450,
            'estado' => 'completada',
            'fecha_transaccion' => now()
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/transactions', [
            'email_receptor' => $receptor->email,
            'monto' => 200,
            'descripcion' => 'Prueba límite diario'
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Límite diario de transferencia excedido']);
    }
}
