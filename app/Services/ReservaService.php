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
     * Calcula el monto de la reserva (total - comisión canal).
     */
    public static function calcularMontoReserva($costoPorNoche, $cantidadNoches, $comision): float
    {
        if (!$costoPorNoche || !$cantidadNoches) {
            return 0;
        }
    $total = (float) $costoPorNoche * (float) $cantidadNoches;
        return $total - ($comision ?? 0);
    }

    /**
     * Calcula el monto para la empresa administradora: montoReserva menos comisión del contrato vigente.
     * Busca el contrato vigente para el departamento y la fecha de la reserva.
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
            return $montoReserva;
        }
    $comision = ((float) $montoReserva * (float) $contrato->comisionContrato) / 100;
    return round($comision, 2);
    }

    /**
     * Calcula el monto para el propietario: montoReserva menos comisión del contrato vigente.
     * Busca el contrato vigente para el departamento y la fecha de la reserva.
     */
    public static function calcularMontoPropietario($idDepartamento, $fechaReserva, $montoReserva)
    {
        if (!$idDepartamento || !$fechaReserva || !$montoReserva) {
            return 0;
        }
        $contrato = \App\Models\Contrato::where('idDepartamento', $idDepartamento)
            ->where('fechaInicioContrato', '<=', $fechaReserva)
            ->where('fechaFinContrato', '>=', $fechaReserva)
            ->first();
    $comision = ($contrato && $contrato->comisionContrato) ? ((float) $montoReserva * (float) $contrato->comisionContrato) / 100 : 0;
    $monto = (float) $montoReserva - $comision;
        return round($monto, 2);
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
