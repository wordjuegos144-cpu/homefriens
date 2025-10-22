<?php
// One-off script to apply calculated values to DB for problematic reservas (ids from dry-run report).
// WARNING: This will write to your DB. Run only if you agree.

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Reserva;
use Illuminate\Support\Facades\DB;

$reportPath = __DIR__ . '/reservas_recalc_report.json';
if (!file_exists($reportPath)) {
    echo "Report not found at $reportPath\n";
    exit(1);
}
$data = json_decode(file_get_contents($reportPath), true);
if (empty($data)) {
    echo "No items to apply\n";
    exit(0);
}

DB::beginTransaction();
try {
    foreach ($data as $item) {
        $id = $item['id'];
        $calculated = $item['calculated'];
        $reserva = Reserva::find($id);
        if (!$reserva) continue;
        $reserva->comisionCanal = $calculated['comisionCanal'];
        $reserva->montoReserva = $calculated['montoReserva'];
        $reserva->montoEmpresaAdministradora = $calculated['montoEmpresaAdministradora'];
        $reserva->montoPropietario = $calculated['montoPropietario'];
        $reserva->save();
        echo "Updated reserva id=$id\n";
    }
    DB::commit();
    echo "All done.\n";
} catch (\Throwable $e) {
    DB::rollBack();
    echo "Failed: " . $e->getMessage() . "\n";
}
