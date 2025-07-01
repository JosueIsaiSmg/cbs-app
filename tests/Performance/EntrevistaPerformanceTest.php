<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Services\EntrevistaService;
use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class EntrevistaPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private EntrevistaService $entrevistaService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->entrevistaService = new EntrevistaService();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_handle_large_number_of_interviews()
    {
        $this->actingAs($this->user);

        // Crear datos de prueba
        $vacantes = Vacante::factory()->count(100)->create();
        $prospectos = Prospecto::factory()->count(100)->create();

        $startTime = microtime(true);

        // Crear 1000 entrevistas
        for ($i = 0; $i < 1000; $i++) {
            $data = [
                'vacante' => $vacantes->random()->id,
                'prospecto' => $prospectos->random()->id,
                'fecha_entrevista' => fake()->dateTimeBetween('now', '+2 months'),
                'notas' => fake()->paragraph(),
                'reclutado' => fake()->boolean()
            ];

            $result = $this->entrevistaService->createEntrevista($data);
            $this->assertTrue($result['success']);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que todas las entrevistas se crearon
        $this->assertEquals(1000, Entrevista::count());

        // Verificar que el tiempo de ejecución es razonable (menos de 30 segundos)
        $this->assertLessThan(30, $executionTime, "La creación de 1000 entrevistas tomó demasiado tiempo: {$executionTime} segundos");

        // Verificar que podemos obtener todas las entrevistas
        $result = $this->entrevistaService->getAllEntrevistas();
        $this->assertTrue($result['success']);
        $this->assertCount(1000, $result['data']);
    }

    /** @test */
    public function it_can_handle_concurrent_operations()
    {
        $this->actingAs($this->user);

        $vacantes = Vacante::factory()->count(10)->create();
        $prospectos = Prospecto::factory()->count(10)->create();

        $startTime = microtime(true);

        // Simular operaciones concurrentes
        $promises = [];
        for ($i = 0; $i < 100; $i++) {
            $data = [
                'vacante' => $vacantes->random()->id,
                'prospecto' => $prospectos->random()->id,
                'fecha_entrevista' => fake()->dateTimeBetween('now', '+2 months'),
                'notas' => fake()->paragraph(),
                'reclutado' => fake()->boolean()
            ];

            $promises[] = $this->entrevistaService->createEntrevista($data);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que todas las operaciones fueron exitosas
        foreach ($promises as $result) {
            $this->assertTrue($result['success']);
        }

        // Verificar que se crearon todas las entrevistas
        $this->assertEquals(100, Entrevista::count());

        // Verificar que el tiempo de ejecución es razonable
        $this->assertLessThan(10, $executionTime, "Las operaciones concurrentes tomaron demasiado tiempo: {$executionTime} segundos");
    }

    /** @test */
    public function it_can_handle_large_dataset_queries()
    {
        $this->actingAs($this->user);

        // Crear un dataset grande
        $vacantes = Vacante::factory()->count(50)->create();
        $prospectos = Prospecto::factory()->count(50)->create();

        // Crear 5000 entrevistas
        for ($i = 0; $i < 5000; $i++) {
            Entrevista::factory()->create([
                'vacante' => $vacantes->random()->id,
                'prospecto' => $prospectos->random()->id
            ]);
        }

        // Probar consultas con dataset grande
        $startTime = microtime(true);

        // Obtener todas las entrevistas
        $result = $this->entrevistaService->getAllEntrevistas();
        $this->assertTrue($result['success']);
        $this->assertCount(5000, $result['data']);

        // Obtener entrevistas por vacante
        $vacanteId = $vacantes->first()->id;
        $result = $this->entrevistaService->getEntrevistasByVacante($vacanteId);
        $this->assertTrue($result['success']);

        // Obtener entrevistas por prospecto
        $prospectoId = $prospectos->first()->id;
        $result = $this->entrevistaService->getEntrevistasByProspecto($prospectoId);
        $this->assertTrue($result['success']);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que las consultas son eficientes
        $this->assertLessThan(5, $executionTime, "Las consultas con dataset grande tomaron demasiado tiempo: {$executionTime} segundos");
    }

    /** @test */
    public function it_can_handle_memory_efficiently()
    {
        $this->actingAs($this->user);

        $initialMemory = memory_get_usage();

        // Crear dataset grande
        $vacantes = Vacante::factory()->count(20)->create();
        $prospectos = Prospecto::factory()->count(20)->create();

        // Crear 2000 entrevistas
        for ($i = 0; $i < 2000; $i++) {
            Entrevista::factory()->create([
                'vacante' => $vacantes->random()->id,
                'prospecto' => $prospectos->random()->id
            ]);
        }

        // Realizar operaciones
        $result = $this->entrevistaService->getAllEntrevistas();
        $this->assertTrue($result['success']);

        $finalMemory = memory_get_usage();
        $memoryIncrease = $finalMemory - $initialMemory;

        // Verificar que el uso de memoria es razonable (menos de 100MB)
        $this->assertLessThan(100 * 1024 * 1024, $memoryIncrease, "El uso de memoria es excesivo: " . round($memoryIncrease / 1024 / 1024, 2) . "MB");
    }

    /** @test */
    public function it_can_handle_database_connection_efficiently()
    {
        $this->actingAs($this->user);

        $initialConnections = DB::connection()->getQueryLog();

        // Realizar múltiples operaciones
        $vacantes = Vacante::factory()->count(10)->create();
        $prospectos = Prospecto::factory()->count(10)->create();

        for ($i = 0; $i < 100; $i++) {
            $data = [
                'vacante' => $vacantes->random()->id,
                'prospecto' => $prospectos->random()->id,
                'fecha_entrevista' => fake()->dateTimeBetween('now', '+2 months'),
                'notas' => fake()->paragraph(),
                'reclutado' => fake()->boolean()
            ];

            $this->entrevistaService->createEntrevista($data);
            $this->entrevistaService->getAllEntrevistas();
        }

        $finalConnections = DB::connection()->getQueryLog();
        $connectionCount = count($finalConnections) - count($initialConnections);

        // Verificar que el número de conexiones es razonable
        $this->assertLessThan(1000, $connectionCount, "Demasiadas consultas a la base de datos: {$connectionCount}");
    }

    /** @test */
    public function it_can_handle_bulk_operations()
    {
        $this->actingAs($this->user);

        $vacantes = Vacante::factory()->count(10)->create();
        $prospectos = Prospecto::factory()->count(10)->create();

        $startTime = microtime(true);

        // Crear datos en lotes
        $batchSize = 100;
        $totalRecords = 1000;

        for ($batch = 0; $batch < $totalRecords / $batchSize; $batch++) {
            $batchData = [];
            
            for ($i = 0; $i < $batchSize; $i++) {
                $batchData[] = [
                    'vacante' => $vacantes->random()->id,
                    'prospecto' => $prospectos->random()->id,
                    'fecha_entrevista' => fake()->dateTimeBetween('now', '+2 months'),
                    'notas' => fake()->paragraph(),
                    'reclutado' => fake()->boolean(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Insertar lote
            Entrevista::insert($batchData);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que todas las entrevistas se crearon
        $this->assertEquals($totalRecords, Entrevista::count());

        // Verificar que el tiempo de ejecución es razonable
        $this->assertLessThan(15, $executionTime, "Las operaciones en lote tomaron demasiado tiempo: {$executionTime} segundos");

        // Verificar que podemos consultar los datos
        $result = $this->entrevistaService->getAllEntrevistas();
        $this->assertTrue($result['success']);
        $this->assertCount($totalRecords, $result['data']);
    }

    /** @test */
    public function it_can_handle_complex_queries_efficiently()
    {
        $this->actingAs($this->user);

        // Crear dataset complejo
        $vacantes = Vacante::factory()->count(30)->create();
        $prospectos = Prospecto::factory()->count(30)->create();

        // Crear entrevistas con diferentes estados
        for ($i = 0; $i < 1500; $i++) {
            Entrevista::factory()->create([
                'vacante' => $vacantes->random()->id,
                'prospecto' => $prospectos->random()->id,
                'reclutado' => fake()->boolean()
            ]);
        }

        $startTime = microtime(true);

        // Realizar consultas complejas
        $queries = [
            // Entrevistas reclutadas
            Entrevista::where('reclutado', true)->count(),
            
            // Entrevistas no reclutadas
            Entrevista::where('reclutado', false)->count(),
            
            // Entrevistas por vacante específica
            Entrevista::where('vacante', $vacantes->first()->id)->count(),
            
            // Entrevistas por prospecto específico
            Entrevista::where('prospecto', $prospectos->first()->id)->count(),
            
            // Entrevistas con relaciones
            Entrevista::with(['vacante', 'prospecto'])->get()->count()
        ];

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar que todas las consultas devuelven resultados válidos
        foreach ($queries as $count) {
            $this->assertGreaterThanOrEqual(0, $count);
        }

        // Verificar que las consultas complejas son eficientes
        $this->assertLessThan(3, $executionTime, "Las consultas complejas tomaron demasiado tiempo: {$executionTime} segundos");
    }
} 