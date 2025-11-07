<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class ReservasPropietarioExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $reservas;

    public function __construct(Collection $reservas)
    {
        $this->reservas = $reservas;
    }

    public function collection()
    {
        return $this->reservas;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Departamento',
            'Propietario',
            'Huésped',
            'Fecha Inicio',
            'Fecha Fin',
            'Noches',
            'Canal',
            'Estado',
            'Costo por Noche',
            'Total Bruto',
            'Comisión Canal',
            'Monto Limpieza',
            'Monto Garantía',
            'Monto Final',
            'Monto Propietario',
        ];
    }

    public function map($reserva): array
    {
        $costoNoche = floatval($reserva->costoPorNoche);
        $noches = intval($reserva->cantidadNoches);
        $totalBruto = $costoNoche * $noches;

        $fechaInicio = $reserva->fechaInicio;
        $fechaFin = $reserva->fechaFin;

        if (!is_null($fechaInicio) && !$fechaInicio instanceof \Carbon\Carbon) {
            $fechaInicio = \Carbon\Carbon::parse($fechaInicio);
        }
        
        if (!is_null($fechaFin) && !$fechaFin instanceof \Carbon\Carbon) {
            $fechaFin = \Carbon\Carbon::parse($fechaFin);
        }

        $dep = optional($reserva->departamento);
        $prop = optional($dep->propietario);

        return [
            $reserva->id,
            $dep->nombreEdificio ?? '',
            $prop->nombre ?? '',
            $reserva->huesped->nombre ?? '',
            $fechaInicio ? $fechaInicio->format('Y-m-d') : '',
            $fechaFin ? $fechaFin->format('Y-m-d') : '',
            $noches,
            $reserva->canalReserva->nombre ?? '',
            $reserva->estado ?? '',
            $costoNoche,
            $totalBruto,
            floatval($reserva->comisionCanal),
            floatval($reserva->montoLimpieza),
            floatval($reserva->montoGarantia),
            floatval($reserva->montoReserva),
            floatval($reserva->montoPropietario),
        ];
    }
}
