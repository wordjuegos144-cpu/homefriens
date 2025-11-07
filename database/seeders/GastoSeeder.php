<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gasto;
use App\Models\Departamento;
use Carbon\Carbon;

class GastoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener un departamento existente (o el primero)
        $departamento = Departamento::first();
        
        if (!$departamento) {
            $this->command->info('No hay departamentos. Crea al menos uno primero.');
            return;
        }

        // Ejemplos de gastos comunes
        $gastos = [
            [
                'idDepartamento' => $departamento->id,
                'monto' => 150.00,
                'tipo' => 'Mantenimiento',
                'fecha' => Carbon::now()->subDays(5),
                'estado' => 'Pendiente',
                'descripcion' => 'Reparación de grifería en baño principal',
            ],
            [
                'idDepartamento' => $departamento->id,
                'monto' => 80.00,
                'tipo' => 'Servicios',
                'fecha' => Carbon::now()->subDays(2),
                'estado' => 'Pendiente',
                'descripcion' => 'Pago de servicio de internet mensual',
            ],
            [
                'idDepartamento' => $departamento->id,
                'monto' => 45.00,
                'tipo' => 'Suministros',
                'fecha' => Carbon::now()->subDays(1),
                'estado' => 'Cubierto',
                'descripcion' => 'Reposición de artículos de limpieza',
            ],
        ];

        foreach ($gastos as $gasto) {
            Gasto::create($gasto);
        }

        $this->command->info('Gastos de ejemplo creados exitosamente.');
    }
}