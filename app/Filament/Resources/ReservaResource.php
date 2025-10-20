<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages;
use App\Filament\Resources\ReservaResource\RelationManagers;
use App\Models\Reserva;
use App\Services\ReservaService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        // Usar ReservaService para cálculos centralizados
        $calcularComision = function (callable $get, callable $set) {
            $canalId = $get('idCanalReserva');
            $costoPorNoche = $get('costoPorNoche');
            $cantidadNoches = $get('cantidadNoches');
            $comision = \App\Services\ReservaService::calcularComisionCanal($canalId, $costoPorNoche, $cantidadNoches);
            $set('comisionCanal', $comision);
        };
        $calcularMontoReserva = function (callable $get, callable $set) {
            $costoPorNoche = $get('costoPorNoche');
            $cantidadNoches = $get('cantidadNoches');
            $comision = $get('comisionCanal');
            $monto = \App\Services\ReservaService::calcularMontoReserva($costoPorNoche, $cantidadNoches, $comision);
            $set('montoReserva', $monto);
        };
        $calcularMontoEmpresaAdministradora = function (callable $get, callable $set) {
            $idDepartamento = $get('idDepartamento');
            $fechaReserva = $get('fechaInicio');
            $montoReserva = $get('montoReserva');
            $monto = \App\Services\ReservaService::calcularMontoEmpresaAdministradora($idDepartamento, $fechaReserva, $montoReserva);
            $set('montoEmpresaAdministradora', $monto);
        };
        $calcularMontoPropietario = function (callable $get, callable $set) {
            $idDepartamento = $get('idDepartamento');
            $fechaReserva = $get('fechaInicio');
            $montoReserva = $get('montoReserva');
            
            $monto = \App\Services\ReservaService::calcularMontoPropietario($idDepartamento, $fechaReserva, $montoReserva);
            $set('montoPropietario', $monto);
        };
        $calcularTotalAPagar = function (callable $get, callable $set) {
            $costoPorNoche = $get('costoPorNoche');
            $cantidadNoches = $get('cantidadNoches');
            $montoLimpieza = $get('montoLimpieza');
            $montoGarantia = $get('montoGarantia');
            $total = \App\Services\ReservaService::calcularTotalAPagar($costoPorNoche, $cantidadNoches, $montoLimpieza, $montoGarantia);
            $set('totalAPagar', $total);
        };
        // Recalcular monto empresa administradora en todos los campos relevantes
        $recalcularEmpresaAdmin = function (callable $get, callable $set) use ($calcularMontoEmpresaAdministradora) {
            $calcularMontoEmpresaAdministradora($get, $set);
        };
        return $form
            ->schema([
                Forms\Components\Select::make('idDepartamento')
                    ->label('Departamento')
                    ->relationship('departamento', 'nombreEdificio')
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return \App\Models\Departamento::query()
                            ->where('nombreEdificio', 'like', "%$search%")
                            ->orWhere('piso', 'like', "%$search%")
                            ->orWhere('numero', 'like', "%$search%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function ($departamento) {
                                return [
                                    $departamento->id => $departamento->nombreEdificio . ' - Piso:' . ($departamento->piso ?? '-') . ' - Nro:' . ($departamento->numero ?? '-')
                                ];
                            });
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $departamento = \App\Models\Departamento::find($value);
                        if (!$departamento) return null;
                        return $departamento->nombreEdificio . ' - Piso:' . ($departamento->piso ?? '-') . ' - Nro:' . ($departamento->numero ?? '-');
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($recalcularEmpresaAdmin, $calcularMontoPropietario) {
                        $recalcularEmpresaAdmin($get, $set);
                        $calcularMontoPropietario($get, $set);
                    }),
                Forms\Components\Select::make('idHuesped')
                    ->label('Huesped')
                    ->relationship('huesped', 'nombre')
                    ->required()
                    ->reactive()
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return \App\Models\Huesped::query()
                            ->where('nombre', 'like', "%$search%")
                            ->orWhere('Whatsapp', 'like', "%$search%")
                            ->orWhere('numeroDocumento', 'like', "%$search%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function ($huesped) {
                                return [
                                    $huesped->id => $huesped->nombre . ' - Whatsapp:' . $huesped->Whatsapp . ' - # Doc:' . ($huesped->numeroDocumento ?? '-')
                                ];
                            });
                    })
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($state) {
                            $huesped = \App\Models\Huesped::find($state);
                            if ($huesped && ($huesped->enListaNegra === true || $huesped->enListaNegra == 1)) {
                                \Filament\Notifications\Notification::make('huesped-lista-negra')
                                    ->title('¡Atención! El huésped seleccionado está en lista negra.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
                        }
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required(),
                        Forms\Components\TextInput::make('Whatsapp')
                            ->label('Whatsapp')
                            ->required(),
                        Forms\Components\TextInput::make('numeroDocumento')
                            ->label('Número de Documento'),
                        Forms\Components\Toggle::make('enListaNegra')
                            ->label('En lista negra')
                            ->default(false),
                    ]),
                Forms\Components\Select::make('idCanalReserva')
                    ->label('Canal Reserva')
                    ->relationship('canalReserva', 'nombre')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($calcularComision, $calcularMontoReserva) {
                        $calcularComision($get, $set);
                        $calcularMontoReserva($get, $set);
                    }),
                Forms\Components\DatePicker::make('fechaInicio')
                    ->label('Fecha Inicio')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($calcularComision, $calcularMontoReserva) {
                        $fin = $get('fechaFin');
                        $idDepartamento = $get('idDepartamento');
                        $dias = \App\Services\ReservaService::calcularCantidadNoches($state, $fin);
                        $set('cantidadNochesLabel', $dias);
                        $set('cantidadNoches', $dias);
                        $calcularComision($get, $set);
                        $calcularMontoReserva($get, $set);
                        if ($state && $fin) {
                            $inicioDate = \Carbon\Carbon::parse($state)->startOfDay();
                            $finDate = \Carbon\Carbon::parse($fin)->startOfDay();
                            if ($inicioDate->greaterThan($finDate)) {
                                \Filament\Notifications\Notification::make()
                                    ->title('La fecha inicio no puede ser mayor a la fecha fin.')
                                    ->danger()
                                    ->send();
                            }
                            // Validación de reserva existente en el departamento y fechas
                            if ($idDepartamento) {
                                $existeReserva = \App\Models\Reserva::where('idDepartamento', $idDepartamento)
                                    ->where(function($query) use ($state, $fin) {
                                        $query->where(function($q) use ($state, $fin) {
                                            $q->where('fechaInicio', '<=', $fin)
                                              ->where('fechaFin', '>=', $state);
                                        });
                                    })
                                    ->where('estado', 'Confirmada')
                                    ->exists();
                                if ($existeReserva) {
                                    \Filament\Notifications\Notification::make('reserva-ocupada')
                                        ->title('¡Atención! Ya existe una reserva confirmada en ese departamento para las fechas seleccionadas.')
                                        ->danger()
                                        ->persistent()
                                        ->send();
                                }
                            }
                        } else {
                            $set('cantidadNochesLabel', 0);
                        }
                    }),
                Forms\Components\DatePicker::make('fechaFin')
                    ->label('Fecha Fin')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($calcularComision, $calcularMontoReserva) {
                        $inicio = $get('fechaInicio');
                        $idDepartamento = $get('idDepartamento');
                        $dias = \App\Services\ReservaService::calcularCantidadNoches($inicio, $state);
                        $set('cantidadNochesLabel', $dias);
                        $set('cantidadNoches', $dias);
                        $calcularComision($get, $set);
                        $calcularMontoReserva($get, $set);
                        if ($inicio && $state) {
                            $inicioDate = \Carbon\Carbon::parse($inicio)->startOfDay();
                            $finDate = \Carbon\Carbon::parse($state)->startOfDay();
                            if ($finDate->lessThan($inicioDate)) {
                                \Filament\Notifications\Notification::make()
                                    ->title('La fecha fin no puede ser menor a la fecha inicio.')
                                    ->danger()
                                    ->send();
                            }
                            // Validación de reserva existente en el departamento y fechas
                            if ($idDepartamento) {
                                $existeReserva = \App\Models\Reserva::where('idDepartamento', $idDepartamento)
                                    ->where(function($query) use ($inicio, $state) {
                                        $query->where(function($q) use ($inicio, $state) {
                                            $q->where('fechaInicio', '<=', $state)
                                              ->where('fechaFin', '>=', $inicio);
                                        });
                                    })
                                    ->where('estado', 'Confirmada')
                                    ->exists();
                                if ($existeReserva) {
                                    \Filament\Notifications\Notification::make('reserva-ocupada')
                                        ->title('¡Atención! Ya existe una reserva confirmada en ese departamento para las fechas seleccionadas.')
                                        ->danger()
                                        ->persistent()
                                        ->send();
                                }
                            }
                        } else {
                            $set('cantidadNochesLabel', 0);
                        }
                    }),
                Forms\Components\Placeholder::make('cantidadNochesLabel')
                    ->label('Cantidad de Noches')
                    ->content(function (callable $get) {
                        $inicio = $get('fechaInicio');
                        $fin = $get('fechaFin');
                        return \App\Services\ReservaService::calcularCantidadNoches($inicio, $fin);
                    }),
                Forms\Components\Hidden::make('cantidadNoches')
                    ->dehydrated()
                    ->default(function (callable $get) {
                        $inicio = $get('fechaInicio');
                        $fin = $get('fechaFin');
                        return \App\Services\ReservaService::calcularCantidadNoches($inicio, $fin);
                    })
                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($calcularTotalAPagar) {
                        $calcularTotalAPagar($get, $set);
                    }),
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'Confirmada' => 'Confirmada',
                        'Cancelada' => 'Cancelada',
                        'Pendiente' => 'Pendiente',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('costoPorNoche')->label('Costo por Noche')->numeric()->required()
                    ->reactive(),
                Forms\Components\TextInput::make('montoLimpieza')->label('Monto Limpieza')->numeric()->required()
                    ->reactive(),
                Forms\Components\TextInput::make('montoGarantia')
                    ->label('Monto Garantía')
                    ->numeric()
                    ->reactive()
                    ->default(0),
                Forms\Components\Placeholder::make('comisionCanal')
                    ->label('Comisión Canal')
                    ->content(function (callable $get) {
                        $canalId = $get('idCanalReserva');
                        $costoPorNoche = $get('costoPorNoche');
                        $cantidadNoches = $get('cantidadNoches');
                        return \App\Services\ReservaService::calcularComisionCanal($canalId, $costoPorNoche, $cantidadNoches);
                    }),
                Forms\Components\Hidden::make('comisionCanal')->dehydrated(),
                Forms\Components\Placeholder::make('montoReserva')
                    ->label('Monto Reserva')
                    ->content(function (callable $get) {
                        $costoPorNoche = $get('costoPorNoche');
                        $cantidadNoches = $get('cantidadNoches');
                        $comision = \App\Services\ReservaService::calcularComisionCanal($get('idCanalReserva'), $costoPorNoche, $cantidadNoches);
                        return \App\Services\ReservaService::calcularMontoReserva($costoPorNoche, $cantidadNoches, $comision);
                    }),
                Forms\Components\Hidden::make('montoReserva')->dehydrated(),
                Forms\Components\Placeholder::make('totalAPagar')
                    ->label('Total a Pagar')
                    ->content(function (callable $get) {
                        $costoPorNoche = $get('costoPorNoche');
                        $cantidadNoches = $get('cantidadNoches');
                        $montoLimpieza = $get('montoLimpieza');
                        $montoGarantia = $get('montoGarantia');
                        return \App\Services\ReservaService::calcularTotalAPagar($costoPorNoche, $cantidadNoches, $montoLimpieza, $montoGarantia);
                    }),
                Forms\Components\Hidden::make('totalAPagar')->dehydrated(),
                Forms\Components\Placeholder::make('montoEmpresaAdministradora')
                    ->label('Monto Empresa Administradora')
                    ->content(function (callable $get) {
                        $idDepartamento = $get('idDepartamento');
                        $fechaReserva = $get('fechaInicio');
                        $costoPorNoche = $get('costoPorNoche');
                        $cantidadNoches = $get('cantidadNoches');
                        $comision = \App\Services\ReservaService::calcularComisionCanal($get('idCanalReserva'), $costoPorNoche, $cantidadNoches);
                        $montoReserva = \App\Services\ReservaService::calcularMontoReserva($costoPorNoche, $cantidadNoches, $comision);
                        return \App\Services\ReservaService::calcularMontoEmpresaAdministradora($idDepartamento, $fechaReserva, $montoReserva);
                    }),
                Forms\Components\Hidden::make('montoEmpresaAdministradora')->dehydrated(),
                Forms\Components\Placeholder::make('montoPropietario')
                    ->label('Monto Propietario')
                    ->content(function (callable $get) {
                        $idDepartamento = $get('idDepartamento');
                        $fechaReserva = $get('fechaInicio');
                        $costoPorNoche = $get('costoPorNoche');
                        $cantidadNoches = $get('cantidadNoches');
                        $comision = \App\Services\ReservaService::calcularComisionCanal($get('idCanalReserva'), $costoPorNoche, $cantidadNoches);
                        $montoReserva = \App\Services\ReservaService::calcularMontoReserva($costoPorNoche, $cantidadNoches, $comision);
                        return \App\Services\ReservaService::calcularMontoPropietario($idDepartamento, $fechaReserva, $montoReserva);
                    }),
                Forms\Components\Hidden::make('montoPropietario')->dehydrated(),
                Forms\Components\TextInput::make('cantidadHuespedes')
                    ->label('Cantidad de Huéspedes')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Columns\TextColumn::make('descuentoAplicado')->label('Descuento'),
                Tables\Columns\TextColumn::make('comisionCanal')->label('Comisión Canal'),
                Tables\Columns\TextColumn::make('montoReserva')->label('Monto Reserva'),
                Tables\Columns\TextColumn::make('montoLimpieza')->label('Monto Limpieza'),
                Tables\Columns\TextColumn::make('montoGarantia')->label('Monto Garantía'),
                Tables\Columns\TextColumn::make('montoEmpresaAdministradora')->label('Monto Empresa'),
                Tables\Columns\TextColumn::make('montoPropietario')->label('Monto Propietario'),
            ])
            ->filters([
                SelectFilter::make('estado')
                ->options([
                    'Confirmada' => 'Confirmada',
                    'Cancelada' => 'Cancelada',
                    'Pendiente' => 'Pendiente',
                ]),
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
        // Validar y forzar todos los campos base requeridos
        $required = [
            'idDepartamento', 'idHuesped', 'idCanalReserva', 'fechaInicio', 'fechaFin',
            'costoPorNoche', 'montoLimpieza', 'cantidadNoches', 'estado'
        ];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                throw new \Exception("El campo '$field' es obligatorio y no fue proporcionado.");
            }
        }
        // Si no se proporciona montoGarantia, asignar 0
        if (!isset($data['montoGarantia']) || $data['montoGarantia'] === '' || $data['montoGarantia'] === null) {
            $data['montoGarantia'] = 0;
        }
        // Forzar tipos y valores por defecto
        $data['idDepartamento'] = (int) $data['idDepartamento'];
        $data['idHuesped'] = (int) $data['idHuesped'];
        $data['idCanalReserva'] = (int) $data['idCanalReserva'];
        $data['costoPorNoche'] = (float) $data['costoPorNoche'];
        $data['montoLimpieza'] = (float) $data['montoLimpieza'];
        $data['montoGarantia'] = (float) $data['montoGarantia'];
        $data['cantidadNoches'] = (int) $data['cantidadNoches'];
        $data['cantidadHuespedes'] = isset($data['cantidadHuespedes']) && $data['cantidadHuespedes'] !== '' && $data['cantidadHuespedes'] !== null ? (int) $data['cantidadHuespedes'] : 1;
        $data['descuentoAplicado'] = isset($data['descuentoAplicado']) ? (float) $data['descuentoAplicado'] : 0;
        // Eliminar lógica de overbooking
        $data['estado'] = $data['estado'] ?? 'Confirmada';
        $data['fechaInicio'] = $data['fechaInicio'] ?? now();
        $data['fechaFin'] = $data['fechaFin'] ?? now();

        // Cálculos robustos (nunca null)
        $data['comisionCanal'] = (float) (\App\Services\ReservaService::calcularComisionCanal($data['idCanalReserva'], $data['costoPorNoche'], $data['cantidadNoches']) ?? 0);
        $data['montoReserva'] = (float) (\App\Services\ReservaService::calcularMontoReserva($data['costoPorNoche'], $data['cantidadNoches'], $data['comisionCanal']) ?? 0);
        $data['totalAPagar'] = (float) (\App\Services\ReservaService::calcularTotalAPagar($data['costoPorNoche'], $data['cantidadNoches'], $data['montoLimpieza'], $data['montoGarantia']) ?? 0);
        $data['montoEmpresaAdministradora'] = (float) (\App\Services\ReservaService::calcularMontoEmpresaAdministradora($data['idDepartamento'], $data['fechaInicio'], $data['montoReserva']) ?? 0);
        $data['montoPropietario'] = (float) (\App\Services\ReservaService::calcularMontoPropietario($data['idDepartamento'], $data['fechaInicio'], $data['montoReserva']) ?? 0);

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Repetir la lógica para edición
        return self::mutateFormDataBeforeCreate($data);
    }
}
