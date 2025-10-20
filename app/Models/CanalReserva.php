<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CanalReserva extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'nombre',
        'comision', // Ahora es fillable
    ];
}
