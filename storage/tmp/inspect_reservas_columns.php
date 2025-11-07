<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$cols = Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM reservas');
foreach ($cols as $c) {
    echo $c->Field . ' ' . $c->Type . ' ' . ($c->Null === 'NO' ? 'NOT NULL' : 'NULL') . ' Default:' . ($c->Default === NULL ? 'NULL' : $c->Default) . PHP_EOL;
}
