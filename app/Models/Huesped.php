<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Huesped extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'nombre',
        'Whatsapp',
        'numeroDocumento',
        'enListaNegra',
    ];
}
