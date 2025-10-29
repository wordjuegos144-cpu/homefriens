<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\View;
use App\Models\Departamento;
use App\Models\GanaciaDeDepartamento;

class GananciasDepartamento extends Widget
{
    protected static string $view = 'filament.widgets.ganancias-departamento';

    public $periodo = 'mensual';
    public $departamento_id = null;

    // Totales
    public $ingresos_reservas = 0;
    public $gastos_limpieza = 0;
    public $comisiones_admin = 0;
    public $ganancia_neta = 0;

    public function mount(string $periodo = 'mensual')
    {
        $this->periodo = $periodo;
        // preselect first departamento if present
        $first = Departamento::first();
        $this->departamento_id = $first ? $first->id : null;
        $this->recalculate();
    }

    public function updatedPeriodo()
    {
        $this->recalculate();
    }

    public function updatedDepartamentoId()
    {
        $this->recalculate();
    }

    protected function recalculate(): void
    {
        if (!$this->departamento_id) {
            $this->ingresos_reservas = 0;
            $this->gastos_limpieza = 0;
            $this->comisiones_admin = 0;
            $this->ganancia_neta = 0;
            return;
        }

        $result = GanaciaDeDepartamento::calcularGanancias($this->departamento_id, $this->periodo);

        $this->ingresos_reservas = $result['ingresos_reservas'] ?? 0;
        $this->gastos_limpieza = $result['gastos_limpieza'] ?? 0;
        $this->comisiones_admin = $result['comisiones_admin'] ?? 0;
        $this->ganancia_neta = $result['ganancia_neta'] ?? 0;
    }

    public function getDepartamentosProperty()
    {
        return Departamento::orderBy('nombreEdificio')->get();
    }
}
