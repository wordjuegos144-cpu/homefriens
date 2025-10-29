<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva;

class CalendarController extends Controller
{
    // Render the public calendar page
    public function index()
    {
        return view('calendar');
    }

    // Return reservation events as JSON for FullCalendar
    public function events(Request $request)
    {
        $reservas = Reserva::with('departamento')->get();

        $events = $reservas->map(function ($r) {
            return [
                'id' => $r->id,
                'title' => ($r->departamento?->nombreEdificio ?? 'Departamento') . ' (Res. ' . $r->id . ')',
                'start' => $r->fechaInicio?->format('Y-m-d'),
                'end' => $r->fechaFin?->addDay()?->format('Y-m-d'),
                'color' => $r->estado === 'Confirmada' ? '#10b981' : '#f59e0b',
            ];
        })->toArray();

        return response()->json($events);
    }
}
