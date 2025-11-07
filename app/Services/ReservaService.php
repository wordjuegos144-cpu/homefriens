<?php

namespace App\Services;

use Carbon\Carbon;

class ReservaService
{
    /**
     * Calcula la cantidad de noches entre dos fechas (solo fechas, sin horas, nunca negativo).
     */
    public static function calcularCantidadNoches($fechaInicio, $fechaFin): int
    {
        if (!$fechaInicio || !$fechaFin) {
            return 0;
        }
        $inicio = Carbon::parse($fechaInicio)->startOfDay();
        $fin = Carbon::parse($fechaFin)->startOfDay();
        $dias = $inicio->diffInDays($fin);
        return $dias > 0 ? $dias : 0;
    }

    /**
     * Calcula la comisión del canal según el canal, costo por noche y cantidad de noches.
     */
    public static function calcularComisionCanal($canalId, $costoPorNoche, $cantidadNoches): float
    {
        if (!$canalId || !$costoPorNoche || !$cantidadNoches) {
            return 0;
        }
        $canal = \App\Models\CanalReserva::find($canalId);
        if (!$canal || !$canal->comision) {
            return 0;
        }
    $total = (float) $costoPorNoche * (float) $cantidadNoches;
    $comision = ($total * (float) $canal->comision) / 100;
        return round($comision, 2);
    }

    /**
     * Calcula el monto de la reserva según la fórmula:
     * montoReserva = (cantidadNoches × costoPorNoche) + montoGarantia - descuentoAplicado - comisionCanal
     */
    public static function calcularMontoReserva($costoPorNoche, $cantidadNoches, $comision, $montoGarantia = 0, $descuentoAplicado = 0): float
    {
        if (!$costoPorNoche || !$cantidadNoches) {
            return 0;
        }
        $total = (float) $costoPorNoche * (float) $cantidadNoches;
        $total += (float)($montoGarantia ?? 0); // Añadir garantía al total
        return round($total - (float)($descuentoAplicado ?? 0) - (float)($comision ?? 0), 2);
    }

    /**
     * Calcula el monto para la empresa administradora según la fórmula:
     * montoEmpresaAdministradora = montoReserva × porcentaje de comisión
     */
    public static function calcularMontoEmpresaAdministradora($idDepartamento, $fechaReserva, $montoReserva)
    {
        if (!$idDepartamento || !$fechaReserva || !$montoReserva) {
            return 0;
        }
        $contrato = \App\Models\Contrato::where('idDepartamento', $idDepartamento)
            ->where('fechaInicioContrato', '<=', $fechaReserva)
            ->where('fechaFinContrato', '>=', $fechaReserva)
            ->first();
        if (!$contrato || !$contrato->comisionContrato) {
            return 0; // Si no hay contrato o comisión, la empresa no recibe comisión
        }
        $montoComision = ((float) $montoReserva * (float) $contrato->comisionContrato) / 100;
        return round($montoComision, 2);
    }

    /**
     * Calcula el monto para el propietario según la fórmula:
     * montoPropietario = montoBase - montoEmpresaAdministradora
     * donde montoBase = total bruto - comisionCanal
     * (la garantía NO se resta aquí porque se devuelve al huésped)
     */
    public static function calcularMontoPropietario($idDepartamento, $fechaReserva, $montoBase)
    {
        if (!$idDepartamento || !$fechaReserva || $montoBase === null) {
            return 0;
        }

        // Calculamos la comisión de la empresa administradora sobre el monto base
        $montoEmpresaAdministradora = static::calcularMontoEmpresaAdministradora($idDepartamento, $fechaReserva, $montoBase);
        
        // El monto del propietario es el monto base menos la comisión de la empresa
        $montoPropietario = round($montoBase - $montoEmpresaAdministradora, 2);
        return $montoPropietario < 0 ? 0 : $montoPropietario;
    }

    /**
     * Calcula el total a pagar: (costoPorNoche * cantidadNoches) + montoLimpieza + montoGarantia
     */
    public static function calcularTotalAPagar($costoPorNoche, $cantidadNoches, $montoLimpieza, $montoGarantia)
    {
    $total = (float) ($costoPorNoche ?? 0) * (float) ($cantidadNoches ?? 0);
    $total += (float) ($montoLimpieza ?? 0) + (float) ($montoGarantia ?? 0);
        return round($total, 2);
    }

    // Aquí puedes agregar más métodos de lógica de negocio reutilizable

    /**
     * Retorna el promedio de calificaciones del huésped, o null si no tiene calificaciones.
     *
     * @param int $idHuesped
     * @return float|null
     */
    public static function obtenerPromedioCalificacionesHuesped(int $idHuesped): ?float
    {
        return \App\Models\Calificacion::averageForHuesped($idHuesped);
    }
}
