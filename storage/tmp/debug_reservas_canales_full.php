<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Reserva;

$reservas = Reserva::latest()->take(20)->get();
$out = [];
foreach ($reservas as $r) {
    $out[] = [
        'id' => $r->id,
        'created_at' => (string) $r->created_at,
        'updated_at' => (string) $r->updated_at,
        'idCanalReserva' => $r->idCanalReserva,
        'canal' => $r->canalReserva ? $r->canalReserva->toArray() : null,
        'costoPorNoche' => $r->costoPorNoche,
        'cantidadNoches' => $r->cantidadNoches,
        'comisionCanal' => $r->comisionCanal,
        'montoReserva' => $r->montoReserva,
        'attributes' => $r->getAttributes(),
    ];
}
file_put_contents(__DIR__ . '/reservas_canales_full.json', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Wrote storage/tmp/reservas_canales_full.json\n";