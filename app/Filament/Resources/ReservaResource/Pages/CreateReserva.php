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
        
        // Solo crear devolución si hay garantía (la limpieza se crea automáticamente en el modelo)
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
