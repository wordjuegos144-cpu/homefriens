<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propietario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    // Un propietario puede tener muchos departamentos
    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'idPropietario');
    }
}
