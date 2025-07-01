<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\VacanteService;
use App\Models\Vacante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class VacanteServiceTest extends TestCase
{
    use RefreshDatabase;

    private VacanteService $vacanteService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->vacanteService = new VacanteService();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_get_all_vacancies()
    {
        Vacante::factory()->count(3)->create();

        $result = $this->vacanteService->getAllVacantes();

        $this->assertTrue($result['success']);
        $this->assertCount(3, $result['data']);
        $this->assertEquals('Vacantes obtenidas exitosamente', $result['message']);
    }

    /** @test */
    public function it_can_create_a_vacancy()
    {
        $data = [
            'salario' => 45000,
            'activo' => true,
            'area' => 'Desarrollo'
        ];

        $result = $this->vacanteService->createVacante($data);

        $this->assertTrue($result['success']);
        $this->assertEquals('Vacante creada exitosamente', $result['message']);
        $this->assertDatabaseHas('vacantes', $data);
    }

    /** @test */
    public function it_can_get_a_vacancy()
    {
        $vacante = Vacante::factory()->create();

        $result = $this->vacanteService->getVacante($vacante->id);

        $this->assertTrue($result['success']);
        $this->assertEquals('Vacante obtenida exitosamente', $result['message']);
        $this->assertEquals($vacante->id, $result['data']->id);
    }

    /** @test */
    public function it_can_update_a_vacancy()
    {
        $vacante = Vacante::factory()->create();

        $updateData = [
            'area' => 'Desarrollo',
            'sueldo' => 55000,
            'activo' => false
        ];

        $result = $this->vacanteService->updateVacante($vacante->id, $updateData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Vacante actualizada exitosamente', $result['message']);
        $this->assertDatabaseHas('vacantes', $updateData);
    }

    /** @test */
    public function it_can_delete_a_vacancy()
    {
        $vacante = Vacante::factory()->create();

        $result = $this->vacanteService->deleteVacante($vacante->id);

        $this->assertTrue($result['success']);
        $this->assertEquals('Vacante eliminada exitosamente', $result['message']);
        $this->assertDatabaseMissing('vacantes', ['id' => $vacante->id]);
    }

    /** @test */
    public function it_returns_error_when_vacancy_not_found()
    {
        $result = $this->vacanteService->getVacante(999);

        $this->assertFalse($result['success']);
        $this->assertEquals('Vacante no encontrada', $result['message']);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $result = $this->vacanteService->createVacante([]);

        $this->assertFalse($result['success']);
        $this->assertEquals('Datos de validación incorrectos', $result['message']);
        $this->assertArrayHasKey('errors', $result);
    }

    /** @test */
    public function it_validates_vacancy_status()
    {
        $data = [
            'area' => 'Desarrollo',
            'sueldo' => 45000,
            'activo' => false
        ];

        $result = $this->vacanteService->createVacante($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Datos de validación incorrectos', $result['message']);
    }

    /** @test */
    public function it_can_get_active_vacancies()
    {
        Vacante::factory()->count(2)->create(['activo' => true]);
        Vacante::factory()->create(['activo' => false]);

        $result = $this->vacanteService->getVacantesActivas();

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['data']);
        $this->assertEquals('Vacantes activas obtenidas exitosamente', $result['message']);
    }

    /** @test */
    public function it_prevents_deleting_vacancy_with_interviews()
    {
        $vacante = Vacante::factory()->create();
        
        // Crear una entrevista asociada
        $prospecto = \App\Models\Prospecto::factory()->create();
        \App\Models\Entrevista::factory()->create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id
        ]);

        $result = $this->vacanteService->deleteVacante($vacante->id);

        $this->assertFalse($result['success']);
        $this->assertEquals('No se puede eliminar la vacante porque tiene entrevistas asociadas', $result['message']);
    }
} 