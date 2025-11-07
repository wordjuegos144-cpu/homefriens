<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use App\Filament\Resources\ReservaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditReserva extends EditRecord
{
    protected static string $resource = ReservaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validar todo antes de guardar
        $validations = [
            'dates' => ReservaResource::validateReservaDates($data),
            'overbooking' => ReservaResource::validateOverbooking($data),
            'huesped' => ReservaResource::validateHuesped($data),
            'capacidad' => ReservaResource::validateCapacidad($data),
        ];

        // Si alguna validación falla, loguear detalles e interrumpir el guardado
        if (in_array(false, $validations, true)) {
            try {
                Log::warning('Reserva update validation failed', [
                    'validations' => $validations,
                    'data' => $data,
                ]);
            } catch (\Throwable $e) {
            }

            $this->halt();
        }

        return $data;
    }
}
