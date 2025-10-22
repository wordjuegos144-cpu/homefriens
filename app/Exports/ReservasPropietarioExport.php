<?php

namespace App\Exports;

use App\Models\Reserva;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReservasPropietarioExport implements FromView, ShouldAutoSize
{
    protected $reservas;

    public function __construct($reservas)
    {
        $this->reservas = $reservas;
    }

    public function view(): View
    {
        return view('exports.reservas_propietario', [
            'reservas' => $this->reservas,
        ]);
    }
}
