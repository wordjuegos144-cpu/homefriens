<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GanaciaDeDepartamento extends Model
{
    protected $table = 'ganacia_departamentos';

    /**
     * Calcular ganancias para un departamento en un periodo.
     * $period puede ser 'mensual', 'trimestral', 'anual' o 'custom'.
     * Si $fechaInicio y $fechaFin se proveen, se usan como rango.
     * Retorna un arreglo con ingresos_reservas, gastos_limpieza, comisiones_admin, ganancia_neta y detalles.
     */
    public static function calcularGanancias(int $idDepartamento, string $period = 'mensual', $fechaInicio = null, $fechaFin = null): array
    {
        // Determinar rango de fechas
        if ($period !== 'custom') {
            $now = Carbon::now();
            switch ($period) {
                case 'trimestral':
                    $start = $now->copy()->firstOfQuarter()->startOfDay();
                    $end = $now->copy()->lastOfQuarter()->endOfDay();
                    break;
                case 'anual':
                    $start = $now->copy()->firstOfYear()->startOfDay();
                    $end = $now->copy()->lastOfYear()->endOfDay();
                    break;
                case 'mensual':
                default:
                    $start = $now->copy()->firstOfMonth()->startOfDay();
                    $end = $now->copy()->lastOfMonth()->endOfDay();
                    break;
            }
        } else {
            $start = $fechaInicio ? Carbon::parse($fechaInicio)->startOfDay() : Carbon::now()->startOfMonth();
            $end = $fechaFin ? Carbon::parse($fechaFin)->endOfDay() : Carbon::now()->endOfMonth();
        }

        // Ingresos por reservas: sumar montoReserva para reservas Confirmadas que caen dentro del rango
        $ingresosReservas = (float) DB::table('reservas')
            ->where('idDepartamento', $idDepartamento)
            ->where('estado', 'Confirmada')
            ->whereDate('fechaInicio', '>=', $start->toDateString())
            ->whereDate('fechaFin', '<=', $end->toDateString())
            ->sum('montoReserva');

        // Gastos de limpieza: sumar monto de limpiezas asociadas a reservas en el rango
        $gastosLimpieza = (float) DB::table('limpiezas')
            ->join('reservas', 'limpiezas.reserva_id', '=', 'reservas.id')
            ->where('reservas.idDepartamento', $idDepartamento)
            ->whereDate('limpiezas.fecha_programada', '>=', $start->toDateString())
            ->whereDate('limpiezas.fecha_programada', '<=', $end->toDateString())
            ->sum('limpiezas.monto');

        // Comisiones de la empresa: sumar montoEmpresaAdministradora de reservas
        $comisionesAdmin = (float) DB::table('reservas')
            ->where('idDepartamento', $idDepartamento)
            ->where('estado', 'Confirmada')
            ->whereDate('fechaInicio', '>=', $start->toDateString())
            ->whereDate('fechaFin', '<=', $end->toDateString())
            ->sum('montoEmpresaAdministradora');

        $gananciaNeta = round($ingresosReservas - $gastosLimpieza - $comisionesAdmin, 2);

        return [
            'departamento_id' => $idDepartamento,
            'periodo' => $period,
            'fecha_inicio' => $start->toDateTimeString(),
            'fecha_fin' => $end->toDateTimeString(),
            'ingresos_reservas' => round($ingresosReservas, 2),
            'gastos_limpieza' => round($gastosLimpieza, 2),
            'comisiones_admin' => round($comisionesAdmin, 2),
            'ganancia_neta' => $gananciaNeta,
        ];
    }
}
