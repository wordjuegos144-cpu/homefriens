<?php

namespace App\Exports;

use App\Models\MetaMensual;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MetasMensualesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return MetaMensual::with('departamento')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Departamento',
            'Mes',
            'Año',
            'Meta ($)',
            'Valor Actual ($)',
            'Porcentaje Alcanzado',
            'Estado',
            'Observaciones',
            'Última Actualización'
        ];
    }

    public function map($meta): array
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
            4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $updated = $meta->updated_at;
        if (!is_null($updated) && !$updated instanceof \Carbon\Carbon) {
            $updated = \Carbon\Carbon::parse($updated);
        }

        return [
            $meta->id,
            optional($meta->departamento)->nombreEdificio ?? '',
            $meses[$meta->mes] ?? '',
            $meta->anio,
            number_format($meta->valor_meta, 2),
            number_format($meta->valor_actual, 2),
            number_format($meta->porcentaje_alcanzado, 2) . '%',
            $meta->estado,
            $meta->observaciones,
            $updated ? $updated->format('Y-m-d H:i:s') : '',
        ];
    }
}