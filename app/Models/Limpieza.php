<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Limpieza extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'reserva_id',
        'fecha_programada',
        'hora_programada',
        'monto',
        'estado',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}
