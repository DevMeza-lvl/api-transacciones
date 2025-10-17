<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    public function test_usuario_no_puede_transferir_monto_mayor_a_saldo()
    {
        $user = User::factory()->create(['saldo_actual' => 100]);
        $this->assertFalse($user->puedeTransferir(150));
        $this->assertTrue($user->puedeTransferir(50));
    }

}
