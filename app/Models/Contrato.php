<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Contrato extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'fechaInicioContrato',
        'fechaFinContrato',
        'comisionContrato',
        'idDepartamento',
        'idEmpresaAdministradora',
    ];

    public function departamento()
    {
        return $this->belongsTo(\App\Models\Departamento::class, 'idDepartamento');
    }

    public function empresaAdministradora()
    {
        return $this->belongsTo(\App\Models\EmpresaAdministradora::class, 'idEmpresaAdministradora');
    }
}
