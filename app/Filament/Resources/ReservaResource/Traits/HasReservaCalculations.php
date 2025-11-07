<?php

namespace App\Filament\Resources\ReservaResource\Traits;

use App\Services\ReservaService;
use Filament\Notifications\Notification;

trait HasReservaCalculations
{
    /**
     * Calcula y setea el total bruto en el formulario
     */
    protected static function calcularTotalBruto(callable $get, callable $set): void
    {
        try {
            $costo = floatval($get('costoPorNoche') ?? 0);
            $noches = intval($get('cantidadNoches') ?? 0);
            $total = round($costo * $noches, 2);
            $set('totalBruto', $total);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error calculando Total Bruto')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Calcula y setea la comisión del canal
     */
    protected static function calcularComision(callable $get, callable $set): void
    {
        try {
            $canal = $get('idCanalReserva');
            $costo = floatval($get('costoPorNoche') ?? 0);
            $noches = intval($get('cantidadNoches') ?? 0);
            $comision = ReservaService::calcularComisionCanal($canal, $costo, $noches);
            $set('comisionCanal', $comision);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error calculando comisión')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Calcula y setea el montoReserva
     */
    protected static function calcularMontoReserva(callable $get, callable $set): void
    {
        try {
            $costo = floatval($get('costoPorNoche') ?? 0);
            $noches = intval($get('cantidadNoches') ?? 0);
            $comision = floatval($get('comisionCanal') ?? 0);
            $montoGarantia = floatval($get('montoGarantia') ?? 0);
            $descuento = floatval($get('descuentoAplicado') ?? 0);
            $montoReserva = ReservaService::calcularMontoReserva($costo, $noches, $comision, $montoGarantia, $descuento);
            $set('montoReserva', $montoReserva);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error calculando monto de reserva')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Calcula la distribución de montos: montoBase, montoEmpresaAdministradora, montoPropietario
     */
    protected static function calcularMontosDistribucion(callable $get, callable $set): void
    {
        try {
            $totalBruto = floatval($get('totalBruto') ?? 0);
            $comisionCanal = floatval($get('comisionCanal') ?? 0);
            $montoGarantia = floatval($get('montoGarantia') ?? 0);

            // montoBase = totalBruto - comisionCanal
            // NOTA: No restamos la garantía aquí porque la garantía se devuelve al huésped
            $montoBase = round($totalBruto - $comisionCanal, 2);
            if ($montoBase < 0) $montoBase = 0;
            $set('montoBase', $montoBase);

            $montoEmpresa = ReservaService::calcularMontoEmpresaAdministradora($get('idDepartamento'), $get('fechaInicio'), $montoBase);
            $set('montoEmpresaAdministradora', $montoEmpresa);

            $montoPropietario = round($montoBase - $montoEmpresa, 2);
            if ($montoPropietario < 0) $montoPropietario = 0;
            $set('montoPropietario', $montoPropietario);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error calculando distribución de montos')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
