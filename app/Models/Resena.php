<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Resena extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'idHuesped',
        'idPropietario',
        'valor',
        'argumento',
        'fecha',
    ];

    protected $casts = [
        'valor' => 'integer',
        'fecha' => 'datetime',
    ];

    public function huesped()
    {
        return $this->belongsTo(Huesped::class, 'idHuesped');
    }

    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'idPropietario');
    }
}
