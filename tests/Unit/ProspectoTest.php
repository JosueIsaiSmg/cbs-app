<?php

namespace Tests\Unit;

use App\Models\Prospecto;
use App\Models\Entrevista;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProspectoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_prospecto()
    {
        $prospecto = Prospecto::create([
            'nombre' => 'Juan Pérez',
            'correo' => 'juan@example.com',
            'fecha_registro' => '2024-01-15',
        ]);

        $this->assertInstanceOf(Prospecto::class, $prospecto);
        $this->assertEquals('Juan Pérez', $prospecto->nombre);
        $this->assertEquals('juan@example.com', $prospecto->correo);
        $this->assertEquals('2024-01-15', $prospecto->fecha_registro);
    }

    /** @test */
    public function it_has_entrevistas_relationship()
    {
        $prospecto = Prospecto::factory()->create();
        $entrevista = Entrevista::factory()->create([
            'prospecto' => $prospecto->id
        ]);

        $this->assertInstanceOf(Entrevista::class, $prospecto->entrevistas->first());
        $this->assertEquals($entrevista->id, $prospecto->entrevistas->first()->id);
    }

    /** @test */
    public function it_can_update_prospecto()
    {
        $prospecto = Prospecto::create([
            'nombre' => 'Juan Pérez',
            'correo' => 'juan@example.com',
            'fecha_registro' => '2024-01-15',
        ]);

        $prospecto->update([
            'nombre' => 'Juan Carlos Pérez',
            'correo' => 'juancarlos@example.com',
            'fecha_registro' => '2024-01-20',
        ]);

        $this->assertEquals('Juan Carlos Pérez', $prospecto->nombre);
        $this->assertEquals('juancarlos@example.com', $prospecto->correo);
        $this->assertEquals('2024-01-20', $prospecto->fecha_registro);
    }

    /** @test */
    public function it_can_delete_prospecto()
    {
        $prospecto = Prospecto::create([
            'nombre' => 'Juan Pérez',
            'correo' => 'juan@example.com',
            'fecha_registro' => '2024-01-15',
        ]);

        $prospectoId = $prospecto->id;
        
        $this->assertDatabaseHas('prospectos', [
            'id' => $prospectoId,
        ]);

        $prospecto->delete();

        $this->assertDatabaseMissing('prospectos', [
            'id' => $prospectoId,
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'nombre',
            'correo', 
            'fecha_registro',
        ];

        $this->assertEquals($fillable, (new Prospecto())->getFillable());
    }

    /** @test */
    public function it_can_access_entrevistas_count()
    {
        $prospecto = Prospecto::factory()->create();
        Entrevista::factory()->count(3)->create([
            'prospecto' => $prospecto->id
        ]);

        $this->assertEquals(3, $prospecto->entrevistas()->count());
    }
} 