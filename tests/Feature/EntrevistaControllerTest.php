<?php

namespace Tests\Feature;

use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EntrevistaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario autenticado para las pruebas
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_entrevistas_index()
    {
        // Crear datos de prueba
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista de prueba',
            'reclutado' => false,
        ]);

        // Hacer la petición
        $response = $this->get(route('entrevistas.index'));

        // Verificar la respuesta
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Entrevistas/Index')
            ->has('entrevistas', 1)
            ->has('vacantes')
            ->has('prospectos')
            ->where('entrevistas.0.vacante.id', $vacante->id)
            ->where('entrevistas.0.prospecto.id', $prospecto->id)
        );
    }

    /** @test */
    public function it_can_display_create_form()
    {
        // Crear datos de prueba
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();

        // Hacer la petición
        $response = $this->get(route('entrevistas.create'));

        // Verificar la respuesta
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Entrevistas/Create')
            ->has('vacantes', 1)
            ->has('prospectos', 1)
            ->where('vacantes.0.area', $vacante->area)
            ->where('prospectos.0.nombre', $prospecto->nombre)
        );
    }

    /** @test */
    public function it_can_store_new_entrevista()
    {
        // Crear datos de prueba
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();

        $entrevistaData = [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Nueva entrevista de prueba',
            'reclutado' => true,
        ];

        // Hacer la petición
        $response = $this->post(route('entrevistas.store'), $entrevistaData);

        // Verificar que se creó en la base de datos
        $this->assertDatabaseHas('entrevistas', [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Nueva entrevista de prueba',
            'reclutado' => true,
        ]);

        // Verificar la redirección
        $response->assertRedirect(route('entrevistas.index'));
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        // Hacer la petición sin datos
        $response = $this->post(route('entrevistas.store'), []);

        // Verificar errores de validación
        $response->assertSessionHasErrors([
            'vacante',
            'prospecto',
            'fecha_entrevista',
            'reclutado'
        ]);
    }

    /** @test */
    public function it_can_display_edit_form()
    {
        // Crear datos de prueba
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista para editar',
            'reclutado' => false,
        ]);

        // Hacer la petición
        $response = $this->get(route('entrevistas.edit', [$vacante->id, $prospecto->id]));

        // Verificar la respuesta
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Entrevistas/Edit')
            ->has('entrevista')
            ->has('vacantes')
            ->has('prospectos')
            ->where('entrevista.vacante.id', $vacante->id)
            ->where('entrevista.prospecto.id', $prospecto->id)
        );
    }

    /** @test */
    public function it_can_update_entrevista()
    {
        // Crear datos de prueba
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista original',
            'reclutado' => false,
        ]);

        $updateData = [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-20',
            'notas' => 'Entrevista actualizada',
            'reclutado' => true,
        ];

        // Hacer la petición
        $response = $this->put(route('entrevistas.update', [$vacante->id, $prospecto->id]), $updateData);

        // Verificar que se actualizó en la base de datos
        $this->assertDatabaseHas('entrevistas', [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-20',
            'notas' => 'Entrevista actualizada',
            'reclutado' => true,
        ]);

        // Verificar la redirección
        $response->assertRedirect(route('entrevistas.index'));
    }

    /** @test */
    public function it_can_delete_entrevista()
    {
        // Crear datos de prueba
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista para eliminar',
            'reclutado' => false,
        ]);

        // Verificar que existe en la base de datos
        $this->assertDatabaseHas('entrevistas', [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
        ]);

        // Hacer la petición
        $response = $this->delete(route('entrevistas.destroy', [$vacante->id, $prospecto->id]));

        // Verificar que se eliminó de la base de datos
        $this->assertDatabaseMissing('entrevistas', [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
        ]);

        // Verificar la redirección
        $response->assertRedirect(route('entrevistas.index'));
    }
}
