<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class EvidenciaLimpieza extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'limpieza_id',
        'tipo',
        'archivo',
    ];

    public function limpieza()
    {
        return $this->belongsTo(Limpieza::class);
    }
}
