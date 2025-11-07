<?php

namespace App\Observers;

use App\Models\Reserva;
use App\Models\MetaMensual;
use Carbon\Carbon;

class ReservaObserver
{
    /**
     * Handle the Reserva "created" event.
     */
    public function created(Reserva $reserva): void
    {
        $this->actualizarMetasMensuales($reserva);
    }

    /**
     * Handle the Reserva "updated" event.
     */
    public function updated(Reserva $reserva): void
    {
        $this->actualizarMetasMensuales($reserva);
    }

    /**
     * Handle the Reserva "deleted" event.
     */
    public function deleted(Reserva $reserva): void
    {
        $this->actualizarMetasMensuales($reserva);
    }

    /**
     * Actualiza las metas mensuales relacionadas con la reserva
     */
    protected function actualizarMetasMensuales(Reserva $reserva): void
    {
        // Solo procesar reservas confirmadas
        if ($reserva->estado !== 'Confirmada') {
            return;
        }

        $fechaInicio = Carbon::parse($reserva->fechaInicio);
        
        // Buscar la meta mensual correspondiente
        $meta = MetaMensual::where('idDepartamento', $reserva->idDepartamento)
            ->where('mes', $fechaInicio->month)
            ->where('anio', $fechaInicio->year)
            ->first();

        if ($meta) {
            $observer = new MetaMensualObserver();
            $observer->updated($meta);
        }
    }
}