<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Prospecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

class ProspectoControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_display_prospectos_index()
    {
        $this->actingAs($this->user);

        // Crear datos de prueba
        $prospecto = Prospecto::factory()->create();

        // Hacer la petición
        $response = $this->get(route('prospectos.index'));

        // Verificar la respuesta
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Prospectos/Index')
            ->has('prospectos', 1)
            ->where('prospectos.0.nombre', $prospecto->nombre)
            ->where('prospectos.0.correo', $prospecto->correo)
        );
    }

    /** @test */
    public function it_can_display_create_form()
    {
        $this->actingAs($this->user);

        // Hacer la petición
        $response = $this->get(route('prospectos.create'));

        // Verificar la respuesta
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Prospectos/Create')
        );
    }

    /** @test */
    public function it_can_store_new_prospecto()
    {
        $this->actingAs($this->user);

        $prospectoData = [
            'nombre' => 'Nuevo Prospecto',
            'correo' => 'nuevo@example.com',
            'fecha_registro' => '2024-01-15',
        ];

        // Hacer la petición
        $response = $this->post(route('prospectos.store'), $prospectoData);

        // Verificar que se creó en la base de datos
        $this->assertDatabaseHas('prospectos', $prospectoData);

        // Verificar la redirección
        $response->assertRedirect(route('prospectos.index'));
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $this->actingAs($this->user);

        // Hacer la petición sin datos
        $response = $this->post(route('prospectos.store'), []);

        // Verificar errores de validación
        $response->assertSessionHasErrors([
            'nombre',
            'correo',
            'fecha_registro'
        ]);
    }

    /** @test */
    public function it_can_display_show_page()
    {
        $this->actingAs($this->user);

        // Crear datos de prueba
        $prospecto = Prospecto::factory()->create();

        // Hacer la petición
        $response = $this->get(route('prospectos.show', $prospecto->id));

        // Verificar la respuesta
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Prospectos/Show')
            ->has('prospecto')
            ->where('prospecto.nombre', $prospecto->nombre)
            ->where('prospecto.correo', $prospecto->correo)
        );
    }

    /** @test */
    public function it_can_display_edit_form()
    {
        $this->actingAs($this->user);

        // Crear datos de prueba
        $prospecto = Prospecto::factory()->create();

        // Hacer la petición
        $response = $this->get(route('prospectos.edit', $prospecto->id));

        // Verificar la respuesta
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Prospectos/Edit')
            ->has('prospecto')
            ->where('prospecto.nombre', $prospecto->nombre)
            ->where('prospecto.correo', $prospecto->correo)
        );
    }

    /** @test */
    public function it_can_update_prospecto()
    {
        $this->actingAs($this->user);

        // Crear datos de prueba
        $prospecto = Prospecto::factory()->create();

        $updateData = [
            'nombre' => 'Prospecto Actualizado',
            'correo' => 'actualizado@example.com',
            'fecha_registro' => '2024-01-20',
        ];

        // Hacer la petición
        $response = $this->put(route('prospectos.update', $prospecto->id), $updateData);

        // Verificar que se actualizó en la base de datos
        $this->assertDatabaseHas('prospectos', [
            'id' => $prospecto->id,
            'nombre' => 'Prospecto Actualizado',
            'correo' => 'actualizado@example.com',
            'fecha_registro' => '2024-01-20',
        ]);

        // Verificar la redirección
        $response->assertRedirect(route('prospectos.index'));
    }

    /** @test */
    public function it_can_delete_prospecto()
    {
        $this->actingAs($this->user);

        // Crear datos de prueba
        $prospecto = Prospecto::factory()->create();

        // Verificar que existe en la base de datos
        $this->assertDatabaseHas('prospectos', [
            'id' => $prospecto->id,
        ]);

        // Hacer la petición
        $response = $this->delete(route('prospectos.destroy', $prospecto->id));

        // Verificar que se eliminó de la base de datos
        $this->assertDatabaseMissing('prospectos', [
            'id' => $prospecto->id,
        ]);

        // Verificar la redirección
        $response->assertRedirect(route('prospectos.index'));
    }

    /** @test */
    public function it_returns_404_for_nonexistent_prospecto()
    {
        $this->actingAs($this->user);

        // Hacer la petición con ID inexistente
        $response = $this->get(route('prospectos.show', 999));

        // Verificar redirección con error
        $response->assertRedirect(route('prospectos.index'));
    }
} 