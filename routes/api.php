<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApartmentController;
use App\Http\Controllers\Api\OwnerAuthController;

// Ping de diagnóstico
Route::get('ping', function () {
    return response()->json(['status' => 'ok', 'time' => now()->toDateTimeString()]);
});

// Rutas de autenticación para propietarios
Route::post('owners/register', [OwnerAuthController::class, 'register']);
Route::post('owners/login',    [OwnerAuthController::class, 'login']);
Route::post('owners/logout',   [OwnerAuthController::class, 'logout']);

// Rutas de departamentos (asegúrate que estas existan también)
Route::get('departamentos',      [ApartmentController::class, 'index']);
Route::get('departamentos/{id}', [ApartmentController::class, 'show']);

// Alias en inglés (opcional)
Route::get('apartments',      [ApartmentController::class, 'index']);
Route::get('apartments/{id}', [ApartmentController::class, 'show']);

// Ganancias por departamento
Route::get('ganancias/departamento/{id}', [\App\Http\Controllers\Api\GananciasController::class, 'departamento']);