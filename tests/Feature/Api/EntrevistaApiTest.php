<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EntrevistaApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Vacante $vacante;
    private Prospecto $prospecto;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->vacante = Vacante::factory()->create();
        $this->prospecto = Prospecto::factory()->create();
        
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_can_list_all_interviews()
    {
        Entrevista::factory()->count(3)->create([
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson('/api/entrevistas');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'vacante_id',
                            'prospecto_id',
                            'fecha',
                            'hora',
                            'tipo',
                            'estado',
                            'notas',
                            'created_at',
                            'updated_at',
                            'vacante' => [
                                'id',
                                'titulo',
                                'empresa'
                            ],
                            'prospecto' => [
                                'id',
                                'nombre',
                                'email'
                            ]
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_can_show_a_single_interview()
    {
        $entrevista = Entrevista::factory()->create([
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson("/api/entrevistas/{$entrevista->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $entrevista->id,
                        'vacante_id' => $entrevista->vacante_id,
                        'prospecto_id' => $entrevista->prospecto_id,
                        'fecha' => $entrevista->fecha,
                        'hora' => $entrevista->hora,
                        'tipo' => $entrevista->tipo,
                        'estado' => $entrevista->estado,
                        'notas' => $entrevista->notas
                    ]
                ]);
    }

    /** @test */
    public function it_can_create_an_interview()
    {
        $data = [
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'fecha' => '2024-01-15',
            'hora' => '14:30',
            'tipo' => 'presencial',
            'estado' => 'programada',
            'notas' => 'Primera entrevista técnica'
        ];

        $response = $this->postJson('/api/entrevistas', $data);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Entrevista creada exitosamente',
                    'data' => [
                        'vacante_id' => $data['vacante_id'],
                        'prospecto_id' => $data['prospecto_id'],
                        'fecha' => $data['fecha'],
                        'hora' => $data['hora'],
                        'tipo' => $data['tipo'],
                        'estado' => $data['estado'],
                        'notas' => $data['notas']
                    ]
                ]);

        $this->assertDatabaseHas('entrevistas', $data);
    }

    /** @test */
    public function it_can_update_an_interview()
    {
        $entrevista = Entrevista::factory()->create([
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'user_id' => $this->user->id
        ]);

        $updateData = [
            'fecha' => '2024-01-20',
            'hora' => '15:00',
            'estado' => 'completada',
            'notas' => 'Entrevista completada exitosamente'
        ];

        $response = $this->putJson("/api/entrevistas/{$entrevista->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Entrevista actualizada exitosamente',
                    'data' => [
                        'id' => $entrevista->id,
                        'fecha' => $updateData['fecha'],
                        'hora' => $updateData['hora'],
                        'estado' => $updateData['estado'],
                        'notas' => $updateData['notas']
                    ]
                ]);

        $this->assertDatabaseHas('entrevistas', array_merge(['id' => $entrevista->id], $updateData));
    }

    /** @test */
    public function it_can_delete_an_interview()
    {
        $entrevista = Entrevista::factory()->create([
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->deleteJson("/api/entrevistas/{$entrevista->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Entrevista eliminada exitosamente'
                ]);

        $this->assertDatabaseMissing('entrevistas', ['id' => $entrevista->id]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_interview()
    {
        $response = $this->getJson('/api/entrevistas/999');

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Entrevista no encontrada'
                ]);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $response = $this->postJson('/api/entrevistas', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'vacante_id',
                    'prospecto_id',
                    'fecha',
                    'hora',
                    'tipo',
                    'estado'
                ]);
    }

    /** @test */
    public function it_validates_date_format()
    {
        $data = [
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'fecha' => 'invalid-date',
            'hora' => '14:30',
            'tipo' => 'presencial',
            'estado' => 'programada'
        ];

        $response = $this->postJson('/api/entrevistas', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['fecha']);
    }

    /** @test */
    public function it_validates_time_format()
    {
        $data = [
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'fecha' => '2024-01-15',
            'hora' => 'invalid-time',
            'tipo' => 'presencial',
            'estado' => 'programada'
        ];

        $response = $this->postJson('/api/entrevistas', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['hora']);
    }

    /** @test */
    public function it_validates_interview_type()
    {
        $data = [
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'fecha' => '2024-01-15',
            'hora' => '14:30',
            'tipo' => 'invalid-type',
            'estado' => 'programada'
        ];

        $response = $this->postJson('/api/entrevistas', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['tipo']);
    }

    /** @test */
    public function it_validates_interview_status()
    {
        $data = [
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'fecha' => '2024-01-15',
            'hora' => '14:30',
            'tipo' => 'presencial',
            'estado' => 'invalid-status'
        ];

        $response = $this->postJson('/api/entrevistas', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['estado']);
    }

    /** @test */
    public function it_validates_vacancy_exists()
    {
        $data = [
            'vacante_id' => 999,
            'prospecto_id' => $this->prospecto->id,
            'fecha' => '2024-01-15',
            'hora' => '14:30',
            'tipo' => 'presencial',
            'estado' => 'programada'
        ];

        $response = $this->postJson('/api/entrevistas', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['vacante_id']);
    }

    /** @test */
    public function it_validates_prospect_exists()
    {
        $data = [
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => 999,
            'fecha' => '2024-01-15',
            'hora' => '14:30',
            'tipo' => 'presencial',
            'estado' => 'programada'
        ];

        $response = $this->postJson('/api/entrevistas', $data);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['prospecto_id']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        // No autenticar al usuario
        $this->app->make('auth')->guard('sanctum')->logout();

        $response = $this->getJson('/api/entrevistas');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_filter_interviews_by_status()
    {
        Entrevista::factory()->count(2)->create([
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'user_id' => $this->user->id,
            'estado' => 'programada'
        ]);
        
        Entrevista::factory()->create([
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'user_id' => $this->user->id,
            'estado' => 'completada'
        ]);

        $response = $this->getJson('/api/entrevistas?estado=programada');

        $response->assertStatus(200)
                ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_can_filter_interviews_by_date_range()
    {
        Entrevista::factory()->create([
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'user_id' => $this->user->id,
            'fecha' => '2024-01-15'
        ]);
        
        Entrevista::factory()->create([
            'vacante_id' => $this->vacante->id,
            'prospecto_id' => $this->prospecto->id,
            'user_id' => $this->user->id,
            'fecha' => '2024-01-25'
        ]);

        $response = $this->getJson('/api/entrevistas?fecha_desde=2024-01-10&fecha_hasta=2024-01-20');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }
} 