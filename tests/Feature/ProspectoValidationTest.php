<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Prospecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProspectoValidationTest extends TestCase
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

        $response = $this->post(route('prospectos.store'), []);

        $response->assertSessionHasErrors([
            'nombre',
            'correo',
            'fecha_registro'
        ]);
    }

    /** @test */
    public function it_validates_nombre_is_string()
    {
        $this->actingAs($this->user);

        $data = [
            'nombre' => 123, // No es string
            'correo' => 'test@example.com',
            'fecha_registro' => '2024-01-15'
        ];

        $response = $this->post(route('prospectos.store'), $data);

        $response->assertSessionHasErrors(['nombre']);
    }

    /** @test */
    public function it_validates_nombre_max_length()
    {
        $this->actingAs($this->user);

        $data = [
            'nombre' => str_repeat('a', 256), // Más de 255 caracteres
            'correo' => 'test@example.com',
            'fecha_registro' => '2024-01-15'
        ];

        $response = $this->post(route('prospectos.store'), $data);

        $response->assertSessionHasErrors(['nombre']);
    }

    /** @test */
    public function it_validates_correo_is_email()
    {
        $this->actingAs($this->user);

        $data = [
            'nombre' => 'Test Prospecto',
            'correo' => 'invalid-email',
            'fecha_registro' => '2024-01-15'
        ];

        $response = $this->post(route('prospectos.store'), $data);

        $response->assertSessionHasErrors(['correo']);
    }

    /** @test */
    public function it_validates_correo_is_unique()
    {
        $this->actingAs($this->user);

        // Crear primer prospecto
        $data = [
            'nombre' => 'Test Prospecto',
            'correo' => 'test@example.com',
            'fecha_registro' => '2024-01-15'
        ];

        $this->post(route('prospectos.store'), $data);

        // Intentar crear duplicado
        $response = $this->post(route('prospectos.store'), $data);

        $response->assertSessionHasErrors(['correo']);
    }

    /** @test */
    public function it_validates_fecha_registro_is_date()
    {
        $this->actingAs($this->user);

        $data = [
            'nombre' => 'Test Prospecto',
            'correo' => 'test@example.com',
            'fecha_registro' => 'invalid-date'
        ];

        $response = $this->post(route('prospectos.store'), $data);

        $response->assertSessionHasErrors(['fecha_registro']);
    }

    /** @test */
    public function it_accepts_valid_data()
    {
        $this->actingAs($this->user);

        $data = [
            'nombre' => 'Test Prospecto',
            'correo' => 'test@example.com',
            'fecha_registro' => '2024-01-15'
        ];

        $response = $this->post(route('prospectos.store'), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('prospectos', $data);
    }

    /** @test */
    public function it_validates_update_with_unique_constraint()
    {
        $this->actingAs($this->user);

        // Crear primer prospecto
        $prospecto1 = Prospecto::factory()->create([
            'correo' => 'test1@example.com'
        ]);

        // Crear segundo prospecto
        $prospecto2 = Prospecto::factory()->create([
            'correo' => 'test2@example.com'
        ]);

        // Intentar actualizar el segundo prospecto con el correo del primero
        $data = [
            'nombre' => 'Updated Prospecto',
            'correo' => 'test1@example.com', // Correo del primer prospecto
            'fecha_registro' => '2024-01-20'
        ];

        $response = $this->put(route('prospectos.update', $prospecto2->id), $data);

        $response->assertSessionHasErrors(['correo']);
    }

    /** @test */
    public function it_allows_update_with_same_email()
    {
        $this->actingAs($this->user);

        $prospecto = Prospecto::factory()->create([
            'nombre' => 'Original Name',
            'correo' => 'test@example.com',
            'fecha_registro' => '2024-01-15'
        ]);

        $data = [
            'nombre' => 'Updated Name',
            'correo' => 'test@example.com', // Mismo correo
            'fecha_registro' => '2024-01-15'
        ];

        $response = $this->put(route('prospectos.update', $prospecto->id), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('prospectos', [
            'id' => $prospecto->id,
            'nombre' => 'Updated Name',
            'correo' => 'test@example.com'
        ]);
    }
} 