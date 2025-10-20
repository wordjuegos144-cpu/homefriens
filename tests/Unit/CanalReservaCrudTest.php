<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CanalReserva;

class CanalReservaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_canal_reserva()
    {
        $canal = CanalReserva::create([
            'nombre' => 'Airbnb',
        ]);
        $this->assertDatabaseHas('canal_reservas', [
            'id' => $canal->id,
            'nombre' => 'Airbnb',
        ]);
    }

    public function test_read_canal_reserva()
    {
        $canal = CanalReserva::factory()->create();
        $found = CanalReserva::find($canal->id);
        $this->assertNotNull($found);
        $this->assertEquals($canal->id, $found->id);
    }

    public function test_update_canal_reserva()
    {
        $canal = CanalReserva::factory()->create(['nombre' => 'Original']);
        $canal->update(['nombre' => 'Modificado']);
        $this->assertEquals('Modificado', $canal->fresh()->nombre);
    }

    public function test_delete_canal_reserva()
    {
        $canal = CanalReserva::factory()->create();
        $id = $canal->id;
        $canal->delete();
        $this->assertDatabaseMissing('canal_reservas', ['id' => $id]);
    }
}
