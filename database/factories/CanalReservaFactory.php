<?php

namespace Database\Factories;

use App\Models\CanalReserva;
use Illuminate\Database\Eloquent\Factories\Factory;

class CanalReservaFactory extends Factory
{
    protected $model = CanalReserva::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->company(),
            'comision' => $this->faker->randomFloat(2, 0, 30), // Valor de ejemplo entre 0 y 30
        ];
    }
}
