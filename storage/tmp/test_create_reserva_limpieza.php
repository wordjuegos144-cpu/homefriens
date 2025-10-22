<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Reserva;
use App\Models\Departamento;
use App\Models\Huesped;
use App\Models\CanalReserva;

// Create minimal related records if not exist
$departamento = Departamento::first() ?? Departamento::factory()->create();
$huesped = Huesped::first() ?? Huesped::factory()->create();
$canal = CanalReserva::first() ?? CanalReserva::factory()->create(['comision' => 10]);

$reserva = Reserva::create([
    'idDepartamento' => $departamento->id,
    'idHuesped' => $huesped->id,
    'idCanalReserva' => $canal->id,
    'fechaInicio' => now()->toDateString(),
    'fechaFin' => now()->addDays(2)->toDateString(),
    'estado' => 'Confirmada',
    'costoPorNoche' => 100,
    'cantidadHuespedes' => 2,
    'cantidadNoches' => 2,
    'montoLimpieza' => 25,
    'montoGarantia' => 50,
]);

$reserva->load('limpieza');
echo "Reserva created id={$reserva->id}\n";
if ($reserva->limpieza) {
    echo "Limpieza created: id={$reserva->limpieza->id}, monto={$reserva->limpieza->monto}\n";
} else {
    echo "No limpieza created.\n";
}
