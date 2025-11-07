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
                $bruto = $costoPorNoche * $cantidadNoches;
                $totalBruto += $bruto;
                $totalMontoReserva += floatval($reserva->montoReserva ?? 0);
                $totalMontoLimpieza += floatval($reserva->montoLimpieza ?? 0);
                $totalMontoGarantia += floatval($reserva->montoGarantia ?? 0);
                $totalMontoEmpresa += floatval($reserva->montoEmpresaAdministradora ?? 0);
                $totalMontoPropietario += floatval($reserva->montoPropietario ?? 0);
            @endphp
            <tr>
                <td>{{ $reserva->id }}</td>
                @php $dep = optional($reserva->departamento); $prop = optional($dep->propietario); @endphp
                <td>{{ $dep->nombreEdificio ?? '' }} - {{ $dep->numero ?? '' }}</td>
                <td>{{ $prop->nombre ?? '' }}</td>
                <td>{{ $reserva->huesped->nombre ?? '' }}</td>
                <td>{{ $reserva->fechaInicio }}</td>
                <td>{{ $reserva->fechaFin }}</td>
                <td>{{ $reserva->canalReserva->nombre ?? '' }}</td>
                <td>{{ $reserva->estado }}</td>
                <td>{{ $costoPorNoche }}</td>
                <td>{{ $reserva->cantidadHuespedes }}</td>
                <td>{{ $cantidadNoches }}</td>
                <td>{{ floatval($reserva->comisionCanal ?? 0) }}</td>
                <td>{{ $bruto }}</td>
                <td>{{ floatval($reserva->montoReserva ?? 0) }}</td>
                <td>{{ floatval($reserva->montoLimpieza ?? 0) }}</td>
                <td>{{ floatval($reserva->montoGarantia ?? 0) }}</td>
                <td>{{ floatval($reserva->montoEmpresaAdministradora ?? 0) }}</td>
                <td>{{ $reserva->pagos && $reserva->pagos->count() ? $reserva->pagos->first()->formaPago : '' }}</td>
                <td>{{ floatval($reserva->montoPropietario ?? 0) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="12"><strong>TOTALES</strong></td>
            <td><strong>{{ $totalBruto }}</strong></td>
            <td><strong>{{ $totalMontoReserva }}</strong></td>
            <td><strong>{{ $totalMontoLimpieza }}</strong></td>
            <td><strong>{{ $totalMontoGarantia }}</strong></td>
            <td><strong>{{ $totalMontoEmpresa }}</strong></td>
            <td><strong>{{ $totalMontoPropietario }}</strong></td>
        </tr>
    </tbody>
</table>
