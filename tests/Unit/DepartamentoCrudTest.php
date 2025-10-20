<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Departamento;

class DepartamentoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_departamento()
    {
        $empresa = \App\Models\EmpresaAdministradora::factory()->create();
        $departamento = Departamento::create([
            'idEmpresaAdministradora' => $empresa->id,
            'nombreEdificio' => 'Edificio Test',
            'piso' => 1,
            'numero' => 'A1',
            'direccion' => 'Calle Falsa 123',
            'capacidadNormal' => 2,
            'capacidadExtra' => 0,
            'nombrePropietario' => 'Propietario Test',
            'telefonoPropietario' => '123456789',
        ]);
        $this->assertDatabaseHas('departamentos', [
            'id' => $departamento->id,
            'nombreEdificio' => 'Edificio Test',
        ]);
    }

    public function test_read_departamento()
    {
        $departamento = Departamento::factory()->create();
        $found = Departamento::find($departamento->id);
        $this->assertNotNull($found);
        $this->assertEquals($departamento->id, $found->id);
    }

    public function test_update_departamento()
    {
        $departamento = Departamento::factory()->create(['nombreEdificio' => 'Original']);
        $departamento->update(['nombreEdificio' => 'Modificado']);
        $this->assertEquals('Modificado', $departamento->fresh()->nombreEdificio);
    }

    public function test_delete_departamento()
    {
        $departamento = Departamento::factory()->create();
        $id = $departamento->id;
        $departamento->delete();
        $this->assertDatabaseMissing('departamentos', ['id' => $id]);
    }
}
