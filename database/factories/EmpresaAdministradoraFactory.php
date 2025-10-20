<?php

namespace Database\Factories;

use App\Models\EmpresaAdministradora;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaAdministradoraFactory extends Factory
{
    protected $model = EmpresaAdministradora::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company(),
        ];
    }
}
