<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Prospecto;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProspectoApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_can_list_all_prospects()
    {
        Prospecto::factory()->count(3)->create();

        $response = $this->getJson('/api/prospectos');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'nombre',
                            'correo',
                            'fecha_registro',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_can_show_a_single_prospect()
    {
        $prospecto = Prospecto::factory()->create();

        $response = $this->getJson("/api/prospectos/{$prospecto->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $prospecto->id,
                        'nombre' => $prospecto->nombre,
                        'correo' => $prospecto->correo,
                        'fecha_registro' => $prospecto->fecha_registro
                    ]
                ]);
    }

    /** @test */
    public function it_can_create_a_prospect()
    {
        $data = [
            'nombre' => 'Juan Pérez',
            'correo' => 'juan.perez@email.com',
            'fecha_registro' => '2024-01-15'
        ];

        $response = $this->postJson('/api/prospectos', $data);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Prospecto creado exitosamente',
                    'data' => [
                        'nombre' => $data['nombre'],
                        'correo' => $data['correo'],
                        'fecha_registro' => $data['fecha_registro']
                    ]
                ]);

        $this->assertDatabaseHas('prospectos', $data);
    }

    /** @test */
    public function it_can_update_a_prospect()
    {
        $prospecto = Prospecto::factory()->create();

        $updateData = [
            'nombre' => 'Juan Carlos Pérez',
            'correo' => 'juan.carlos@email.com',
            'fecha_registro' => '2024-01-20'
        ];

        $response = $this->putJson("/api/prospectos/{$prospecto->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Prospecto actualizado exitosamente',
                    'data' => [
                        'id' => $prospecto->id,
                        'nombre' => $updateData['nombre'],
                        'correo' => $updateData['correo'],
                        'fecha_registro' => $updateData['fecha_registro']
                    ]
                ]);

        $this->assertDatabaseHas('prospectos', $updateData);
    }

    /** @test */
    public function it_can_delete_a_prospect()
    {
        $prospecto = Prospecto::factory()->create();

        $response = $this->deleteJson("/api/prospectos/{$prospecto->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Prospecto eliminado exitosamente'
                ]);

        $this->assertDatabaseMissing('prospectos', ['id' => $prospecto->id]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_prospect()
    {
        $response = $this->getJson('/api/prospectos/999');

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Prospecto no encontrado'
                ]);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $response = $this->postJson('/api/prospectos', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'nombre',
                    'correo',
                    'fecha_registro'
                ]);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $data = [
            'nombre' => 'Juan Pérez',
            'correo' => 'invalid-email',
            'fecha_registro' => '2024-01-15'
        ];

        $response = $this->postJson('/api/prospectos', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['correo']);
    }

    /** @test */
    public function it_validates_unique_email()
    {
        Prospecto::factory()->create(['correo' => 'juan.perez@email.com']);

        $data = [
            'nombre' => 'Otro Usuario',
            'correo' => 'juan.perez@email.com', // Email duplicado
            'fecha_registro' => '2024-01-15'
        ];

        $response = $this->postJson('/api/prospectos', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['correo']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/prospectos');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_search_prospects_by_name()
    {
        Prospecto::factory()->create([
            'nombre' => 'Juan Pérez',
            'user_id' => $this->user->id
        ]);
        
        Prospecto::factory()->create([
            'nombre' => 'María García',
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson('/api/prospectos?search=Juan');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }

    /** @test */
    public function it_can_search_prospects_by_email()
    {
        Prospecto::factory()->create([
            'correo' => 'juan.perez@email.com',
            'user_id' => $this->user->id
        ]);
        
        Prospecto::factory()->create([
            'correo' => 'maria.garcia@email.com',
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson('/api/prospectos?search=juan.perez');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }
} 