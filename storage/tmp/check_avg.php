<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = $argv[1] ?? null;
if (!$id) {
    echo "Usage: php check_avg.php <idHuesped>\n";
    exit(1);
}

use App\Models\Calificacion;
use App\Models\Resena;

$avg = Calificacion::averageForHuesped((int)$id);
$recent = Resena::where('idHuesped', $id)->orderByDesc('fecha')->limit(5)->get();

echo "Huesped ID: $id\n";
echo "Promedio: ".($avg === null ? 'null' : number_format($avg,2))."\n";
echo "Reseñas encontradas: ".count($recent)."\n";
foreach ($recent as $r) {
    $fecha = $r->fecha ? $r->fecha->format('Y-m-d') : 'n/a';
    $valor = $r->valor ?? '-';
    $texto = $r->argumento ?? '-';
    echo "- [$fecha] (valor $valor): $texto\n";
}
