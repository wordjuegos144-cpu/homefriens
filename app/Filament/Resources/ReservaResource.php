<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages;
use App\Models\Reserva;
use App\Exports\ReservasPropietarioExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ReservaResource\Traits\HasReservaCalculations;
use App\Filament\Resources\ReservaResource\Traits\HasReservaFormFields;
use App\Filament\Resources\ReservaResource\Traits\HasReservaTable;
use App\Filament\Resources\ReservaResource\Traits\HasReservaMutators;
use App\Filament\Resources\ReservaResource\Traits\HasReservaValidations;
use Filament\Notifications\Notification;
use App\Models\Calificacion;
use App\Models\Resena;

class ReservaResource extends Resource
{
    use HasReservaCalculations;
    use HasReservaFormFields;
    use HasReservaTable;
    use HasReservaMutators;
    use HasReservaValidations;

    protected static ?string $model = Reserva::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function form(Form $form): Form
    {
        return $form->schema([
            static::getDepartamentoField(),
            static::getHuespedField(),
            ...static::getFechasFields(),
            static::getEstadoField(),
            ...static::getMontosFields(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::getTableColumns())
            ->filters(static::getTableFilters())
            ->headerActions([
                    Tables\Actions\Action::make('export')
                        ->label('Exportar Reporte')
                        ->icon('heroicon-o-document-arrow-down')
                        ->form([
                            Forms\Components\DatePicker::make('fechaInicio')
                                ->label('Fecha Inicio')
                                ->required(),
                            Forms\Components\DatePicker::make('fechaFin')
                                ->label('Fecha Fin')
                                ->required(),
                            Forms\Components\Select::make('idDepartamento')
                                ->label('Departamento')
                                ->relationship('departamento', 'nombreEdificio')
                                ->searchable(),
                            Forms\Components\Select::make('formato')
                                ->label('Formato de Exportación')
                                ->options([
                                    'csv' => 'CSV',
                                    'xlsx' => 'Excel',
                                ])
                                ->default('xlsx')
                                ->required(),
                        ])
                        ->action(function (array $data) {
                            $query = Reserva::query()
                                ->whereBetween('fechaInicio', [$data['fechaInicio'], $data['fechaFin']])
                                ->orWhereBetween('fechaFin', [$data['fechaInicio'], $data['fechaFin']]);
                            
                            if (!empty($data['idDepartamento'])) {
                                $query->where('idDepartamento', $data['idDepartamento']);
                            }

                            $reservas = $query->with([
                                'departamento.propietario',
                                'huesped',
                                'canalReserva',
                            ])->get();

                            if ($reservas->isEmpty()) {
                                Notification::make()
                                    ->warning()
                                    ->title('No hay datos para exportar')
                                    ->body('No se encontraron reservas en el rango de fechas seleccionado.')
                                    ->send();
                                return;
                            }

                            return Excel::download(
                                new ReservasPropietarioExport($reservas),
                                'reporte-reservas.' . $data['formato']
                            );
                        })
                ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservas::route('/'),
            'create' => Pages\CreateReserva::route('/create'),
            'edit' => Pages\EditReserva::route('/{record}/edit'),
        ];
    }
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        static::validateRequiredFields($data);
        $data = static::normalizeData($data);
        $data = static::calculateDerivedFields($data);
        // Evaluar y disparar alerta por reseñas bajas del huésped (si corresponde)
        static::evaluarAlertaResenaBaja($data);

        return $data;
    }
    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data = static::mutateFormDataBeforeCreate($data);
        // También evaluamos en actualizaciones
        static::evaluarAlertaResenaBaja($data);
        return $data;
    }
    /**
     * Evalúa el historial de calificaciones de un huésped y dispara una
     * notificación de advertencia si el promedio es menor a 4.
     *
     * @param array $data Form data que contiene al menos `idHuesped`.
     * @return void
     */
    protected static function evaluarAlertaResenaBaja(array $data): void
    {
        $idHuesped = $data['idHuesped'] ?? null;
        if (!$idHuesped) {
            return;
        }

        // Intentar promedio desde Calificacion (reseñas de huéspedes). Si no hay, usar Resena (reseñas de propietarios).
        $avgCal = Calificacion::averageForHuesped($idHuesped);
        $avgRes = Resena::where('idHuesped', $idHuesped)->avg('valor');
        $avg = $avgCal ?? ($avgRes !== null ? round((float) $avgRes, 2) : null);

        if ($avg === null) {
            return; // Sin calificaciones o reseñas previas
        }

        if ($avg < 4) {
            $recent = Resena::where('idHuesped', $idHuesped)
                ->orderByDesc('fecha')
                ->limit(5)
                ->get();

            $lines = [];
            $lines[] = 'Calificación promedio: ' . number_format($avg, 2);
            $lines[] = '';
            $lines[] = 'Últimas reseñas:';

            if ($recent->isEmpty()) {
                $lines[] = '- No hay reseñas disponibles.';
            } else {
                foreach ($recent as $r) {
                    $fecha = optional($r->fecha)->format('Y-m-d');
                    $valor = $r->valor ?? '-';
                    $texto = $r->argumento ? substr($r->argumento, 0, 200) : '-';
                    $lines[] = "- [$fecha] (valor $valor): $texto";
                }
            }

            $body = implode("\n", $lines);

            Notification::make('huesped-baja-calificacion-on-save')
                ->title('Advertencia: huésped con calificación baja')
                ->warning()
                ->body($body)
                ->persistent()
                ->send();
        }
    }
    protected static function validarDisponibilidadFechas($idDepartamento, $inicio, $fin): void
    {
        $existeReserva = Reserva::where('idDepartamento', $idDepartamento)
            ->where(function($query) use ($inicio, $fin) {
                $query->where(function($q) use ($inicio, $fin) {
                    $q->where('fechaInicio', '<=', $fin)
                      ->where('fechaFin', '>=', $inicio);
                });
            })
            ->where('estado', 'Confirmada')
            ->exists();

        if ($existeReserva) {
            Notification::make('reserva-ocupada')
                ->title('¡Atención! Ya existe una reserva confirmada en ese departamento para las fechas seleccionadas.')
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
