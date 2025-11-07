<?php

namespace App\Filament\Widgets;

use App\Models\MetaMensual;
use App\Models\Gasto;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class MetasGastosOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Totales generales
        $totalMetas = MetaMensual::where('mes', $currentMonth)
            ->where('anio', $currentYear)
            ->sum('valor_meta');

        $totalActual = MetaMensual::where('mes', $currentMonth)
            ->where('anio', $currentYear)
            ->sum('valor_actual');

        $totalGastos = Gasto::whereYear('fecha', $currentYear)
            ->whereMonth('fecha', $currentMonth)
            ->where('estado', 'Cubierto')
            ->sum('monto');

        // Obtener el total de gastos pendientes para mostrar en la descripción
        $totalGastosPendientes = Gasto::whereYear('fecha', $currentYear)
            ->whereMonth('fecha', $currentMonth)
            ->where('estado', 'Pendiente')
            ->sum('monto');

        // Calcular porcentaje general y margen neto
        $porcentajeGeneral = $totalMetas > 0 ? ($totalActual / $totalMetas) * 100 : 0;
        $margenNeto = $totalActual - $totalGastos;

        $mesesEspanol = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return [
            Stat::make('Meta Total ' . $mesesEspanol[$currentMonth], '$' . number_format($totalMetas, 2))
                ->description('Meta mensual combinada')
                ->chart([0, 0, $totalMetas])
                ->color('info'),

            Stat::make('Ingresos Actuales', '$' . number_format($totalActual, 2))
                ->description(number_format($porcentajeGeneral, 1) . '% de la meta alcanzada')
                ->chart([0, $totalActual, $totalMetas])
                ->color($porcentajeGeneral >= 75 ? 'success' : ($porcentajeGeneral >= 50 ? 'warning' : 'danger')),

            Stat::make('Gastos Cubiertos', '$' . number_format($totalGastos, 2))
                ->description(
                    'Margen Neto: $' . number_format($margenNeto, 2) . "\n" .
                    'Gastos Pendientes: $' . number_format($totalGastosPendientes, 2)
                )
                ->chart([0, $totalGastos, $totalActual])
                ->color($margenNeto > 0 ? 'success' : 'danger'),
        ];
    }
}