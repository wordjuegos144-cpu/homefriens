<?php

namespace App\Filament\Resources\ReservaResource\Traits;

use App\Models\Reserva;
use Carbon\Carbon;
use Filament\Notifications\Notification;

trait HasReservaValidations
{
    public static function validateReservaDates($data): bool
    {
        $inicio = Carbon::parse($data['fechaInicio'])->startOfDay();
        $fin = Carbon::parse($data['fechaFin'])->startOfDay();
        
    if ($inicio->greaterThanOrEqualTo($fin)) {
            Notification::make('fecha-invalida')
                ->title('Fechas inválidas')
                ->body('La fecha de inicio debe ser anterior a la fecha de fin.')
                ->danger()
                ->persistent()
                ->send();
            return false;
        }

        // Considerar como "pasada" únicamente si la fecha de inicio es anterior a hoy.
        // Antes se usaba ->isPast() sobre startOfDay(), lo que hacía que cualquier
        // reserva para hoy fuese considerada en el pasado (porque startOfDay() < now()).
        if ($inicio->lt(Carbon::today())) {
            Notification::make('fecha-pasada')
                ->title('Fecha inválida')
                ->body('La fecha de inicio no puede ser en el pasado.')
                ->danger()
                ->persistent()
                ->send();
            return false;
        }

        return true;
    }

    public static function validateOverbooking($data): bool
    {
        $idDepartamento = $data['idDepartamento'];
        $inicio = $data['fechaInicio'];
        $fin = $data['fechaFin'];
        $currentReservaId = $data['id'] ?? null;

        $query = Reserva::where('idDepartamento', $idDepartamento)
            ->where('estado', 'Confirmada')
            ->where(function($q) use ($inicio, $fin) {
                $q->where(function($subQ) use ($inicio, $fin) {
                    $subQ->where('fechaInicio', '<=', $fin)
                         ->where('fechaFin', '>=', $inicio);
                });
            });
        
        // Si estamos editando, excluir la reserva actual
        if ($currentReservaId) {
            $query->where('id', '!=', $currentReservaId);
        }

        $existingReserva = $query->first();

        if ($existingReserva) {
            $message = "Ya existe una reserva confirmada para estas fechas:\n";
            $message .= "Huésped: {$existingReserva->huesped->nombre}\n";
            $message .= "Del: " . Carbon::parse($existingReserva->fechaInicio)->format('d/m/Y') . "\n";
            $message .= "Al: " . Carbon::parse($existingReserva->fechaFin)->format('d/m/Y');

            Notification::make('overbooking-error')
                ->title('¡Error de Overbooking!')
                ->body($message)
                ->danger()
                ->persistent()
                ->send();
            return false;
        }

        return true;
    }

    public static function validateHuesped($data): bool
    {
        $idHuesped = $data['idHuesped'];
        
        $huesped = \App\Models\Huesped::find($idHuesped);
        if (!$huesped) {
            return true; // Si no encontramos el huésped, asumimos que está siendo creado
        }

        $warnings = [];

        // Verificar lista negra — por defecto mostramos una advertencia pero no bloqueamos
        // la creación para permitir casos en los que se quiere forzar la reserva.
        if ($huesped->enListaNegra) {
            Notification::make('huesped-lista-negra')
                ->title('¡Atención! El huésped está en lista negra')
                ->warning()
                ->persistent()
                ->send();
            // No bloqueamos la creación; devolvemos true para permitir continuar.
            return true;
        }

        // Verificar calificaciones bajas
        $avgCal = \App\Models\Calificacion::averageForHuesped($idHuesped);
        $avgRes = \App\Models\Resena::where('idHuesped', $idHuesped)->avg('valor');
        $avg = $avgCal ?? ($avgRes !== null ? round((float) $avgRes, 2) : null);

        if ($avg !== null && $avg < 3) {
            Notification::make('huesped-calificacion-muy-baja')
                ->title('¡Advertencia! Huésped con calificación muy baja')
                ->body("El huésped tiene una calificación promedio de " . number_format($avg, 2))
                ->danger()
                ->persistent()
                ->send();
            return false;
        }

        if ($avg !== null && $avg < 4) {
            $warnings[] = "El huésped tiene una calificación promedio de " . number_format($avg, 2);
        }

        // Si hay advertencias pero no son bloqueantes, mostrarlas
        if (count($warnings) > 0) {
            Notification::make('huesped-advertencias')
                ->title('Advertencias sobre el huésped')
                ->body(implode("\n", $warnings))
                ->warning()
                ->persistent()
                ->send();
        }

        return true;
    }

    public static function validateCapacidad($data): bool
    {
        $idDepartamento = $data['idDepartamento'];
        $cantidadHuespedes = $data['cantidadHuespedes'];
        
        $departamento = \App\Models\Departamento::find($idDepartamento);
        if (!$departamento || !$departamento->capacidadMaxima) {
            return true; // Si no hay información de capacidad, permitimos la reserva
        }

        if ($cantidadHuespedes > $departamento->capacidadMaxima) {
            Notification::make('capacidad-excedida')
                ->title('Capacidad excedida')
                ->body("El departamento tiene una capacidad máxima de {$departamento->capacidadMaxima} huéspedes")
                ->danger()
                ->persistent()
                ->send();
            return false;
        }

        return true;
    }
}