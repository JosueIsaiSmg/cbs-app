<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;
use App\Services\EntrevistaService;
use App\Http\Controllers\EntrevistaController;
use App\Http\Controllers\Api\EntrevistaController as ApiEntrevistaController;
use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class EntrevistaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private EntrevistaService $entrevistaService;
    private EntrevistaController $webController;
    private ApiEntrevistaController $apiController;
    private User $user;
    private Vacante $vacante;
    private Prospecto $prospecto;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->entrevistaService = new EntrevistaService();
        $this->webController = new EntrevistaController($this->entrevistaService);
        $this->apiController = new ApiEntrevistaController($this->entrevistaService);
        
        $this->user = User::factory()->create();
        $this->vacante = Vacante::factory()->create();
        $this->prospecto = Prospecto::factory()->create();
    }

    /** @test */
    public function it_integrates_service_with_web_controller()
    {
        $this->actingAs($this->user);

        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test integration',
            'reclutado' => false
        ];

        // Simular request del controlador web
        $request = new Request($data);
        $response = $this->webController->store($request);

        // Verificar que la entrevista se creó correctamente
        $this->assertDatabaseHas('entrevistas', $data);
        
        // Verificar que el servicio devuelve la entrevista
        $result = $this->entrevistaService->getEntrevista($this->vacante->id, $this->prospecto->id);
        $this->assertTrue($result['success']);
    }

    /** @test */
    public function it_integrates_service_with_api_controller()
    {
        $this->actingAs($this->user);

        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test API integration',
            'reclutado' => false
        ];

        // Simular request del controlador API
        $request = new Request($data);
        $response = $this->apiController->store($request);

        // Verificar que la entrevista se creó correctamente
        $this->assertDatabaseHas('entrevistas', $data);
        
        // Verificar que el servicio devuelve la entrevista
        $result = $this->entrevistaService->getEntrevista($this->vacante->id, $this->prospecto->id);
        $this->assertTrue($result['success']);
    }

    /** @test */
    public function it_handles_validation_errors_consistently()
    {
        $this->actingAs($this->user);

        $invalidData = [
            'vacante' => 999, // Vacante inexistente
            'prospecto' => 999, // Prospecto inexistente
            'fecha_entrevista' => 'invalid-date',
            'notas' => '',
            'reclutado' => 'invalid-boolean'
        ];

        // Probar validación en el servicio
        $result = $this->entrevistaService->createEntrevista($invalidData);
        $this->assertFalse($result['success']);

        // Probar validación en el controlador web
        $request = new Request($invalidData);
        $response = $this->webController->store($request);
        $this->assertTrue($response->getStatusCode() === 302 || $response->getStatusCode() === 422);

        // Probar validación en el controlador API
        $request = new Request($invalidData);
        $response = $this->apiController->store($request);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /** @test */
    public function it_maintains_data_consistency_across_layers()
    {
        $this->actingAs($this->user);

        // Crear entrevista a través del servicio
        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test consistency',
            'reclutado' => false
        ];

        $result = $this->entrevistaService->createEntrevista($data);
        $this->assertTrue($result['success']);

        // Verificar que el controlador web puede acceder a la misma data
        $webResponse = $this->webController->show($this->vacante->id, $this->prospecto->id);
        $this->assertNotNull($webResponse);

        // Verificar que el controlador API puede acceder a la misma data
        $apiResponse = $this->apiController->show($this->vacante->id, $this->prospecto->id);
        $this->assertNotNull($apiResponse);
    }

    /** @test */
    public function it_handles_relationships_correctly()
    {
        $this->actingAs($this->user);

        // Crear entrevista con relaciones
        $entrevista = Entrevista::factory()->create([
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id
        ]);

        // Verificar que el servicio puede cargar relaciones
        $result = $this->entrevistaService->getEntrevista($this->vacante->id, $this->prospecto->id);
        $this->assertTrue($result['success']);
        $this->assertNotNull($result['data']->vacante);
        $this->assertNotNull($result['data']->prospecto);

        // Verificar que los controladores pueden acceder a las relaciones
        $webResponse = $this->webController->show($this->vacante->id, $this->prospecto->id);
        $this->assertNotNull($webResponse);

        $apiResponse = $this->apiController->show($this->vacante->id, $this->prospecto->id);
        $this->assertNotNull($apiResponse);
    }

    /** @test */
    public function it_handles_business_logic_consistently()
    {
        $this->actingAs($this->user);

        // Crear múltiples entrevistas para probar filtros
        Entrevista::factory()->count(2)->create([
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id
        ]);

        // Verificar que el servicio filtra correctamente
        $result = $this->entrevistaService->getEntrevistasByVacante($this->vacante->id);
        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['data']);

        $result = $this->entrevistaService->getEntrevistasByProspecto($this->prospecto->id);
        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['data']);

        // Verificar que los controladores pueden usar los mismos filtros
        $webResponse = $this->webController->index();
        $this->assertNotNull($webResponse);

        $apiResponse = $this->apiController->index();
        $this->assertNotNull($apiResponse);
    }

    /** @test */
    public function it_handles_error_scenarios_consistently()
    {
        $this->actingAs($this->user);

        // Probar acceso a entrevista inexistente
        $result = $this->entrevistaService->getEntrevista(999, 999);
        $this->assertFalse($result['success']);

        // Verificar que los controladores manejan errores de manera consistente
        $webResponse = $this->webController->show(999, 999);
        $this->assertTrue($webResponse->getStatusCode() === 404 || $webResponse->getStatusCode() === 302);

        $apiResponse = $this->apiController->show(999, 999);
        $this->assertEquals(404, $apiResponse->getStatusCode());
    }

    /** @test */
    public function it_maintains_audit_trail()
    {
        $this->actingAs($this->user);

        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test audit trail',
            'reclutado' => false
        ];

        // Crear entrevista
        $result = $this->entrevistaService->createEntrevista($data);
        $this->assertTrue($result['success']);
        
        $entrevista = $result['data'];
        $this->assertNotNull($entrevista->created_at);
        $this->assertNotNull($entrevista->updated_at);

        // Actualizar entrevista
        $updateData = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-20',
            'notas' => 'Entrevista actualizada',
            'reclutado' => true
        ];
        
        $result = $this->entrevistaService->updateEntrevista($this->vacante->id, $this->prospecto->id, $updateData);
        $this->assertTrue($result['success']);
        
        $updatedEntrevista = $result['data'];
        $this->assertNotEquals($entrevista->updated_at, $updatedEntrevista->updated_at);

        // Verificar que los timestamps se mantienen consistentes
        $this->assertEquals($entrevista->created_at, $updatedEntrevista->created_at);
    }

    /** @test */
    public function it_handles_concurrent_operations()
    {
        $this->actingAs($this->user);

        $data = [
            'vacante' => $this->vacante->id,
            'prospecto' => $this->prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Test concurrent operations',
            'reclutado' => false
        ];

        // Simular operaciones concurrentes
        $result1 = $this->entrevistaService->createEntrevista($data);
        $this->assertTrue($result1['success']);

        // Cambiar prospecto para evitar duplicados
        $data2 = $data;
        $data2['prospecto'] = Prospecto::factory()->create()->id;
        $result2 = $this->entrevistaService->createEntrevista($data2);
        $this->assertTrue($result2['success']);

        // Verificar que ambas entrevistas se crearon correctamente
        $this->assertNotEquals($result1['data']->id, $result2['data']->id);
        $this->assertEquals($result1['data']->vacante, $result2['data']->vacante);

        // Verificar que los controladores pueden manejar múltiples registros
        $webResponse = $this->webController->index();
        $this->assertNotNull($webResponse);

        $apiResponse = $this->apiController->index();
        $this->assertNotNull($apiResponse);
    }
} 