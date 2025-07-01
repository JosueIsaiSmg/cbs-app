<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Vacante;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VacanteApiTest extends TestCase
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
    public function it_can_list_all_vacancies()
    {
        Vacante::factory()->count(3)->create();

        $response = $this->getJson('/api/vacantes');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'area',
                            'sueldo',
                            'activo',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_can_show_a_single_vacancy()
    {
        $vacante = Vacante::factory()->create();

        $response = $this->getJson("/api/vacantes/{$vacante->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $vacante->id,
                        'area' => $vacante->area,
                        'sueldo' => $vacante->sueldo,
                        'activo' => $vacante->activo
                    ]
                ]);
    }

    /** @test */
    public function it_can_create_a_vacancy()
    {
        $data = [
            'area' => 'Desarrollo',
            'sueldo' => 45000,
            'activo' => true
        ];

        $response = $this->postJson('/api/vacantes', $data);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Vacante creada exitosamente',
                    'data' => [
                        'area' => $data['area'],
                        'sueldo' => $data['sueldo'],
                        'activo' => $data['activo']
                    ]
                ]);

        $this->assertDatabaseHas('vacantes', $data);
    }

    /** @test */
    public function it_can_update_a_vacancy()
    {
        $vacante = Vacante::factory()->create();

        $updateData = [
            'area' => 'Desarrollo Senior',
            'sueldo' => 55000,
            'activo' => false
        ];

        $response = $this->putJson("/api/vacantes/{$vacante->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Vacante actualizada exitosamente',
                    'data' => [
                        'id' => $vacante->id,
                        'area' => $updateData['area'],
                        'sueldo' => $updateData['sueldo'],
                        'activo' => $updateData['activo']
                    ]
                ]);

        $this->assertDatabaseHas('vacantes', $updateData);
    }

    /** @test */
    public function it_can_delete_a_vacancy()
    {
        $vacante = Vacante::factory()->create();

        $response = $this->deleteJson("/api/vacantes/{$vacante->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Vacante eliminada exitosamente'
                ]);

        $this->assertDatabaseMissing('vacantes', ['id' => $vacante->id]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_vacancy()
    {
        $response = $this->getJson('/api/vacantes/999');

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Vacante no encontrada'
                ]);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $response = $this->postJson('/api/vacantes', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'area',
                    'sueldo',
                    'activo'
                ]);
    }

    /** @test */
    public function it_validates_salary_is_numeric()
    {
        $data = [
            'area' => 'Desarrollo',
            'sueldo' => 'invalid-salary',
            'activo' => true
        ];

        $response = $this->postJson('/api/vacantes', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['sueldo']);
    }

    /** @test */
    public function it_validates_active_is_boolean()
    {
        $data = [
            'area' => 'Desarrollo',
            'sueldo' => 45000,
            'activo' => 'invalid-boolean'
        ];

        $response = $this->postJson('/api/vacantes', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['activo']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/vacantes');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_filter_vacancies_by_status()
    {
        Vacante::factory()->count(2)->create(['activo' => true]);
        Vacante::factory()->create(['activo' => false]);

        $response = $this->getJson('/api/vacantes?activo=1');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }

    /** @test */
    public function it_can_search_vacancies_by_area()
    {
        Vacante::factory()->create(['area' => 'Desarrollo Laravel']);
        Vacante::factory()->create(['area' => 'Diseño UX']);

        $response = $this->getJson('/api/vacantes?search=Laravel');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }
} 