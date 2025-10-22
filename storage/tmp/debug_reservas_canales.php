<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Reserva;
use App\Models\CanalReserva;

$reservas = Reserva::latest()->take(20)->get();
$out = [];
foreach ($reservas as $r) {
    $canal = $r->canalReserva;
    $out[] = [
        'id' => $r->id,
        'idCanalReserva' => $r->idCanalReserva,
        'canal' => $canal ? $canal->toArray() : null,
        'costoPorNoche' => (string) $r->costoPorNoche,
        'cantidadNoches' => $r->cantidadNoches,
        'comisionCanal' => (string) $r->comisionCanal,
        'montoReserva' => (string) $r->montoReserva,
    ];
}
file_put_contents(__DIR__ . '/reservas_canales_debug.json', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Wrote storage/tmp/reservas_canales_debug.json\n";