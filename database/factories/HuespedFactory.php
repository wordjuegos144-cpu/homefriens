<?php

namespace Database\Factories;

use App\Models\Huesped;
use Illuminate\Database\Eloquent\Factories\Factory;

class HuespedFactory extends Factory
{
    protected $model = Huesped::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'Whatsapp' => $this->faker->phoneNumber(),
            'numeroDocumento' => $this->faker->unique()->numerify('########'),
            'enListaNegra' => $this->faker->boolean(10), // 10% probabilidad en lista negra
        ];
    }
}
