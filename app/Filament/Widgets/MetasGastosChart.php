<?php

namespace App\Filament\Widgets;

use App\Models\MetaMensual;
use App\Models\Gasto;
use App\Models\Limpieza;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MetasGastosChart extends ChartWidget
{
    protected static ?string $heading = 'Progreso de Metas y Gastos';
    protected static ?string $maxHeight = '400px';
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Obtener todas las metas del mes actual
        $metas = MetaMensual::with('departamento')
            ->where('mes', $currentMonth)
            ->where('anio', $currentYear)
            ->get();

    $labels = [];
    $metasData = [];
    $actualesData = [];
    $gastosData = [];
    $gastosLimpiezaData = [];

        foreach ($metas as $meta) {
            $labels[] = optional($meta->departamento)->nombreEdificio ?? 'Sin nombre';
            $metasData[] = $meta->valor_meta;
            $actualesData[] = $meta->valor_actual;

            // Calcular solo los gastos cubiertos del departamento para el mes actual
            $gastos = Gasto::where('idDepartamento', $meta->idDepartamento)
                ->where('estado', 'Cubierto')
                ->whereYear('fecha', $currentYear)
                ->whereMonth('fecha', $currentMonth)
                ->sum('monto');

            // Calcular gastos de limpieza asociados a reservas del departamento en el mes actual
            $gastosLimpieza = Limpieza::whereHas('reserva', function ($q) use ($meta) {
                    $q->where('idDepartamento', $meta->idDepartamento);
                })
                ->whereYear('fecha_programada', $currentYear)
                ->whereMonth('fecha_programada', $currentMonth)
                ->sum('monto');

            $gastosData[] = $gastos;
            $gastosLimpiezaData[] = $gastosLimpieza;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Meta Mensual',
                    'data' => $metasData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)', // Azul claro
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Valor Actual',
                    'data' => $actualesData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)', // Verde claro
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Gastos',
                    'data' => $gastosData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)', // Rojo claro
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Gastos Limpieza',
                    'data' => $gastosLimpiezaData,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.2)', // Morado claro
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { 
                            return '$' + new Intl.NumberFormat().format(value);
                        }",
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.dataset.label + ': $' + 
                                   new Intl.NumberFormat().format(context.raw);
                        }",
                    ],
                ],
            ],
        ];
    }
}