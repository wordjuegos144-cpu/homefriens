<table>
    <thead>
        <tr>
            <th>ID Reserva</th>
            <th>Departamento</th>
            <th>Propietario</th>
            <th>Huésped</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Canal Reserva</th>
            <th>Estado</th>
            <th>Costo por Noche</th>
            <th>Cantidad Huéspedes</th>
            <th>Cantidad Noches</th>
            <th>Comisión Canal</th>
            <th>Total Bruto</th>
            <th>Monto Reserva</th>
            <th>Monto Limpieza</th>
            <th>Monto Garantía</th>
            <th>Monto Empresa</th>
            <th>Forma Pago</th>
            <th>Monto Propietario</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalBruto = 0;
            $totalMontoReserva = 0;
            $totalMontoLimpieza = 0;
            $totalMontoGarantia = 0;
            $totalMontoEmpresa = 0;
            $totalMontoPropietario = 0;
        @endphp
        @foreach($reservas as $reserva)
            @php
                $costoPorNoche = floatval($reserva->costoPorNoche ?? 0);
                $cantidadNoches = intval($reserva->cantidadNoches ?? 0);
                $bruto = $costoPorNoche * $cantidadNoches / 1000;
                $totalBruto += $bruto;
                $totalMontoReserva += floatval($reserva->montoReserva ?? 0) / 1000;
                $totalMontoLimpieza += floatval($reserva->montoLimpieza ?? 0) / 1000;
                $totalMontoGarantia += floatval($reserva->montoGarantia ?? 0) / 1000;
                $totalMontoEmpresa += floatval($reserva->montoEmpresaAdministradora ?? 0) / 1000;
                $totalMontoPropietario += floatval($reserva->montoPropietario ?? 0) / 1000;
            @endphp
            <tr>
                <td>{{ $reserva->id }}</td>
                <td>{{ $reserva->departamento->nombreEdificio ?? '' }} - {{ $reserva->departamento->numero ?? '' }}</td>
                <td>{{ $reserva->departamento->propietario->nombre ?? '' }}</td>
                <td>{{ $reserva->huesped->nombre ?? '' }}</td>
                <td>{{ $reserva->fechaInicio }}</td>
                <td>{{ $reserva->fechaFin }}</td>
                <td>{{ $reserva->canalReserva->nombre ?? '' }}</td>
                <td>{{ $reserva->estado }}</td>
                <td>{{ number_format($costoPorNoche, 0, ',', '.') }}</td>
                <td>{{ $reserva->cantidadHuespedes }}</td>
                <td>{{ $cantidadNoches }}</td>
                <td>{{ number_format(floatval($reserva->comisionCanal ?? 0), 3, ',', '.') }}</td>
                <td>{{ number_format($bruto, 3, ',', '.') }}</td>
                <td>{{ number_format(floatval($reserva->montoReserva ?? 0), 3, ',', '.') }}</td>
                <td>{{ number_format(floatval($reserva->montoLimpieza ?? 0), 3, ',', '.') }}</td>
                <td>{{ number_format(floatval($reserva->montoGarantia ?? 0), 3, ',', '.') }}</td>
                <td>{{ number_format(floatval($reserva->montoEmpresaAdministradora ?? 0), 3, ',', '.') }}</td>
                <td>{{ $reserva->pagos && $reserva->pagos->count() ? $reserva->pagos->first()->formaPago : '' }}</td>
                <td>{{ number_format(floatval($reserva->montoPropietario ?? 0), 3, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="12"><strong>TOTALES</strong></td>
            <td><strong>{{ number_format($totalBruto, 3, ',', '.') }}</strong></td>
            <td><strong>{{ number_format($totalMontoReserva, 3, ',', '.') }}</strong></td>
            <td><strong>{{ number_format($totalMontoLimpieza, 3, ',', '.') }}</strong></td>
            <td><strong>{{ number_format($totalMontoGarantia, 3, ',', '.') }}</strong></td>
            <td><strong>{{ number_format($totalMontoEmpresa, 3, ',', '.') }}</strong></td>
            <td><strong>{{ number_format($totalMontoPropietario, 3, ',', '.') }}</strong></td>
        </tr>
    </tbody>
</table>
