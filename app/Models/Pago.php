<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Pago extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'idReserva',
        'idLimpieza',
        'tipoPago',
        'monto',
        'fechaPago',
        'formaPago',
        'estadoPago',
        'comprobante',
    ];

    public function reserva() {
        return $this->belongsTo(Reserva::class, 'idReserva');
    }
    public function limpieza() {
        return $this->belongsTo(Limpieza::class, 'idLimpieza');
    }
}
