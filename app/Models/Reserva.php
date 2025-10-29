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
        // Ensure computed monetary fields are populated for any create path
        static::creating(function ($reserva) {
            // Normalize basic numeric fields to avoid string arithmetic issues
            $reserva->costoPorNoche = isset($reserva->costoPorNoche) ? (float) $reserva->costoPorNoche : 0;
            $reserva->cantidadNoches = isset($reserva->cantidadNoches) ? (int) $reserva->cantidadNoches : 0;
            $reserva->montoLimpieza = isset($reserva->montoLimpieza) ? (float) $reserva->montoLimpieza : 0;
            $reserva->montoGarantia = isset($reserva->montoGarantia) ? (float) $reserva->montoGarantia : 0;

            // Compute commission and derived amounts when missing or zero
            try {
                if (empty($reserva->comisionCanal) || $reserva->comisionCanal == 0) {
                    $reserva->comisionCanal = \App\Services\ReservaService::calcularComisionCanal(
                        $reserva->idCanalReserva,
                        $reserva->costoPorNoche,
                        $reserva->cantidadNoches
                    );
                }

                if (empty($reserva->montoReserva) || $reserva->montoReserva == 0) {
                    $reserva->montoReserva = \App\Services\ReservaService::calcularMontoReserva(
                        $reserva->costoPorNoche,
                        $reserva->cantidadNoches,
                        $reserva->comisionCanal
                    );
                }

                // Note: the DB schema does not include a `totalAPagar` column. We avoid setting
                // that attribute here to prevent SQLite "no column named totalAPagar" errors.

                if (empty($reserva->montoEmpresaAdministradora) || $reserva->montoEmpresaAdministradora == 0) {
                    $reserva->montoEmpresaAdministradora = \App\Services\ReservaService::calcularMontoEmpresaAdministradora(
                        $reserva->idDepartamento,
                        $reserva->fechaInicio,
                        $reserva->montoReserva
                    );
                }

                if (empty($reserva->montoPropietario) || $reserva->montoPropietario == 0) {
                    $reserva->montoPropietario = \App\Services\ReservaService::calcularMontoPropietario(
                        $reserva->idDepartamento,
                        $reserva->fechaInicio,
                        $reserva->montoReserva
                    );
                }
            } catch (\Throwable $e) {
                // Don't prevent creation due to calculation errors; log for later inspection
                try {
                    \Illuminate\Support\Facades\Log::error('Reserva creating hook failed to calculate amounts', [
                        'reserva_id' => $reserva->id ?? null,
                        'error' => $e->getMessage(),
                    ]);
                } catch (\Throwable $_) {
                    // ignore logging failures
                }
            }
        });
        static::created(function ($reserva) {
            if ($reserva->estado === 'Confirmada') {
                \App\Models\Limpieza::create([
                    'reserva_id' => $reserva->id,
                    'fecha_programada' => $reserva->fechaFin,
                    'hora_programada' => '14:00:00',
                    'monto' => $reserva->montoLimpieza,
                    'estado' => 'Pendiente',
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
                // When reservation becomes Confirmed, ensure the cleaning is Programada
                if ($reserva->estado === 'Confirmada') {
                    if ($limpieza) {
                        // If a limpieza exists and is Pending, move it to Programada
                        if ($limpieza->estado === 'Pendiente') {
                            $limpieza->estado = 'Programada';
                            $limpieza->save();
                        }
                        // If it was Cancelada, also open it back to Programada
                        if ($limpieza->estado === 'Cancelada') {
                            $limpieza->estado = 'Programada';
                            $limpieza->save();
                        }
                    } else {
                        // No limpieza exists yet: create one already Programada
                        \App\Models\Limpieza::create([
                            'reserva_id' => $reserva->id,
                            'fecha_programada' => $reserva->fechaFin,
                            'hora_programada' => '14:00:00',
                            'monto' => $reserva->montoLimpieza,
                            'estado' => 'Programada',
                        ]);
                    }
                } else {
                    // If reservation no longer confirmed, and a cleaning is Programada, cancel it
                    if ($limpieza && $limpieza->estado === 'Programada' && $reserva->estado !== 'Confirmada') {
                        $limpieza->estado = 'Cancelada';
                        $limpieza->save();
                    }
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
    
    // Relación con la limpieza asociada a la reserva (si existe)
    public function limpieza()
    {
        return $this->hasOne(Limpieza::class, 'reserva_id');
    }

    // Pagos asociados a la reserva
    public function pagos()
    {
        return $this->hasMany(\App\Models\Pago::class, 'idReserva');
    }
}

