<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MetaMensual;

class ActualizarMetasMensuales extends Command
{
    protected $signature = 'metas:actualizar';
    protected $description = 'Actualiza los valores actuales y porcentajes de todas las metas mensuales';

    public function handle()
    {
        $metas = MetaMensual::all();
        $count = 0;

        foreach ($metas as $meta) {
            $observer = new \App\Observers\MetaMensualObserver();
            $observer->updated($meta);
            $count++;
        }

        $this->info("Se actualizaron {$count} metas mensuales.");
    }
}