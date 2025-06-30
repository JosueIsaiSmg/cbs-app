<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vacante;
use App\Models\Prospecto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntrevistaValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function vacante_is_required()
    {
        $prospecto = Prospecto::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Notas de prueba',
            'reclutado' => false,
        ]);

        $response->assertSessionHasErrors(['vacante']);
    }

    /** @test */
    public function prospecto_is_required()
    {
        $vacante = Vacante::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'vacante' => $vacante->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Notas de prueba',
            'reclutado' => false,
        ]);

        $response->assertSessionHasErrors(['prospecto']);
    }

    /** @test */
    public function fecha_entrevista_is_required()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'notas' => 'Notas de prueba',
            'reclutado' => false,
        ]);

        $response->assertSessionHasErrors(['fecha_entrevista']);
    }

    /** @test */
    public function notas_is_required()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'reclutado' => false,
        ]);

        $response->assertSessionHasErrors(['notas']);
    }

    /** @test */
    public function reclutado_is_required()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Notas de prueba',
        ]);

        $response->assertSessionHasErrors(['reclutado']);
    }

    /** @test */
    public function fecha_entrevista_must_be_valid_date()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => 'fecha-invalida',
            'notas' => 'Notas de prueba',
            'reclutado' => false,
        ]);

        $response->assertSessionHasErrors(['fecha_entrevista']);
    }

    /** @test */
    public function vacante_must_exist()
    {
        $prospecto = Prospecto::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'vacante' => 99999, // ID que no existe
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Notas de prueba',
            'reclutado' => false,
        ]);

        $response->assertSessionHasErrors(['vacante']);
    }

    /** @test */
    public function prospecto_must_exist()
    {
        $vacante = Vacante::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'vacante' => $vacante->id,
            'prospecto' => 99999, // ID que no existe
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Notas de prueba',
            'reclutado' => false,
        ]);

        $response->assertSessionHasErrors(['prospecto']);
    }

    /** @test */
    public function reclutado_must_be_boolean()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Notas de prueba',
            'reclutado' => 'no-boolean',
        ]);

        $response->assertSessionHasErrors(['reclutado']);
    }

    /** @test */
    public function valid_entrevista_data_passes_validation()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();

        $response = $this->post(route('entrevistas.store'), [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Notas de prueba válidas',
            'reclutado' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('entrevistas.index'));
    }
} 