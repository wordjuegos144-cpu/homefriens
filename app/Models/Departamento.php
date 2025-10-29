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
        'idPropietario',
        'nombreEdificio',
        'direccion',
        'descripcion',
        'piso',
        'numero',
        'capacidadNormal',
        'capacidadExtra',
        'telefonoPropietario',
        'camas',
        'cuartos',
        'banos',
    'imagenes',
    'servicios',
    ];

    public function getGoogleMapsUrlAttribute()
    {
        return $this->direccion
            ? "https://www.google.com/maps/search/?api=1&query=" . urlencode($this->direccion)
            : null;
    }

    protected $casts = [
    'imagenes' => 'array',
    'servicios' => 'array',
    ];

    public function empresaAdministradora()
    {
        return $this->belongsTo(EmpresaAdministradora::class, 'idEmpresaAdministradora');
    }
    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'idPropietario');
    }
}
