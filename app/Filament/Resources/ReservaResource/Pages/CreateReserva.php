<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use App\Filament\Resources\ReservaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReserva extends CreateRecord
{
    protected static string $resource = ReservaResource::class;

    protected function afterCreate(): void
    {
        $reserva = $this->record;
        \App\Models\Limpieza::create([
            'reserva_id' => $reserva->id,
            'fecha_programada' => $reserva->fechaFin,
            'hora_programada' => '14:00:00',
            'monto' => $reserva->montoLimpieza,
            'estado' => 'Pendiente',
        ]);

        // Si se registra un valor en garantía, crear devolución vinculada
        if ($reserva->montoGarantia > 0) {
            \App\Models\Devolucion::create([
                'idReserva' => $reserva->id,
                'monto' => $reserva->montoGarantia,
                'fechaDevolucion' => $reserva->fechaFin, // Puedes ajustar la lógica de fecha si es necesario
                'estadoPago' => 'Pendiente',
                'comprobante' => null,
            ]);
        }
    }
}
