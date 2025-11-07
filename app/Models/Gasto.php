<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Gasto extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'idDepartamento',
        'monto',
        'tipo',
        'fecha',
        'estado',
        'descripcion',
    ];

    protected $casts = [
        'monto' => 'float',
        'fecha' => 'date',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento');
    }
}
