<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$h = App\Models\Huesped::find(1);
if (!$h) {
    echo "Huesped not found\n";
    exit(0);
}
echo json_encode($h->toArray(), JSON_PRETTY_PRINT) . "\n";
