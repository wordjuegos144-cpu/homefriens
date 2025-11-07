<?php

namespace App\Filament\Resources\ReservaResource\Traits;

use App\Services\ReservaService;
use App\Models\CanalReserva;
use Illuminate\Support\Facades\Log;

trait HasReservaMutators
{
    protected static function normalizeNumber($value)
    {
        if (is_null($value) || $value === '') return 0;
        if (is_string($value)) {
            // Eliminar espacios y separadores de miles comunes (puntos y espacios)
            $v = str_replace(['.', ' '], '', $value);
            // Reemplazar coma decimal por punto
            $v = str_replace(',', '.', $v);
            return is_numeric($v) ? $v : 0;
        }
        return $value;
    }

    protected static function validateRequiredFields(array $data): void
    {
        $required = [
            'idDepartamento', 'idHuesped', 'idCanalReserva', 'fechaInicio', 'fechaFin',
            'costoPorNoche', 'montoLimpieza', 'cantidadNoches', 'estado'
        ];

        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                throw new \Exception("El campo '$field' es obligatorio y no fue proporcionado.");
            }
        }
    }

    protected static function normalizeData(array $data): array
    {
        // Campos enteros
        $integerFields = ['idDepartamento', 'idHuesped', 'idCanalReserva', 'cantidadNoches', 'cantidadHuespedes'];
        foreach ($integerFields as $field) {
            $data[$field] = (int) static::normalizeNumber($data[$field] ?? 0);
        }

        // Campos decimales
        $floatFields = ['costoPorNoche', 'montoLimpieza', 'montoGarantia'];
        foreach ($floatFields as $field) {
            $data[$field] = (float) static::normalizeNumber($data[$field] ?? 0);
        }

        // Valores por defecto
        $data['montoGarantia'] = $data['montoGarantia'] ?? 0;
        $data['cantidadHuespedes'] = $data['cantidadHuespedes'] ?? 1;
        $data['estado'] = $data['estado'] ?? 'Confirmada';
        $data['fechaInicio'] = $data['fechaInicio'] ?? now();
        $data['fechaFin'] = $data['fechaFin'] ?? now();

        return $data;
    }

    protected static function calculateDerivedFields(array $data): array
    {
        // Cálculo de comisión
        $calculatedComision = (float) (
            ReservaService::calcularComisionCanal(
                $data['idCanalReserva'], 
                $data['costoPorNoche'], 
                $data['cantidadNoches']
            ) ?? 0
        );

        // Log si la comisión es 0 para depuración
        if ($calculatedComision == 0) {
            static::logComisionCero($data);
        }

        $data['comisionCanal'] = $calculatedComision;

        // Cálculo de montos
        $data['montoReserva'] = (float) (
            ReservaService::calcularMontoReserva(
                $data['costoPorNoche'], 
                $data['cantidadNoches'], 
                $data['comisionCanal']
            ) ?? 0
        );

        $data['montoEmpresaAdministradora'] = (float) (
            ReservaService::calcularMontoEmpresaAdministradora(
                $data['idDepartamento'], 
                $data['fechaInicio'], 
                $data['montoReserva']
            ) ?? 0
        );

        $data['montoPropietario'] = (float) (
            ReservaService::calcularMontoPropietario(
                $data['idDepartamento'], 
                $data['fechaInicio'], 
                $data['montoReserva']
            ) ?? 0
        );

        return $data;
    }

    protected static function logComisionCero(array $data): void
    {
        try {
            Log::info('ReservaResource::mutateFormDataBeforeCreate - comisionCanal=0', [
                'idCanalReserva' => $data['idCanalReserva'] ?? null,
                'costoPorNoche' => $data['costoPorNoche'] ?? null,
                'cantidadNoches' => $data['cantidadNoches'] ?? null,
                'canal' => isset($data['idCanalReserva']) ? 
                    CanalReserva::find($data['idCanalReserva'])?->toArray() : 
                    null,
            ]);
        } catch (\Throwable $e) {
            // No bloqueamos el guardado por error de logging
        }
    }
}