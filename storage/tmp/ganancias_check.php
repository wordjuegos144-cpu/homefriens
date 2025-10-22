<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Departamento;
use App\Models\GanaciaDeDepartamento;

$departamentos = Departamento::take(5)->get();
$result = [];
foreach ($departamentos as $d) {
    $r = GanaciaDeDepartamento::calcularGanancias($d->id, 'mensual');
    $r['departamento'] = $d->nombreEdificio;
    $result[] = $r;
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
