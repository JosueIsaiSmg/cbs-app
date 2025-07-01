<?php

namespace Tests\Unit;

use App\Models\Vacante;
use App\Models\Entrevista;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacanteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_vacante()
    {
        $vacante = Vacante::create([
            'area' => 'Desarrollo Web',
            'sueldo' => 15000,
            'activo' => true,
        ]);

        $this->assertInstanceOf(Vacante::class, $vacante);
        $this->assertEquals('Desarrollo Web', $vacante->area);
        $this->assertEquals(15000, $vacante->sueldo);
        $this->assertTrue($vacante->activo);
    }

    /** @test */
    public function it_has_entrevistas_relationship()
    {
        $vacante = Vacante::factory()->create();
        $entrevista = Entrevista::factory()->create([
            'vacante' => $vacante->id
        ]);

        $vacante = $vacante->fresh('entrevistas');
        dump($vacante->entrevistas->pluck('id'), $entrevista->id);

        $this->assertInstanceOf(Entrevista::class, $vacante->entrevistas->first());
        $this->assertEquals($entrevista->id, $vacante->entrevistas->first()->id);
    }

    /** @test */
    public function it_can_update_vacante()
    {
        $vacante = Vacante::create([
            'area' => 'Desarrollo Web',
            'sueldo' => 15000,
            'activo' => true,
        ]);

        $vacante->update([
            'area' => 'Desarrollo Full Stack',
            'sueldo' => 18000,
            'activo' => false,
        ]);

        $this->assertEquals('Desarrollo Full Stack', $vacante->area);
        $this->assertEquals(18000, $vacante->sueldo);
        $this->assertFalse($vacante->activo);
    }

    /** @test */
    public function it_can_delete_vacante()
    {
        $vacante = Vacante::create([
            'area' => 'Desarrollo Web',
            'sueldo' => 15000,
            'activo' => true,
        ]);

        $vacanteId = $vacante->id;
        
        $this->assertDatabaseHas('vacantes', [
            'id' => $vacanteId,
        ]);

        $vacante->delete();

        $this->assertDatabaseMissing('vacantes', [
            'id' => $vacanteId,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'area',
            'sueldo', 
            'activo',
        ];

        $this->assertEquals($fillable, (new Vacante())->getFillable());
    }

    /** @test */
    public function it_can_access_entrevistas_count()
    {
        $vacante = Vacante::factory()->create();
        Entrevista::factory()->count(2)->create([
            'vacante' => $vacante->id
        ]);

        $this->assertEquals(2, $vacante->entrevistas()->count());
    }

    /** @test */
    public function it_can_scope_active_vacantes()
    {
        Vacante::factory()->create(['activo' => true]);
        Vacante::factory()->create(['activo' => true]);
        Vacante::factory()->create(['activo' => false]);

        $activeVacantes = Vacante::where('activo', true)->get();

        $this->assertEquals(2, $activeVacantes->count());
    }

    /** @test */
    public function it_can_scope_by_area()
    {
        Vacante::factory()->create(['area' => 'Desarrollo']);
        Vacante::factory()->create(['area' => 'Desarrollo']);
        Vacante::factory()->create(['area' => 'Marketing']);

        $desarrolloVacantes = Vacante::where('area', 'Desarrollo')->get();

        $this->assertEquals(2, $desarrolloVacantes->count());
    }
} 