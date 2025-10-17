<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;
    public function test_usuario_no_puede_transferir_mas_que_su_saldo()
    {
        $emisor = User::factory()->create(['saldo_actual' => 50]);
        $receptor = User::factory()->create();

        Sanctum::actingAs($emisor);

        $response = $this->postJson('/api/transactions', [
            'email_receptor' => $receptor->email,
            'monto' => 100,
            'descripcion' => 'Transferencia de prueba'
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Saldo insuficiente para realizar la transferencia');
    }
}
