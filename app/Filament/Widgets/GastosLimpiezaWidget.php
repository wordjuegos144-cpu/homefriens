<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Limpieza;
use App\Models\Departamento;
use Carbon\Carbon;

class GastosLimpiezaWidget extends ChartWidget
{
    protected static ?string $heading = 'Gastos de Limpieza por Departamento';
    protected static ?string $pollingInterval = null;
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Obtener departamentos que tengan limpiezas este mes
        $limpiezas = Limpieza::with('reserva.departamento')
            ->whereYear('fecha_programada', $currentYear)
            ->whereMonth('fecha_programada', $currentMonth)
            ->get();

        $grouped = [];
        foreach ($limpiezas as $l) {
            // Protegemos la cadena de accesos: primero comprobamos reserva, luego departamento
            $departamento = optional(optional($l->reserva)->departamento)->nombreEdificio ?? 'Sin departamento';
            if (!isset($grouped[$departamento])) {
                $grouped[$departamento] = 0;
            }
            $grouped[$departamento] += (float) ($l->monto ?? 0);
        }

        $labels = array_keys($grouped);
        $data = array_values($grouped);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Gastos de Limpieza',
                    'data' => $data,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.6)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 1,
                ],
            ],
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
                        'callback' => "function(value) { return '$' + new Intl.NumberFormat().format(value); }",
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) { return context.dataset.label + ': $' + new Intl.NumberFormat().format(context.raw); }",
                    ],
                ],
            ],
        ];
    }
}
