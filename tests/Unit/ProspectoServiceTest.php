<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ProspectoService;
use App\Models\Prospecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ProspectoServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProspectoService $prospectoService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->prospectoService = new ProspectoService();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_get_all_prospects()
    {
        Prospecto::factory()->count(3)->create();

        $result = $this->prospectoService->getAllProspectos();

        $this->assertTrue($result['success']);
        $this->assertCount(3, $result['data']);
        $this->assertEquals('Prospectos obtenidos exitosamente', $result['message']);
    }

    /** @test */
    public function it_can_create_a_prospect()
    {
        $data = [
            'nombre' => 'Juan Pérez',
            'correo' => 'juan.perez@example.com',
            'fecha_registro' => '2024-01-01',
        ];

        $result = $this->prospectoService->createProspecto($data);

        $this->assertTrue($result['success']);
        $this->assertEquals('Prospecto creado exitosamente', $result['message']);
        $this->assertDatabaseHas('prospectos', $data);
    }

    /** @test */
    public function it_can_get_a_prospect()
    {
        $prospecto = Prospecto::factory()->create();

        $result = $this->prospectoService->getProspecto($prospecto->id);

        $this->assertTrue($result['success']);
        $this->assertEquals('Prospecto obtenido exitosamente', $result['message']);
        $this->assertEquals($prospecto->id, $result['data']->id);
    }

    /** @test */
    public function it_can_update_a_prospect()
    {
        $prospecto = Prospecto::factory()->create();

        $updateData = [
            'nombre' => 'Juan Carlos Pérez',
            'email' => 'juan.carlos@email.com',
            'telefono' => '+34612345678',
            'cv' => 'cvs/juan_carlos_cv.pdf',
            'experiencia' => 5,
            'estado' => 'contratado'
        ];

        $result = $this->prospectoService->updateProspecto($prospecto->id, $updateData);

        $this->assertTrue($result['success']);
        $this->assertEquals('Prospecto actualizado exitosamente', $result['message']);
        $this->assertDatabaseHas('prospectos', $updateData);
    }

    /** @test */
    public function it_can_delete_a_prospect()
    {
        $prospecto = Prospecto::factory()->create();

        $result = $this->prospectoService->deleteProspecto($prospecto->id);

        $this->assertTrue($result['success']);
        $this->assertEquals('Prospecto eliminado exitosamente', $result['message']);
        $this->assertDatabaseMissing('prospectos', ['id' => $prospecto->id]);
    }

    /** @test */
    public function it_returns_error_when_prospect_not_found()
    {
        $result = $this->prospectoService->getProspecto(999);

        $this->assertFalse($result['success']);
        $this->assertEquals('Prospecto no encontrado', $result['message']);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $result = $this->prospectoService->createProspecto([]);

        $this->assertFalse($result['success']);
        $this->assertEquals('Datos de validación incorrectos', $result['message']);
        $this->assertArrayHasKey('errors', $result);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $data = [
            'nombre' => 'Juan Pérez',
            'email' => 'invalid-email',
            'telefono' => '+34612345678',
            'cv' => 'cvs/juan_perez_cv.pdf',
            'experiencia' => 3,
            'estado' => 'activo'
        ];

        $result = $this->prospectoService->createProspecto($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Datos de validación incorrectos', $result['message']);
    }

    /** @test */
    public function it_validates_unique_email()
    {
        $existingProspecto = Prospecto::factory()->create(['email' => 'juan.perez@email.com']);

        $data = [
            'nombre' => 'Otro Usuario',
            'email' => 'juan.perez@email.com', // Email duplicado
            'telefono' => '+34612345678',
            'cv' => 'cvs/otro_cv.pdf',
            'experiencia' => 3,
            'estado' => 'activo'
        ];

        $result = $this->prospectoService->createProspecto($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Datos de validación incorrectos', $result['message']);
    }

    /** @test */
    public function it_validates_prospect_status()
    {
        $data = [
            'nombre' => 'Juan Pérez',
            'email' => 'juan.perez@email.com',
            'telefono' => '+34612345678',
            'cv' => 'cvs/juan_perez_cv.pdf',
            'experiencia' => 3,
            'estado' => 'invalid-status'
        ];

        $result = $this->prospectoService->createProspecto($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Datos de validación incorrectos', $result['message']);
    }

    /** @test */
    public function it_can_get_active_prospects()
    {
        Prospecto::factory()->count(2)->create(['estado' => 'activo']);
        Prospecto::factory()->create(['estado' => 'inactivo']);

        $result = $this->prospectoService->getProspectosActivos();

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['data']);
        $this->assertEquals('Prospectos activos obtenidos exitosamente', $result['message']);
    }

    /** @test */
    public function it_can_search_prospects()
    {
        Prospecto::factory()->create(['nombre' => 'Juan Pérez']);
        Prospecto::factory()->create(['nombre' => 'María García']);

        $result = $this->prospectoService->searchProspectos('Juan');

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('Prospectos encontrados exitosamente', $result['message']);
    }

    /** @test */
    public function it_prevents_deleting_prospect_with_interviews()
    {
        $prospecto = Prospecto::factory()->create();
        
        // Crear una entrevista asociada
        $vacante = \App\Models\Vacante::factory()->create();
        \App\Models\Entrevista::factory()->create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id
        ]);

        $result = $this->prospectoService->deleteProspecto($prospecto->id);

        $this->assertFalse($result['success']);
        $this->assertEquals('No se puede eliminar el prospecto porque tiene entrevistas asociadas', $result['message']);
    }
} 