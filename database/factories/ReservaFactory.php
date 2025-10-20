<?php

namespace Database\Factories;

use App\Models\Reserva;
use App\Models\Departamento;
use App\Models\Huesped;
use App\Models\CanalReserva;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservaFactory extends Factory
{
    protected $model = Reserva::class;

    public function definition(): array
    {
        return [
            'idDepartamento' => Departamento::factory(),
            'idHuesped' => Huesped::factory(),
            'idCanalReserva' => CanalReserva::factory(),
            'fechaInicio' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'fechaFin' => $this->faker->dateTimeBetween('+2 days', '+2 months'),
            'estado' => $this->faker->randomElement(['Confirmada', 'Cancelada', 'Pendiente']),
            'costoPorNoche' => $this->faker->randomFloat(2, 50, 500),
            'cantidadHuespedes' => $this->faker->numberBetween(1, 6),
            'cantidadNoches' => $this->faker->numberBetween(1, 10),
            'descuentoAplicado' => 0,
            'comisionCanal' => 10,
            'montoReserva' => 200,
            'montoLimpieza' => 20,
            'montoGarantia' => 50,
            'montoEmpresaAdministradora' => 30,
            'montoPropietario' => 120,
        ];
    }
}
