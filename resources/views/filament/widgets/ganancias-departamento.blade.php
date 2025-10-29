<div class="filament-widget p-4">
    <div class="flex gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Departamento</label>
            <select wire:model="departamento_id" class="mt-1 block w-64 rounded-md border-gray-300">
                <option value="">-- Seleccione --</option>
                @foreach($this->departamentos as $d)
                    <option value="{{ $d->id }}">{{ $d->nombreEdificio }} - {{ $d->numero ?? '' }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Periodo</label>
            <select wire:model="periodo" class="mt-1 block w-40 rounded-md border-gray-300">
                <option value="mensual">Mensual</option>
                <option value="trimestral">Trimestral</option>
                <option value="anual">Anual</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-4">
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Ingresos Reservas</div>
            <div class="text-2xl font-bold">{{ number_format($this->ingresos_reservas, 2, ',', '.') }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Gastos Limpieza</div>
            <div class="text-2xl font-bold">{{ number_format($this->gastos_limpieza, 2, ',', '.') }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Comisiones Admin</div>
            <div class="text-2xl font-bold">{{ number_format($this->comisiones_admin, 2, ',', '.') }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Ganancia Neta</div>
            <div class="text-2xl font-bold">{{ number_format($this->ganancia_neta, 2, ',', '.') }}</div>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <canvas id="gananciasChart" width="600" height="250"></canvas>
    </div>

    <div class="bg-white p-4 rounded shadow mt-4">
        <h3 class="font-semibold mb-2">Detalle</h3>
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-sm text-gray-600">
                    <th>Concepto</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2">Ingresos Reservas</td>
                    <td class="py-2">{{ number_format($this->ingresos_reservas, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="py-2">Gastos Limpieza</td>
                    <td class="py-2">{{ number_format($this->gastos_limpieza, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="py-2">Comisiones Admin</td>
                    <td class="py-2">{{ number_format($this->comisiones_admin, 2, ',', '.') }}</td>
                </tr>
                <tr class="font-bold">
                    <td class="py-2">Ganancia Neta</td>
                    <td class="py-2">{{ number_format($this->ganancia_neta, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Chart.js via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            function renderChart() {
                const ctx = document.getElementById('gananciasChart').getContext('2d');
                if (window._gananciasChart) {
                    window._gananciasChart.data.datasets[0].data = [{{ $this->ingresos_reservas }}, {{ $this->gastos_limpieza }}, {{ $this->comisiones_admin }}, {{ $this->ganancia_neta }}];
                    window._gananciasChart.update();
                    return;
                }
                window._gananciasChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Ingresos', 'Gastos', 'Comisiones', 'Neta'],
                        datasets: [{
                            label: 'Valores',
                            backgroundColor: ['#10B981', '#EF4444', '#F59E0B', '#3B82F6'],
                            data: [{{ $this->ingresos_reservas }}, {{ $this->gastos_limpieza }}, {{ $this->comisiones_admin }}, {{ $this->ganancia_neta }}]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            renderChart();

            // Re-render when Livewire updates
            Livewire.hook('message.processed', (message, component) => {
                renderChart();
            });
        });
    </script>
</div>
