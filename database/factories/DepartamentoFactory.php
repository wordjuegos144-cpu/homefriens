<?php

namespace Database\Factories;

use App\Models\Departamento;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartamentoFactory extends Factory
{
    protected $model = Departamento::class;

    public function definition(): array
    {
        return [
            'idEmpresaAdministradora' => \App\Models\EmpresaAdministradora::factory(),
            'nombreEdificio' => $this->faker->company(),
            'direccion' => $this->faker->address(),
            'piso' => $this->faker->numberBetween(1, 10),
            'numero' => $this->faker->numberBetween(1, 100),
            'capacidadNormal' => $this->faker->numberBetween(1, 6),
            'capacidadExtra' => $this->faker->numberBetween(0, 4),
            'nombrePropietario' => $this->faker->name(),
            'telefonoPropietario' => $this->faker->phoneNumber(),
        ];
    }
}
