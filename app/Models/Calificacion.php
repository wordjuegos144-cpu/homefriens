<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Calificacion extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'idReserva',
        'valor',
        'comentario',
        'fecha',
    ];

    protected $casts = [
        'valor' => 'integer',
        'fecha' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($calificacion) {
            // Si no se proporciona una fecha, asignar la fecha/hora actual
            if (empty($calificacion->fecha)) {
                $calificacion->fecha = now();
            }
        });
    }

    public function reserva() {
        return $this->belongsTo(Reserva::class, 'idReserva');
    }

    /**
     * Calcula el promedio de calificaciones para un huésped dado.
     * Busca las calificaciones asociadas a las reservas del huésped.
     * Retorna null si no hay calificaciones.
     *
     * @param int $idHuesped
     * @return float|null
     */
    public static function averageForHuesped(int $idHuesped): ?float
    {
        $query = self::query()
            ->whereHas('reserva', function ($q) use ($idHuesped) {
                $q->where('idHuesped', $idHuesped);
            });

        $count = $query->count();
        if ($count === 0) {
            return null;
        }

        return round((float) $query->avg('valor'), 2);
    }
}
