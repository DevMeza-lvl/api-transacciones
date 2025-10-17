<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    public function test_usuario_debe_autenticarse_para_ver_estadisticas()
    {
        $response = $this->getJson('/api/stats');
        $response->assertStatus(401);
    }

    public function test_usuario_autenticado_puede_ver_estadisticas()
    {
        $user = User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);

        $response = $this->getJson('/api/stats');
        $response->assertStatus(200)
                 ->assertJsonStructure(['usuarios', 'transacciones', 'actividad_reciente']);
    }
}
