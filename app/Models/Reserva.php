<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Reserva extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'idDepartamento',
        'idHuesped',
        'idCanalReserva',
        'fechaInicio',
        'fechaFin',
        'estado',
        'costoPorNoche',
        'cantidadHuespedes',
        'cantidadNoches',
        'descuentoAplicado',
        'comisionCanal',
        'montoReserva',
        'montoLimpieza',
        'montoGarantia',
        'montoEmpresaAdministradora',
        'montoPropietario',
    ];

    protected static function booted()
    {
        static::created(function ($reserva) {
            if ($reserva->estado === 'Confirmada') {
                \App\Models\Limpieza::create([
                    'reserva_id' => $reserva->id,
                    'fecha_programada' => $reserva->fechaFin,
                    'hora_programada' => null,
                    'monto' => $reserva->montoLimpieza,
                    'estado' => 'Programada',
                ]);

                // Registro de Pago: Garantía
                $canal = $reserva->canalReserva;
                $formaPago = ($canal && strtolower($canal->nombre) === 'airbnb') ? 'Airtm' : 'QR';
                $fechaPago = now();
                $comprobante = ($formaPago === 'QR' && $reserva->estado === 'Pendiente') ? '' : null; // Si es pendiente y QR, comprobante vacío
                \App\Models\Pago::create([
                    'idReserva' => $reserva->id,
                    'tipoPago' => 'Reserva',
                    'monto' => $reserva->montoGarantia,
                    'estadoPago' => 'Pendiente',
                    'formaPago' => $formaPago,
                    'fechaPago' => $fechaPago,
                    'comprobante' => '', // Valor por defecto
                ]);

                // Registro de Pago: Deposito (solo si canal != Airbnb y canal != Booking)
                if ($canal && !in_array(strtolower($canal->nombre), ['airbnb', 'booking'])) {
                    \App\Models\Pago::create([
                        'idReserva' => $reserva->id,
                        'tipoPago' => 'Deposito',
                        'monto' => $reserva->montoGarantia,
                        'estadoPago' => 'Pendiente',
                        'formaPago' => 'QR',
                        'fechaPago' => $fechaPago,
                        'comprobante' => '', // Valor por defecto
                    ]);
                }
            }
        });
        static::updated(function ($reserva) {
            $limpieza = \App\Models\Limpieza::where('reserva_id', $reserva->id)->first();
            if ($reserva->isDirty('estado')) {
                if ($reserva->estado === 'Confirmada') {
                    if (!$limpieza) {
                        \App\Models\Limpieza::create([
                            'reserva_id' => $reserva->id,
                            'fecha_programada' => $reserva->fechaFin,
                            'hora_programada' => null,
                            'monto' => $reserva->montoLimpieza,
                            'estado' => 'Programada',
                        ]);
                    } elseif ($limpieza->estado === 'Cancelada') {
                        $limpieza->estado = 'Programada';
                        $limpieza->save();
                    }
                } elseif ($limpieza && $limpieza->estado === 'Programada' && $reserva->estado !== 'Confirmada') {
                    $limpieza->estado = 'Cancelada';
                    $limpieza->save();
                }
            }
        });
    }

    public function departamento() {
        return $this->belongsTo(Departamento::class, 'idDepartamento');
    }
    public function huesped() {
        return $this->belongsTo(Huesped::class, 'idHuesped');
    }
    public function canalReserva() {
        return $this->belongsTo(CanalReserva::class, 'idCanalReserva');
    }
}
