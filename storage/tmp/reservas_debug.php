<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Departamento;
use App\Models\Reserva;
use Illuminate\Support\Carbon;

$departamentos = Departamento::take(5)->get();
$now = Carbon::now();
$start = $now->copy()->startOfMonth();
$end = $now->copy()->endOfMonth();

$out = [];
foreach ($departamentos as $d) {
    $reservas = Reserva::where('idDepartamento', $d->id)
        ->where('estado', 'Confirmada')
        ->where(function($q) use ($start, $end) {
            $q->where('fechaInicio', '<=', $end)
              ->where('fechaFin', '>=', $start);
        })->get();
    $rows = [];
    foreach ($reservas as $r) {
        $rows[] = [
            'id' => $r->id,
            'fechaInicio' => (string)$r->fechaInicio,
            'fechaFin' => (string)$r->fechaFin,
            'cantidadNoches' => $r->cantidadNoches ?? null,
            'costoPorNoche' => $r->costoPorNoche ?? null,
            'montoReserva' => $r->montoReserva ?? null,
            'comisionCanal' => $r->comisionCanal ?? null,
            'montoEmpresaAdministradora' => $r->montoEmpresaAdministradora ?? null,
            'montoLimpieza' => $r->montoLimpieza ?? null,
        ];
    }
    $out[] = [
        'departamento' => $d->id . ' - ' . $d->nombreEdificio,
        'reservas' => $rows,
    ];
}
echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
