<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EmpresaAdministradora;
use App\Models\Propietario;
use App\Models\Departamento;
use App\Models\Huesped;
use App\Models\CanalReserva;
use App\Models\Reserva;
use App\Models\Limpieza;

// Create or reuse minimal dependencies
$empresa = EmpresaAdministradora::first() ?: EmpresaAdministradora::create(['nombre' => 'Test Admin']);
$prop = Propietario::first() ?: Propietario::create(['nombre' => 'Test Owner', 'email' => 'owner@example.com', 'telefono' => '777-7777', 'direccion' => 'Calle Test']);

auth()->logout(); // ensure no tenant/auth issues

$departamento = Departamento::create([
    'idEmpresaAdministradora' => $empresa->id,
    'idPropietario' => $prop->id,
    'nombreEdificio' => 'Test Edificio',
    'direccion' => 'Calle Falsa 123, Santa Cruz',
    'descripcion' => 'Depto prueba',
    'piso' => 1,
    'numero' => 101,
    'capacidadNormal' => 2,
    'capacidadExtra' => 0,
    'telefonoPropietario' => $prop->telefono,
    'camas' => 1,
    'cuartos' => 1,
    'banos' => 1,
    'imagenes' => [],
    'servicios' => [],
]);

$huesped = Huesped::first() ?: Huesped::create(['nombre' => 'Test Huesped', 'email' => 'guest@example.com', 'telefono' => '999-9999']);
$canal = CanalReserva::first() ?: CanalReserva::create(['nombre' => 'Manual']);

// Create reserva in Pendiente
$reserva = Reserva::create([
    'idDepartamento' => $departamento->id,
    'idHuesped' => $huesped->id,
    'idCanalReserva' => $canal->id,
    'fechaInicio' => now(),
    'fechaFin' => now()->addDays(2),
    'estado' => 'Pendiente',
    'costoPorNoche' => 100,
    'cantidadHuespedes' => 1,
    'cantidadNoches' => 2,
    'montoLimpieza' => 20,
]);

echo "Reserva creada: id={$reserva->id}, estado={$reserva->estado}\n";
$l = Limpieza::where('reserva_id', $reserva->id)->first();
echo "Limpieza antes: " . ($l ? $l->estado : 'none') . "\n";

// Change estado to Confirmada
$reserva->estado = 'Confirmada';
$reserva->save();

$reserva->refresh();
$l2 = Limpieza::where('reserva_id', $reserva->id)->first();
echo "Reserva ahora: id={$reserva->id}, estado={$reserva->estado}\n";
echo "Limpieza despues: " . ($l2 ? $l2->estado : 'none') . "\n";

exit(0);
