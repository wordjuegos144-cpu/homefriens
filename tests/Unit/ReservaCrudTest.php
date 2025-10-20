<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Reserva;
use App\Models\Departamento;
use App\Models\Huesped;
use App\Models\CanalReserva;

class ReservaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_reserva()
    {
        $departamento = Departamento::factory()->create();
        $huesped = Huesped::factory()->create();
        $canal = CanalReserva::factory()->create();

        $reserva = Reserva::create([
            'idDepartamento' => $departamento->id,
            'idHuesped' => $huesped->id,
            'idCanalReserva' => $canal->id,
            'fechaInicio' => now(),
            'fechaFin' => now()->addDays(2),
            'estado' => 'Confirmada',
            'costoPorNoche' => 100,
            'cantidadHuespedes' => 2,
            'cantidadNoches' => 2,
            'descuentoAplicado' => 0,
            'comisionCanal' => 10,
            'montoReserva' => 200,
            'montoLimpieza' => 20,
            'montoGarantia' => 50,
            'montoEmpresaAdministradora' => 30,
            'montoPropietario' => 120,
        ]);

        $this->assertDatabaseHas('reservas', [
            'id' => $reserva->id,
            'idDepartamento' => $departamento->id,
            'idHuesped' => $huesped->id,
            'idCanalReserva' => $canal->id,
        ]);
    }

    public function test_read_reserva()
    {
        $reserva = Reserva::factory()->create();
        $found = Reserva::find($reserva->id);
        $this->assertNotNull($found);
        $this->assertEquals($reserva->id, $found->id);
    }

    public function test_update_reserva()
    {
        $reserva = Reserva::factory()->create(['estado' => 'Pendiente']);
        $reserva->update(['estado' => 'Cancelada']);
        $this->assertEquals('Cancelada', $reserva->fresh()->estado);
    }

    public function test_delete_reserva()
    {
        $reserva = Reserva::factory()->create();
        $id = $reserva->id;
        $reserva->delete();
        $this->assertDatabaseMissing('reservas', ['id' => $id]);
    }
}
