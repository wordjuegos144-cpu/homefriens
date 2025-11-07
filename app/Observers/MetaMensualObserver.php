<?php

namespace App\Observers;

use App\Models\MetaMensual;
use App\Models\Reserva;
use Carbon\Carbon;

class MetaMensualObserver
{
    /**
     * Handle the MetaMensual "created" event.
     */
    public function created(MetaMensual $metaMensual): void
    {
        $this->actualizarValoresDesdeReservas($metaMensual);
    }

    /**
     * Handle the MetaMensual "updated" event.
     */
    public function updated(MetaMensual $metaMensual): void
    {
        $this->actualizarValoresDesdeReservas($metaMensual);
    }

    /**
     * Actualiza los valores de la meta basándose en las reservas existentes
     */
    protected function actualizarValoresDesdeReservas(MetaMensual $metaMensual): void
    {
        $valorActual = Reserva::query()
            ->where('idDepartamento', $metaMensual->idDepartamento)
            ->where('estado', 'Confirmada')
            ->whereYear('fechaInicio', $metaMensual->anio)
            ->whereMonth('fechaInicio', $metaMensual->mes)
            ->sum('montoReserva');

        if ($metaMensual->valor_meta > 0) {
            $porcentaje = ($valorActual / $metaMensual->valor_meta) * 100;
        } else {
            $porcentaje = 0;
        }

        // Actualizar directamente en la base de datos para evitar loops infinitos
        MetaMensual::withoutEvents(function () use ($metaMensual, $valorActual, $porcentaje) {
            $metaMensual->forceFill([
                'valor_actual' => $valorActual,
                'porcentaje_alcanzado' => $porcentaje,
                'estado' => match(true) {
                    $porcentaje >= 100 => 'Alcanzada',
                    $porcentaje >= 75 => 'En Progreso',
                    $porcentaje >= 50 => 'En Camino',
                    default => 'Pendiente'
                }
            ])->save();
        });
    }
}