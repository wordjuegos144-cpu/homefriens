<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Departamento extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'idEmpresaAdministradora',
        'nombreEdificio',
        'direccion',
        'piso',
        'numero',
        'capacidadNormal',
        'capacidadExtra',
        'nombrePropietario',
        'telefonoPropietario',
    ];

    public function empresaAdministradora()
    {
        return $this->belongsTo(EmpresaAdministradora::class, 'idEmpresaAdministradora');
    }
}
