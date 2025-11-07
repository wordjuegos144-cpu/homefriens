<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaMensual extends Model
{
    protected $table = 'metas_mensuales';

    protected $fillable = [
        'idDepartamento',
        'mes',
        'anio',
        'valor_meta',
        'valor_actual',
        'porcentaje_alcanzado',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'mes' => 'integer',
        'anio' => 'integer',
        'valor_meta' => 'decimal:2',
        'valor_actual' => 'decimal:2',
        'porcentaje_alcanzado' => 'decimal:2',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento');
    }

    /**
     * Calcula y actualiza el porcentaje alcanzado de la meta
     */
    public function actualizarPorcentaje(): void
    {
        if ($this->valor_meta > 0) {
            $this->porcentaje_alcanzado = ($this->valor_actual / $this->valor_meta) * 100;
            
            // Actualizar el estado basado en el porcentaje
            $this->estado = match(true) {
                $this->porcentaje_alcanzado >= 100 => 'Alcanzada',
                $this->porcentaje_alcanzado >= 75 => 'En Progreso',
                $this->porcentaje_alcanzado >= 50 => 'En Camino',
                default => 'Pendiente'
            };
            
            $this->save();
        }
    }

    /**
     * Actualiza el valor actual y recalcula el porcentaje
     */
    public function actualizarValorActual(float $valor): void
    {
        $this->valor_actual = $valor;
        $this->actualizarPorcentaje();
    }
}