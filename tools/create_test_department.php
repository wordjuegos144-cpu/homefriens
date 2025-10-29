<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EmpresaAdministradora;
use App\Models\Propietario;
use App\Models\Departamento;

$admin = EmpresaAdministradora::create(['nombre' => 'Test Admin']);
$prop = Propietario::create(['nombre' => 'Test Owner', 'email' => 'owner@example.com', 'telefono' => '123', 'direccion' => 'Santa Cruz']);

$dep = Departamento::create([
    'idEmpresaAdministradora' => $admin->id,
    'idPropietario' => $prop->id,
    'nombreEdificio' => 'Test Edificio',
    'direccion' => 'Calle Falsa 123, Santa Cruz, Bolivia',
    'descripcion' => 'Departamento de prueba',
    'piso' => 1,
    'numero' => 101,
    'capacidadNormal' => 2,
    'capacidadExtra' => 0,
    'telefonoPropietario' => '123',
    'camas' => 1,
    'cuartos' => 1,
    'banos' => 1,
    'imagenes' => [],
    'servicios' => [],
]);

echo "Created departamento id: {$dep->id}\n";
