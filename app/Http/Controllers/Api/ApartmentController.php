<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamento;

class ApartmentController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::with(['empresaAdministradora', 'propietario'])->get();
        
        return response()->json($departamentos->map(function ($departamento) {
            return [
                'id' => $departamento->id,
                'titulo' => $departamento->nombreEdificio . ' - Depto ' . $departamento->numero,
                'descripcion' => $departamento->descripcion ?? '',
                'ubicacion' => $departamento->direccion,
                'capacidad' => $departamento->capacidadNormal + $departamento->capacidadExtra,
                'habitaciones' => $departamento->cuartos ?? 0,
                'banos' => $departamento->banos ?? 0,
                'imagen_url' => $departamento->imagenes ? json_decode($departamento->imagenes)[0] : null,
                'servicios' => $departamento->servicios ? json_decode($departamento->servicios) : [],
                'precio_por_noche' => 100 // Agrega un precio por defecto o ajusta según tu modelo
            ];
        }));
    }

    public function show($id)
    {
        $departamento = Departamento::with(['empresaAdministradora', 'propietario'])->find($id);
        
        if (!$departamento) {
            return response()->json(['message' => 'Departamento no encontrado'], 404);
        }

        return response()->json([
            'id' => $departamento->id,
            'titulo' => $departamento->nombreEdificio . ' - Depto ' . $departamento->numero,
            'descripcion' => $departamento->descripcion ?? '',
            'ubicacion' => $departamento->direccion,
            'capacidad' => $departamento->capacidadNormal + $departamento->capacidadExtra,
            'habitaciones' => $departamento->cuartos ?? 0,
            'banos' => $departamento->banos ?? 0,
            'imagen_url' => $departamento->imagenes ? json_decode($departamento->imagenes)[0] : null,
            'servicios' => $departamento->servicios ? json_decode($departamento->servicios) : [],
            'precio_por_noche' => 100, // Agrega un precio por defecto o ajusta según tu modelo
            'propietario' => $departamento->propietario ? [
                'nombre' => $departamento->propietario->nombre,
                'telefono' => $departamento->propietario->telefono
            ] : null,
            'empresa_administradora' => $departamento->empresaAdministradora ? [
                'nombre' => $departamento->empresaAdministradora->nombre,
                'telefono' => $departamento->empresaAdministradora->telefono
            ] : null
        ]);
}
}