<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use App\Filament\Resources\ReservaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateReserva extends CreateRecord
{
    protected static string $resource = ReservaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validar todo antes de crear
        $validations = [
            'dates' => ReservaResource::validateReservaDates($data),
            'overbooking' => ReservaResource::validateOverbooking($data),
            'huesped' => ReservaResource::validateHuesped($data),
            'capacidad' => ReservaResource::validateCapacidad($data),
        ];

        // Si alguna validación falla, loguear detalles e interrumpir la creación
        if (in_array(false, $validations, true)) {
            try {
                Log::warning('Reserva creation validation failed', [
                    'validations' => $validations,
                    'data' => $data,
                ]);
            } catch (\Throwable $e) {
                // no bloquear si el logger falla
            }

            $this->halt();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $reserva = $this->record;
        
        // Solo crear devolución si hay garantía (la limpieza se crea automáticamente en el modelo)
        if ($reserva->montoGarantia > 0) {
            \App\Models\Devolucion::create([
                'idReserva' => $reserva->id,
                'monto' => $reserva->montoGarantia,
                'fechaDevolucion' => $reserva->fechaFin,
                'estadoPago' => 'Pendiente',
                'comprobante' => null,
            ]);
        }
    }
}
