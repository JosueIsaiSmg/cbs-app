<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Services\EntrevistaService;
use App\Services\VacanteService;
use App\Services\ProspectoService;
use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ServicePerformanceTest extends TestCase
{
    use RefreshDatabase;

    private EntrevistaService $entrevistaService;
    private VacanteService $vacanteService;
    private ProspectoService $prospectoService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->entrevistaService = new EntrevistaService();
        $this->vacanteService = new VacanteService();
        $this->prospectoService = new ProspectoService();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_handles_large_number_of_interviews_efficiently()
    {
        // Crear datos de prueba
        $vacantes = Vacante::factory()->count(10)->create(['user_id' => $this->user->id]);
        $prospectos = Prospecto::factory()->count(50)->create(['user_id' => $this->user->id]);
        
        // Crear 1000 entrevistas
        $startTime = microtime(true);
        
        for ($i = 0; $i < 1000; $i++) {
            $data = [
                'vacante' => $vacantes->random()->id,
                'prospecto' => $prospectos->random()->id,
                'fecha_entrevista' => '2024-01-' . str_pad(rand(1, 31), 2, '0', STR_PAD_LEFT),
                'reclutado' => false,
                'notas' => 'Test performance interview ' . $i,
                
            ];
            
            $this->entrevistaService->create($data);
        }
        
        $creationTime = microtime(true) - $startTime;
        
        // Verificar que todas las entrevistas se crearon
        $this->assertEquals(1000, Entrevista::count());
        
        // Probar consulta con relaciones
        $startTime = microtime(true);
        $entrevistas = $this->entrevistaService->getAllWithRelationships();
        $queryTime = microtime(true) - $startTime;
        
        $this->assertCount(1000, $entrevistas);
        
        // Verificar que las relaciones se cargan correctamente
        $this->assertTrue($entrevistas->first()->relationLoaded('vacante'));
        $this->assertTrue($entrevistas->first()->relationLoaded('prospecto'));
        
        // Log de rendimiento
        $this->addWarning("Creación de 1000 entrevistas: {$creationTime}s");
        $this->addWarning("Consulta con relaciones: {$queryTime}s");
        
        // Verificar que el tiempo de consulta es razonable (< 1 segundo)
        $this->assertLessThan(1.0, $queryTime, 'La consulta con relaciones es demasiado lenta');
    }

    /** @test */
    public function it_handles_large_number_of_vacancies_efficiently()
    {
        // Crear 500 vacantes
        $startTime = microtime(true);
        
        for ($i = 0; $i < 500; $i++) {
            $data = [
                'titulo' => 'Vacante ' . $i,
                'descripcion' => 'Descripción de la vacante ' . $i,
                'empresa' => 'Empresa ' . ($i % 20),
                'ubicacion' => ['Madrid', 'Barcelona', 'Valencia', 'Sevilla'][$i % 4],
                'tipo_contrato' => ['indefinido', 'temporal', 'freelance'][$i % 3],
                'salario_min' => rand(20000, 40000),
                'salario_max' => rand(45000, 80000),
                'estado' => ['activa', 'cerrada'][$i % 2],
                'user_id' => $this->user->id
            ];
            
            $this->vacanteService->create($data);
        }
        
        $creationTime = microtime(true) - $startTime;
        
        // Verificar que todas las vacantes se crearon
        $this->assertEquals(500, Vacante::count());
        
        // Probar búsqueda
        $startTime = microtime(true);
        $vacantes = $this->vacanteService->search('Vacante');
        $searchTime = microtime(true) - $startTime;
        
        $this->assertCount(500, $vacantes);
        
        // Probar filtro por ubicación
        $startTime = microtime(true);
        $madridVacantes = $this->vacanteService->getByLocation('Madrid');
        $filterTime = microtime(true) - $startTime;
        
        $this->assertGreaterThan(0, $madridVacantes->count());
        
        // Log de rendimiento
        $this->addWarning("Creación de 500 vacantes: {$creationTime}s");
        $this->addWarning("Búsqueda: {$searchTime}s");
        $this->addWarning("Filtro por ubicación: {$filterTime}s");
        
        // Verificar que los tiempos son razonables
        $this->assertLessThan(1.0, $searchTime, 'La búsqueda es demasiado lenta');
        $this->assertLessThan(0.5, $filterTime, 'El filtro es demasiado lento');
    }

    /** @test */
    public function it_handles_large_number_of_prospects_efficiently()
    {
        // Crear 1000 prospectos
        $startTime = microtime(true);
        
        for ($i = 0; $i < 1000; $i++) {
            $data = [
                'nombre' => 'Prospecto ' . $i,
                'email' => "prospecto{$i}@example.com",
                'telefono' => '+34' . rand(600000000, 699999999),
                'cv_path' => "cvs/prospecto_{$i}_cv.pdf",
                'experiencia_anos' => rand(0, 20),
                'skills' => ['Laravel', 'Vue.js', 'MySQL', 'Docker', 'AWS'][rand(0, 4)],
                'estado' => ['activo', 'inactivo', 'contratado'][rand(0, 2)],
                'user_id' => $this->user->id
            ];
            
            $this->prospectoService->create($data);
        }
        
        $creationTime = microtime(true) - $startTime;
        
        // Verificar que todos los prospectos se crearon
        $this->assertEquals(1000, Prospecto::count());
        
        // Probar búsqueda por nombre
        $startTime = microtime(true);
        $prospectos = $this->prospectoService->search('Prospecto');
        $searchTime = microtime(true) - $startTime;
        
        $this->assertCount(1000, $prospectos);
        
        // Probar filtro por experiencia
        $startTime = microtime(true);
        $seniorProspectos = $this->prospectoService->getByExperienceRange(5, 20);
        $filterTime = microtime(true) - $startTime;
        
        $this->assertGreaterThan(0, $seniorProspectos->count());
        
        // Log de rendimiento
        $this->addWarning("Creación de 1000 prospectos: {$creationTime}s");
        $this->addWarning("Búsqueda: {$searchTime}s");
        $this->addWarning("Filtro por experiencia: {$filterTime}s");
        
        // Verificar que los tiempos son razonables
        $this->assertLessThan(1.0, $searchTime, 'La búsqueda es demasiado lenta');
        $this->assertLessThan(0.5, $filterTime, 'El filtro es demasiado lento');
    }

    /** @test */
    public function it_handles_complex_queries_efficiently()
    {
        // Crear datos de prueba
        $vacantes = Vacante::factory()->count(20)->create(['user_id' => $this->user->id]);
        $prospectos = Prospecto::factory()->count(100)->create(['user_id' => $this->user->id]);
        
        // Crear entrevistas con diferentes estados y fechas
        for ($i = 0; $i < 500; $i++) {
            $data = [
                'vacante_id' => $vacantes->random()->id,
                'prospecto_id' => $prospectos->random()->id,
                'fecha' => '2024-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'hora' => str_pad(rand(9, 18), 2, '0', STR_PAD_LEFT) . ':30',
                'tipo' => ['presencial', 'remota'][rand(0, 1)],
                'estado' => ['programada', 'completada', 'cancelada'][rand(0, 2)],
                'notas' => 'Test complex query ' . $i,
                'user_id' => $this->user->id
            ];
            
            $this->entrevistaService->create($data);
        }
        
        // Probar consulta compleja con múltiples filtros
        $startTime = microtime(true);
        
        // Obtener entrevistas programadas para una vacante específica
        $vacante = $vacantes->first();
        $programadas = $this->entrevistaService->getByVacancy($vacante->id)
            ->where('estado', 'programada');
        
        $queryTime = microtime(true) - $startTime;
        
        $this->assertGreaterThan(0, $programadas->count());
        
        // Probar consulta con filtros de fecha
        $startTime = microtime(true);
        
        $entrevistasEnero = $this->entrevistaService->getAll()
            ->where('fecha', '>=', '2024-01-01')
            ->where('fecha', '<=', '2024-01-31');
        
        $dateFilterTime = microtime(true) - $startTime;
        
        $this->assertGreaterThan(0, $entrevistasEnero->count());
        
        // Log de rendimiento
        $this->addWarning("Consulta compleja: {$queryTime}s");
        $this->addWarning("Filtro por fecha: {$dateFilterTime}s");
        
        // Verificar que los tiempos son razonables
        $this->assertLessThan(0.5, $queryTime, 'La consulta compleja es demasiado lenta');
        $this->assertLessThan(0.5, $dateFilterTime, 'El filtro por fecha es demasiado lento');
    }

    /** @test */
    public function it_handles_memory_usage_efficiently()
    {
        // Crear datos de prueba
        $vacantes = Vacante::factory()->count(10)->create(['user_id' => $this->user->id]);
        $prospectos = Prospecto::factory()->count(50)->create(['user_id' => $this->user->id]);
        
        // Crear 2000 entrevistas
        for ($i = 0; $i < 2000; $i++) {
            $data = [
                'vacante_id' => $vacantes->random()->id,
                'prospecto_id' => $prospectos->random()->id,
                'fecha' => '2024-01-' . str_pad(rand(1, 31), 2, '0', STR_PAD_LEFT),
                'hora' => str_pad(rand(9, 18), 2, '0', STR_PAD_LEFT) . ':30',
                'tipo' => ['presencial', 'remota'][rand(0, 1)],
                'estado' => ['programada', 'completada', 'cancelada'][rand(0, 2)],
                'notas' => 'Test memory usage ' . $i,
                'user_id' => $this->user->id
            ];
            
            $this->entrevistaService->create($data);
        }
        
        // Medir uso de memoria antes de la consulta
        $memoryBefore = memory_get_usage(true);
        
        // Realizar consulta con relaciones
        $entrevistas = $this->entrevistaService->getAllWithRelationships();
        
        // Medir uso de memoria después de la consulta
        $memoryAfter = memory_get_usage(true);
        $memoryUsed = $memoryAfter - $memoryBefore;
        
        $this->assertCount(2000, $entrevistas);
        
        // Verificar que el uso de memoria es razonable (< 50MB)
        $memoryUsedMB = $memoryUsed / 1024 / 1024;
        $this->assertLessThan(50, $memoryUsedMB, "Uso de memoria demasiado alto: {$memoryUsedMB}MB");
        
        // Log de rendimiento
        $this->addWarning("Uso de memoria: {$memoryUsedMB}MB");
    }

    /** @test */
    public function it_handles_database_connection_efficiently()
    {
        // Verificar que no hay conexiones abiertas al inicio
        $initialConnections = DB::connection()->getPdo();
        
        // Crear datos de prueba
        $vacantes = Vacante::factory()->count(5)->create(['user_id' => $this->user->id]);
        $prospectos = Prospecto::factory()->count(10)->create(['user_id' => $this->user->id]);
        
        // Realizar múltiples operaciones
        for ($i = 0; $i < 100; $i++) {
            $data = [
                'vacante_id' => $vacantes->random()->id,
                'prospecto_id' => $prospectos->random()->id,
                'fecha' => '2024-01-15',
                'hora' => '14:30',
                'tipo' => 'presencial',
                'estado' => 'programada',
                'notas' => 'Test connection ' . $i,
                'user_id' => $this->user->id
            ];
            
            $this->entrevistaService->create($data);
            
            // Realizar consultas
            $this->entrevistaService->getAll();
            $this->vacanteService->getAll();
            $this->prospectoService->getAll();
        }
        
        // Verificar que la conexión sigue siendo la misma
        $finalConnections = DB::connection()->getPdo();
        $this->assertEquals($initialConnections, $finalConnections, 'Se crearon nuevas conexiones innecesariamente');
    }
} 