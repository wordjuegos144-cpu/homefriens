<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;
use App\Models\EmpresaAdministradora;

class DepartamentosSeeder extends Seeder
{
    public function run()
    {
        // Crear una empresa administradora si no existe
        $empresa = EmpresaAdministradora::first() ?? EmpresaAdministradora::factory()->create([
            'nombre' => 'Administradora Principal',
            'direccion' => 'Calle Principal 123',
            'telefono' => '123456789'
        ]);

        // Crear 5 departamentos de prueba
        Departamento::factory()
            ->count(5)
            ->create([
                'idEmpresaAdministradora' => $empresa->id
            ]);
    }
}