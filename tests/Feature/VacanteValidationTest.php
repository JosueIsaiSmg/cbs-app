<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Vacante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VacanteValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_validates_required_fields()
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
    public function it_validates_area_is_string()
    {
        $this->actingAs($this->user);
        $data = [
            'area' => 123,
            'sueldo' => 10000,
            'activo' => true
        ];
        $response = $this->post(route('vacantes.store'), $data);
        $response->assertSessionHasErrors(['area']);
    }

    /** @test */
    public function it_validates_area_max_length()
    {
        $this->actingAs($this->user);
        $data = [
            'area' => str_repeat('a', 256),
            'sueldo' => 10000,
            'activo' => true
        ];
        $response = $this->post(route('vacantes.store'), $data);
        $response->assertSessionHasErrors(['area']);
    }

    /** @test */
    public function it_validates_sueldo_is_numeric()
    {
        $this->actingAs($this->user);
        $data = [
            'area' => 'Administración',
            'sueldo' => 'no-numeric',
            'activo' => true
        ];
        $response = $this->post(route('vacantes.store'), $data);
        $response->assertSessionHasErrors(['sueldo']);
    }

    /** @test */
    public function it_validates_activo_is_boolean()
    {
        $this->actingAs($this->user);
        $data = [
            'area' => 'Administración',
            'sueldo' => 10000,
            'activo' => 'not-boolean'
        ];
        $response = $this->post(route('vacantes.store'), $data);
        $response->assertSessionHasErrors(['activo']);
    }

    /** @test */
    public function it_accepts_valid_data()
    {
        $this->actingAs($this->user);
        $data = [
            'area' => 'Administración',
            'sueldo' => 10000,
            'activo' => true
        ];
        $response = $this->post(route('vacantes.store'), $data);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vacantes', [
            'area' => 'Administración',
            'sueldo' => 10000,
            'activo' => 1
        ]);
    }

    /** @test */
    public function it_validates_update_area_max_length()
    {
        $this->actingAs($this->user);
        $vacante = Vacante::factory()->create();
        $data = [
            'area' => str_repeat('a', 256),
            'sueldo' => 10000,
            'activo' => true
        ];
        $response = $this->put(route('vacantes.update', $vacante->id), $data);
        $response->assertSessionHasErrors(['area']);
    }
} 