<?php

namespace Tests\Unit;

use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntrevistaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_entrevista()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();

        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista de prueba',
            'reclutado' => false,
        ]);

        $this->assertInstanceOf(Entrevista::class, $entrevista);
        $this->assertEquals($vacante->id, $entrevista->vacante);
        $this->assertEquals($prospecto->id, $entrevista->prospecto);
        $this->assertEquals('2024-01-15', $entrevista->fecha_entrevista);
        $this->assertEquals('Entrevista de prueba', $entrevista->notas);
        $this->assertFalse($entrevista->reclutado);
    }

    /** @test */
    public function it_has_vacante_relationship()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista de prueba',
            'reclutado' => false,
        ]);

        $this->assertInstanceOf(Vacante::class, $entrevista->vacante);
        $this->assertEquals($vacante->id, $entrevista->vacante->id);
        $this->assertEquals($vacante->area, $entrevista->vacante->area);
    }

    /** @test */
    public function it_has_prospecto_relationship()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista de prueba',
            'reclutado' => false,
        ]);

        $this->assertInstanceOf(Prospecto::class, $entrevista->prospecto);
        $this->assertEquals($prospecto->id, $entrevista->prospecto->id);
        $this->assertEquals($prospecto->nombre, $entrevista->prospecto->nombre);
    }

    /** @test */
    public function it_can_update_entrevista()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista original',
            'reclutado' => false,
        ]);

        $entrevista->update([
            'fecha_entrevista' => '2024-01-20',
            'notas' => 'Entrevista actualizada',
            'reclutado' => true,
        ]);

        $this->assertEquals('2024-01-20', $entrevista->fecha_entrevista);
        $this->assertEquals('Entrevista actualizada', $entrevista->notas);
        $this->assertTrue($entrevista->reclutado);
    }

    /** @test */
    public function it_can_delete_entrevista()
    {
        $vacante = Vacante::factory()->create();
        $prospecto = Prospecto::factory()->create();
        
        $entrevista = Entrevista::create([
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
            'fecha_entrevista' => '2024-01-15',
            'notas' => 'Entrevista para eliminar',
            'reclutado' => false,
        ]);

        $entrevistaId = $entrevista->vacante . '-' . $entrevista->prospecto;
        
        $this->assertDatabaseHas('entrevistas', [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
        ]);

        $entrevista->delete();

        $this->assertDatabaseMissing('entrevistas', [
            'vacante' => $vacante->id,
            'prospecto' => $prospecto->id,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'vacante',
            'prospecto', 
            'fecha_entrevista',
            'notas',
            'reclutado',
        ];

        $this->assertEquals($fillable, (new Entrevista())->getFillable());
    }
} 