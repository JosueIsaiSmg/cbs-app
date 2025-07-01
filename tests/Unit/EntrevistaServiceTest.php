<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EntrevistaService;
use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class EntrevistaServiceTest extends TestCase
{
    use RefreshDatabase;

    private EntrevistaService $entrevistaService;
    private User $user;
    private Vacante $vacante;
    private Prospecto $prospecto;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->entrevistaService = new EntrevistaService();
        
        // Crear datos de prueba
        $this->user = User::factory()->create();
        $this->vacante = Vacante::factory()->create();
        $this->prospecto = Prospecto::factory()->create();
    }

    /** @test */
    public function it_can_get_all_interviews()
    {
        Entrevista::factory()->count(3)->create([
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id
        ]);

        $result = $this->entrevistaService->getAllEntrevistas();

        $this->assertTrue($result['success']);
        $this->assertCount(3, $result['data']);
        $this->assertEquals('Entrevistas obtenidas exitosamente', $result['message']);
    }

    /** @test */
    public function it_can_create_an_interview()
    {
        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Primera entrevista técnica',
            'reclutado' => false
        ];

        $result = $this->entrevistaService->createEntrevista($data);

        $this->assertTrue($result['success']);
        $this->assertEquals('Entrevista creada exitosamente', $result['message']);
        $this->assertDatabaseHas('entrevistas', $data);
    }

    /** @test */
    public function it_can_get_an_interview()
    {
        $entrevista = Entrevista::factory()->create([
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id
        ]);

        $result = $this->entrevistaService->getEntrevista($this->vacante->id, $this->prospecto->id);

        $this->assertTrue($result['success']);
        $this->assertEquals('Entrevista obtenida exitosamente', $result['message']);
        $this->assertEquals($entrevista->id, $result['data']->id);
    }

    /** @test */
    public function it_can_update_an_interview()
    {
        $entrevista = Entrevista::factory()->create([
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id
        ]);

        $updateData = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-20',
            'notas' => 'Entrevista actualizada',
            'reclutado' => true
        ];

        $result = $this->entrevistaService->updateEntrevista($this->vacante->id, $this->prospecto->id, $updateData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Entrevista actualizada exitosamente', $result['message']);
        $this->assertDatabaseHas('entrevistas', $updateData);
    }

    /** @test */
    public function it_can_delete_an_interview()
    {
        $entrevista = Entrevista::factory()->create([
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id
        ]);

        $result = $this->entrevistaService->deleteEntrevista($this->vacante->id, $this->prospecto->id);

        $this->assertTrue($result['success']);
        $this->assertEquals('Entrevista eliminada exitosamente', $result['message']);
        $this->assertDatabaseMissing('entrevistas', ['id' => $entrevista->id]);
    }

    /** @test */
    public function it_returns_error_when_interview_not_found()
    {
        $result = $this->entrevistaService->getEntrevista(999, 999);

        $this->assertFalse($result['success']);
        $this->assertEquals('Entrevista no encontrada', $result['message']);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $result = $this->entrevistaService->createEntrevista([]);

        $this->assertFalse($result['success']);
        $this->assertEquals('Datos de validación incorrectos', $result['message']);
        $this->assertArrayHasKey('errors', $result);
    }

    /** @test */
    public function it_validates_vacancy_exists()
    {
        $data = [
            'vacante' => 999,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test',
            'reclutado' => false
        ];

        $result = $this->entrevistaService->createEntrevista($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Datos de validación incorrectos', $result['message']);
    }

    /** @test */
    public function it_validates_prospect_exists()
    {
        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => 999,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test',
            'reclutado' => false
        ];

        $result = $this->entrevistaService->createEntrevista($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Datos de validación incorrectos', $result['message']);
    }

    /** @test */
    public function it_prevents_duplicate_interviews()
    {
        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test',
            'reclutado' => false
        ];

        // Crear primera entrevista
        $this->entrevistaService->createEntrevista($data);

        // Intentar crear duplicado
        $result = $this->entrevistaService->createEntrevista($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Ya existe una entrevista para esta vacante y prospecto', $result['message']);
    }

    /** @test */
    public function it_can_get_interviews_by_vacancy()
    {
        Entrevista::factory()->count(2)->create([
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id
        ]);

        $result = $this->entrevistaService->getEntrevistasByVacante($this->vacante->id);

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['data']);
        $this->assertEquals('Entrevistas por vacante obtenidas exitosamente', $result['message']);
    }

    /** @test */
    public function it_can_get_interviews_by_prospect()
    {
        Entrevista::factory()->count(2)->create([
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id
        ]);

        $result = $this->entrevistaService->getEntrevistasByProspecto($this->prospecto->id);

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['data']);
        $this->assertEquals('Entrevistas por prospecto obtenidas exitosamente', $result['message']);
    }

    /** @test */
    public function it_can_get_form_data()
    {
        $result = $this->entrevistaService->getFormData();

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('vacantes', $result['data']);
        $this->assertArrayHasKey('prospectos', $result['data']);
    }

    /** @test */
    public function it_validates_date_format()
    {
        $this->expectException(ValidationException::class);

        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => 'invalid-date',
            'notas' => 'Test',
            'reclutado' => false
        ];

        $this->entrevistaService->createEntrevista($data);
    }

    /** @test */
    public function it_validates_time_format()
    {
        $this->expectException(ValidationException::class);

        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test',
            'reclutado' => false
        ];

        $this->entrevistaService->createEntrevista($data);
    }

    /** @test */
    public function it_validates_interview_type()
    {
        $this->expectException(ValidationException::class);

        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test',
            'reclutado' => false
        ];

        $this->entrevistaService->createEntrevista($data);
    }

    /** @test */
    public function it_validates_interview_status()
    {
        $this->expectException(ValidationException::class);

        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test',
            'reclutado' => 'invalid-status'
        ];

        $this->entrevistaService->createEntrevista($data);
    }
} 