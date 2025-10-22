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

    // Backwards compatibility: some code used "costo" attribute. Map costo to monto.
    public function getCostoAttribute()
    {
        return $this->attributes['monto'] ?? null;
    }

    public function setCostoAttribute($value)
    {
        $this->attributes['monto'] = $value;
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}
