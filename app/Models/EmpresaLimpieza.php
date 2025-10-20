<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class EmpresaLimpieza extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'email',
        'activo',
    ];
}
