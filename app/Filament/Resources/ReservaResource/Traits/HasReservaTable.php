<?php

namespace App\Filament\Resources\ReservaResource\Traits;

use Filament\Tables;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Departamento;
use App\Exports\ReservasPropietarioExport;
use Maatwebsite\Excel\Facades\Excel;

trait HasReservaTable
{
    protected static function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->label('ID'),
            Tables\Columns\TextColumn::make('departamento.nombreEdificio')
                ->label('Departamento'),
            Tables\Columns\TextColumn::make('huesped.nombre')
                ->label('Huesped'),
            Tables\Columns\TextColumn::make('canalReserva.nombre')
                ->label('Canal'),
            Tables\Columns\TextColumn::make('fechaInicio')
                ->label('Inicio'),
            Tables\Columns\TextColumn::make('fechaFin')
                ->label('Fin'),
            Tables\Columns\TextColumn::make('estado')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Pendiente' => 'gray',
                    'Confirmada' => 'success',
                    'Cancelada' => 'danger',
                }),
            Tables\Columns\TextColumn::make('costoPorNoche')->label('Costo Noche'),
            Tables\Columns\TextColumn::make('cantidadHuespedes')->label('Cant. Huéspedes'),
            Tables\Columns\TextColumn::make('cantidadNoches')->label('Cant. Noches'),
            Tables\Columns\TextColumn::make('comisionCanal')->label('Comisión Canal'),
            Tables\Columns\TextColumn::make('totalBruto')
                ->label('Total Bruto')
                ->getStateUsing(function($record) {
                    $costoPorNoche = floatval($record->costoPorNoche ?? 0);
                    $cantidadNoches = intval($record->cantidadNoches ?? 0);
                    return number_format($costoPorNoche * $cantidadNoches, 0, ',', '.');
                }),
            Tables\Columns\TextColumn::make('montoReserva')->label('Monto Reserva'),
            Tables\Columns\TextColumn::make('montoLimpieza')->label('Monto Limpieza'),
            Tables\Columns\TextColumn::make('montoGarantia')->label('Monto Garantía'),
            Tables\Columns\TextColumn::make('montoEmpresaAdministradora')->label('Monto Empresa'),
            Tables\Columns\TextColumn::make('montoPropietario')->label('Monto Propietario'),
        ];
    }

    protected static function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('estado')
                ->options([
                    'Confirmada' => 'Confirmada',
                    'Cancelada' => 'Cancelada',
                    'Pendiente' => 'Pendiente',
                ]),
            Tables\Filters\Filter::make('fecha_rango')
                ->form([
                    Forms\Components\DatePicker::make('fecha_inicio')->label('Desde'),
                    Forms\Components\DatePicker::make('fecha_fin')->label('Hasta'),
                ])
                ->query(function (Builder $query, array $data) {
                    if (!empty($data['fecha_inicio'])) {
                        $query->where('fechaInicio', '>=', $data['fecha_inicio']);
                    }
                    if (!empty($data['fecha_fin'])) {
                        $query->where('fechaFin', '<=', $data['fecha_fin']);
                    }
                }),
            Tables\Filters\SelectFilter::make('idDepartamento')
                ->label('Departamento')
                ->relationship('departamento', 'nombreEdificio'),
        ];
    }

    protected static function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('exportarReservas')
                ->label('Exportar Reservas')
                ->form([
                    Forms\Components\DatePicker::make('fecha_inicio')
                        ->label('Desde')
                        ->required(),
                    Forms\Components\DatePicker::make('fecha_fin')
                        ->label('Hasta')
                        ->required(),
                    Forms\Components\Select::make('departamento_id')
                        ->label('Departamento')
                        ->options(Departamento::all()->pluck('nombreEdificio', 'id')),
                    Forms\Components\Select::make('formato')
                        ->label('Formato')
                        ->options([
                            'xlsx' => 'Excel',
                            'csv' => 'CSV',
                        ])
                        ->default('xlsx')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $query = static::getExportQuery($data);
                    $reservas = $query->with([
                        'departamento.propietario',
                        'huesped',
                        'canalReserva',
                    ])->get();

                    if ($reservas->isEmpty()) {
                        Filament\Notifications\Notification::make()
                            ->warning()
                            ->title('No hay datos para exportar')
                            ->body('No se encontraron reservas en el rango de fechas seleccionado.')
                            ->send();
                        return;
                    }

                    $export = new ReservasPropietarioExport($reservas);
                    $filename = 'reservas_' . now()->format('Ymd_His') . '.' . $data['formato'];
                    
                    return Excel::download($export, $filename);
                })
                ->icon('heroicon-o-document-arrow-down')
                ->color('success'),
        ];
    }

    protected static function getExportQuery(array $data): Builder
    {
        $query = \App\Models\Reserva::query()
            ->whereBetween('fechaInicio', [$data['fecha_inicio'], $data['fecha_fin']])
            ->orWhereBetween('fechaFin', [$data['fecha_inicio'], $data['fecha_fin']]);

        if (!empty($data['departamento_id'])) {
            $query->where('idDepartamento', $data['departamento_id']);
        }

        return $query;
    }
}