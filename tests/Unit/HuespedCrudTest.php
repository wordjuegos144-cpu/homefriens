<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Huesped;

class HuespedCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_huesped()
    {
        $huesped = Huesped::create([
            'nombre' => 'Juan Test',
            'Whatsapp' => '123456789',
            'numeroDocumento' => '99999999',
            'enListaNegra' => false,
        ]);
        $this->assertDatabaseHas('huespeds', [
            'id' => $huesped->id,
            'nombre' => 'Juan Test',
        ]);
    }

    public function test_read_huesped()
    {
        $huesped = Huesped::factory()->create();
        $found = Huesped::find($huesped->id);
        $this->assertNotNull($found);
        $this->assertEquals($huesped->id, $found->id);
    }

    public function test_update_huesped()
    {
        $huesped = Huesped::factory()->create(['nombre' => 'Original']);
        $huesped->update(['nombre' => 'Modificado']);
        $this->assertEquals('Modificado', $huesped->fresh()->nombre);
    }

    public function test_delete_huesped()
    {
        $huesped = Huesped::factory()->create();
        $id = $huesped->id;
        $huesped->delete();
        $this->assertDatabaseMissing('huespeds', ['id' => $id]);
    }
}
