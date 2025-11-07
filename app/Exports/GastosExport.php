<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class GastosExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $gastos;

    public function __construct(Collection $gastos)
    {
        $this->gastos = $gastos;
    }

    public function collection()
    {
        return $this->gastos;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Departamento',
            'Fecha',
            'Tipo',
            'Descripción',
            'Monto',
            'Estado',
            'Comprobante',
            'Observaciones',
            'Creado',
            'Actualizado',
        ];
    }

    public function map($gasto): array
    {
        $fecha = $gasto->fecha;
        if (!is_null($fecha) && !$fecha instanceof \Carbon\Carbon) {
            $fecha = \Carbon\Carbon::parse($fecha);
        }

        $created = $gasto->created_at;
        if (!is_null($created) && !$created instanceof \Carbon\Carbon) {
            $created = \Carbon\Carbon::parse($created);
        }

        $updated = $gasto->updated_at;
        if (!is_null($updated) && !$updated instanceof \Carbon\Carbon) {
            $updated = \Carbon\Carbon::parse($updated);
        }

        return [
            $gasto->id,
            optional($gasto->departamento)->nombreEdificio ?? '',
            $fecha ? $fecha->format('Y-m-d') : '',
            $gasto->tipo ?? '',
            $gasto->descripcion ?? '',
            floatval($gasto->monto),
            $gasto->estado ?? '',
            $gasto->comprobante ?? '',
            $gasto->observaciones ?? '',
            $created ? $created->format('Y-m-d H:i:s') : '',
            $updated ? $updated->format('Y-m-d H:i:s') : '',
        ];
    }
}