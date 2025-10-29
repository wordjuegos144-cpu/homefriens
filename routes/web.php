<?php

use Illuminate\Support\Facades\Route;

// Redirige la raíz a /admin (Filament)
Route::redirect('/', '/admin');

// Public calendar frontend
use App\Http\Controllers\Frontend\CalendarController;

Route::get('/calendar', [CalendarController::class, 'index'])->name('frontend.calendar');
Route::get('/calendar/events', [CalendarController::class, 'events'])->name('frontend.calendar.events');

// Public API: lista mínima de departamentos para consumo por el frontend
Route::get('/api/departamentos', function () {
	// Devuelve id y nombreEdificio (campo usado en las vistas)
	return \App\Models\Departamento::select('id', 'nombreEdificio')->get();
});

// Map picker route removed

// Si tienes autenticación, deja esto al final
