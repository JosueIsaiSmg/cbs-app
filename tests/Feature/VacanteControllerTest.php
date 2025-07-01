<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Vacante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

class VacanteControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_display_vacantes_index()
    {
        $this->actingAs($this->user);
        $vacante = Vacante::factory()->create();
        $response = $this->get(route('vacantes.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Vacantes/Index')
            ->has('vacantes', 1)
            ->where('vacantes.0.area', $vacante->area)
            ->where('vacantes.0.sueldo', $vacante->sueldo)
        );
    }

    /** @test */
    public function it_can_display_create_form()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('vacantes.create'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Vacantes/Create')
        );
    }

    /** @test */
    public function it_can_store_new_vacante()
    {
        $this->actingAs($this->user);
        $vacanteData = [
            'area' => 'Recursos Humanos',
            'sueldo' => 12000,
            'activo' => true
        ];
        $response = $this->post(route('vacantes.store'), $vacanteData);
        $this->assertDatabaseHas('vacantes', [
            'area' => 'Recursos Humanos',
            'sueldo' => 12000,
            'activo' => 1
        ]);
        $response->assertRedirect(route('vacantes.index'));
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $this->actingAs($this->user);
        $response = $this->post(route('vacantes.store'), []);
        $response->assertSessionHasErrors([
            'area',
            'sueldo',
            'activo'
        ]);
    }

    /** @test */
    public function it_can_display_show_page()
    {
        $this->actingAs($this->user);
        $vacante = Vacante::factory()->create();
        $response = $this->get(route('vacantes.show', $vacante->id));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Vacantes/Show')
            ->has('vacante')
            ->where('vacante.area', $vacante->area)
            ->where('vacante.sueldo', $vacante->sueldo)
        );
    }

    /** @test */
    public function it_can_display_edit_form()
    {
        $this->actingAs($this->user);
        $vacante = Vacante::factory()->create();
        $response = $this->get(route('vacantes.edit', $vacante->id));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Vacantes/Edit')
            ->has('vacante')
            ->where('vacante.area', $vacante->area)
            ->where('vacante.sueldo', $vacante->sueldo)
        );
    }

    /** @test */
    public function it_can_update_vacante()
    {
        $this->actingAs($this->user);
        $vacante = Vacante::factory()->create();
        $updateData = [
            'area' => 'Ventas',
            'sueldo' => 15000,
            'activo' => false
        ];
        $response = $this->put(route('vacantes.update', $vacante->id), $updateData);
        $this->assertDatabaseHas('vacantes', [
            'id' => $vacante->id,
            'area' => 'Ventas',
            'sueldo' => 15000,
            'activo' => 0
        ]);
        $response->assertRedirect(route('vacantes.index'));
    }

    /** @test */
    public function it_can_delete_vacante()
    {
        $this->actingAs($this->user);
        $vacante = Vacante::factory()->create();
        $this->assertDatabaseHas('vacantes', [
            'id' => $vacante->id,
        ]);
        $response = $this->delete(route('vacantes.destroy', $vacante->id));
        $this->assertDatabaseMissing('vacantes', [
            'id' => $vacante->id,
        ]);
        $response->assertRedirect(route('vacantes.index'));
    }

    /** @test */
    public function it_returns_404_for_nonexistent_vacante()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('vacantes.show', 999));
        $response->assertRedirect(route('vacantes.index'));
    }
} 