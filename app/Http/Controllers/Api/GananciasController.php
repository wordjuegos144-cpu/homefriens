<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GanaciaDeDepartamento;

class GananciasController extends Controller
{
    public function departamento(Request $request, $id)
    {
        $period = $request->query('period', 'mensual');
        $from = $request->query('from');
        $to = $request->query('to');

        $result = GanaciaDeDepartamento::calcularGanancias($id, $period, $from, $to);
        return response()->json($result);
    }
}
