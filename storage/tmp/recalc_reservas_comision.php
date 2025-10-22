<?php
// Dry-run script: recalcula comisiones y montos para reservas con comisionCanal o montoReserva = 0
// No actual updates are performed. Produce JSON report in storage/tmp/reservas_recalc_report.json

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
// Bootstrap the kernel (console context)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Reserva;
use App\Services\ReservaService;

$items = Reserva::where(function($q){
    $q->whereNull('comisionCanal')->orWhere('comisionCanal', 0);
})->orWhere(function($q){
    $q->whereNull('montoReserva')->orWhere('montoReserva', 0);
})->get();

$report = [];
foreach ($items as $r) {
    $calculatedComision = ReservaService::calcularComisionCanal($r->idCanalReserva, $r->costoPorNoche, $r->cantidadNoches);
    $calculatedMontoReserva = ReservaService::calcularMontoReserva($r->costoPorNoche, $r->cantidadNoches, $calculatedComision);
    $calculatedTotalAPagar = ReservaService::calcularTotalAPagar($r->costoPorNoche, $r->cantidadNoches, $r->montoLimpieza, $r->montoGarantia);
    $calculatedMontoEmpresa = ReservaService::calcularMontoEmpresaAdministradora($r->idDepartamento, $r->fechaInicio, $calculatedMontoReserva);
    $calculatedMontoPropietario = ReservaService::calcularMontoPropietario($r->idDepartamento, $r->fechaInicio, $calculatedMontoReserva);

    $report[] = [
        'id' => $r->id,
        'fecha_creacion' => $r->created_at?->toDateTimeString(),
        'idCanalReserva' => $r->idCanalReserva,
        'canal' => $r->canalReserva?->only(['id','nombre','comision']),
        'costoPorNoche' => $r->costoPorNoche,
        'cantidadNoches' => $r->cantidadNoches,
        'old' => [
            'comisionCanal' => $r->comisionCanal,
            'montoReserva' => $r->montoReserva,
            'totalAPagar' => $r->totalAPagar ?? null,
            'montoEmpresaAdministradora' => $r->montoEmpresaAdministradora,
            'montoPropietario' => $r->montoPropietario,
        ],
        'calculated' => [
            'comisionCanal' => $calculatedComision,
            'montoReserva' => $calculatedMontoReserva,
            'totalAPagar' => $calculatedTotalAPagar,
            'montoEmpresaAdministradora' => $calculatedMontoEmpresa,
            'montoPropietario' => $calculatedMontoPropietario,
        ],
    ];
}

$outputPath = __DIR__ . '/reservas_recalc_report.json';
file_put_contents($outputPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Processed: " . count($report) . " reservations. Report written to storage/tmp/reservas_recalc_report.json\n";
