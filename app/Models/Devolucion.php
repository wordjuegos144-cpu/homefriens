<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Devolucion extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'idReserva',
        'monto',
        'fechaDevolucion',
        'fechaProcesada',
        'estadoPago',
        'comprobante',
    ];

    public function reserva() {
        return $this->belongsTo(Reserva::class, 'idReserva');
    }
}
