<?php

namespace Tests\Feature;

use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntrevistaAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cannot_access_entrevistas_index()
    {
        $response = $this->get(route('entrevistas.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function unauthenticated_users_cannot_access_create_form()
    {
        $response = $this->get(route('entrevistas.create'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function unauthenticated_users_cannot_store_entrevista()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista de prueba',
            'reclutado' => false,
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function unauthenticated_users_cannot_access_edit_form()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista de prueba',
            'reclutado' => false,
        ]);

        $response = $this->get(route('entrevistas.edit', [$vacante->id, $prospecto->id]));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function unauthenticated_users_cannot_update_entrevista()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista original',
            'reclutado' => false,
        ]);

        $response = $this->put(route('entrevistas.update', [$vacante->id, $prospecto->id]), [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-20',
            'notas' => 'Entrevista actualizada',
            'reclutado' => true,
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function unauthenticated_users_cannot_delete_entrevista()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista para eliminar',
            'reclutado' => false,
        ]);

        $response = $this->delete(route('entrevistas.destroy', [$vacante->id, $prospecto->id]));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_users_can_access_entrevistas_index()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('entrevistas.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_users_can_access_create_form()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('entrevistas.create'));

        $response->assertStatus(200);
    }
} 