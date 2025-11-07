<?php

namespace App\Filament\Resources\ReservaResource\Traits;

use Filament\Forms;
use App\Models\Departamento;
use App\Models\Huesped;
use App\Services\ReservaService;
use Carbon\Carbon;
use Filament\Notifications\Notification;

trait HasReservaFormFields
{
    use HasReservaCalculations;

    protected static function getDepartamentoField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('idDepartamento')
            ->label('Departamento')
            ->relationship('departamento', 'nombreEdificio')
            ->required()
            ->searchable()
            ->getSearchResultsUsing(function (string $search) {
                return Departamento::query()
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
                $departamento = Departamento::find($value);
                if (!$departamento) return null;
                return $departamento->nombreEdificio . ' - Piso:' . ($departamento->piso ?? '-') . ' - Nro:' . ($departamento->numero ?? '-');
            })
            ->reactive()
            ->afterStateUpdated(static::getRecalculationHandler());
    }
    protected static function getHuespedField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('idHuesped')
            ->label('Huesped')
            ->relationship('huesped', 'nombre')
            ->required()
            ->reactive()
            ->searchable()
            ->getSearchResultsUsing(function (string $search) {
                return Huesped::query()
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
            ])
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                if ($state) {
                    $huesped = Huesped::find($state);
                    if ($huesped && ($huesped->enListaNegra === true || $huesped->enListaNegra == 1)) {
                        Notification::make('huesped-lista-negra')
                            ->title('¡Atención! El huésped seleccionado está en lista negra.')
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                    $avgCal = \App\Models\Calificacion::averageForHuesped($state);
                    $avgRes = \App\Models\Resena::where('idHuesped', $state)->avg('valor');
                    $avg = $avgCal ?? ($avgRes !== null ? round((float) $avgRes, 2) : null);
                    if ($avg !== null && $avg < 4) {
                        $latestBad = \App\Models\Resena::where('idHuesped', $state)
                            ->where('valor', '<', 4)
                            ->orderByDesc('fecha')
                            ->first();

                        $message = "Calificación promedio: " . number_format($avg, 2);
                        if ($latestBad) {
                            $message .= "\nÚltima reseña (valor {$latestBad->valor}): " . ($latestBad->argumento ?? '-');
                        }

                        Notification::make('huesped-baja-calificacion')
                            ->title('Advertencia: huésped con calificación baja')
                            ->warning()
                            ->body($message)
                            ->persistent()
                            ->send();
                    }
                    try {
                        if (config('app.debug')) {
                            Notification::make('huesped-debug-promedio')
                                ->title('DEBUG: promedio huésped')
                                ->info()
                                ->body('Promedio calculado: ' . ($avg === null ? 'null' : number_format($avg, 2)))
                                ->send();
                        }
                    } catch (\Throwable $e) {
                        // No bloquear la UI por errores de debug
                    }
                    $set('huespedInfo', [
                        'avg' => $avg,
                        'latest' => isset($latestBad) ? $latestBad->argumento : null,
                        'latest_valor' => isset($latestBad) ? $latestBad->valor : null,
                    ]);
                } else {
                    $set('huespedInfo', null);
                }
            });
    }
    protected static function getFechasFields(): array
    {
        return [
            Forms\Components\DatePicker::make('fechaInicio')
                ->label('Fecha Inicio')
                ->required()
                ->reactive()
                ->afterStateUpdated(static::getFechaStateHandler('inicio')),

            Forms\Components\DatePicker::make('fechaFin')
                ->label('Fecha Fin')
                ->required()
                ->reactive()
                ->afterStateUpdated(static::getFechaStateHandler('fin')),

            Forms\Components\Placeholder::make('cantidadNochesLabel')
                ->label('Cantidad de Noches')
                ->content(function (callable $get) {
                    return ReservaService::calcularCantidadNoches($get('fechaInicio'), $get('fechaFin'));
                }),

            Forms\Components\Hidden::make('cantidadNoches')
                ->dehydrated()
                ->default(function (callable $get) {
                    return ReservaService::calcularCantidadNoches($get('fechaInicio'), $get('fechaFin'));
                })
        ];
    }
    protected static function getHuespedInfoPlaceholder(): Forms\Components\Placeholder
    {
        return Forms\Components\Placeholder::make('huespedInfo')
            ->label('Información del Huésped')
            ->content(function (callable $get) {
                $info = $get('huespedInfo');
                if (!$info) {
                    return '-';
                }
                $avg = $info['avg'] ?? null;
                $latest = $info['latest'] ?? null;
                $latest_valor = $info['latest_valor'] ?? null;

                $lines = [];
                if ($avg !== null) {
                    $lines[] = 'Calificación promedio: ' . number_format($avg, 2);
                }
                if ($latest !== null) {
                    $lines[] = 'Última reseña (valor ' . $latest_valor . '): "' . $latest . '"';
                }
                return implode('<br />', $lines) ?: '-';
            })
            ->columns(2);
    }
    protected static function getFechaStateHandler(string $tipo): callable
    {
        return function ($state, callable $set, callable $get) use ($tipo) {
            $inicio = $tipo === 'inicio' ? $state : $get('fechaInicio');
            $fin = $tipo === 'fin' ? $state : $get('fechaFin');
            $idDepartamento = $get('idDepartamento');

            if ($inicio && $fin) {
                $inicioDate = Carbon::parse($inicio)->startOfDay();
                $finDate = Carbon::parse($fin)->startOfDay();

                // Validación de fechas
                if (($tipo === 'inicio' && $inicioDate->greaterThan($finDate)) ||
                    ($tipo === 'fin' && $finDate->lessThan($inicioDate))) {
                    Notification::make()
                        ->title("La fecha $tipo no puede ser " . ($tipo === 'inicio' ? 'mayor' : 'menor') . " a la fecha " . ($tipo === 'inicio' ? 'fin' : 'inicio') . ".")
                        ->danger()
                        ->send();
                }
                if ($idDepartamento) {
                    static::validarDisponibilidadFechas($idDepartamento, $inicio, $fin);
                }
            }
            $dias = ReservaService::calcularCantidadNoches($inicio, $fin);
            $set('cantidadNochesLabel', $dias);
            $set('cantidadNoches', $dias);
            static::calcularTotalBruto($get, $set);
            static::calcularComision($get, $set);
            static::calcularMontoReserva($get, $set);
            static::calcularMontosDistribucion($get, $set);
        };
    }
    protected static function getMontosFields(): array
    {
        return [
            Forms\Components\Select::make('idCanalReserva')
                ->label('Canal de Reserva')
                ->relationship('canalReserva', 'nombre')
                ->required()
                ->reactive()
                ->searchable(),
            Forms\Components\TextInput::make('costoPorNoche')
                ->label('Costo por Noche')
                ->numeric()
                ->required()
                ->reactive()
                ->afterStateUpdated(static::getRecalculationHandler()),
            Forms\Components\TextInput::make('montoLimpieza')
                ->label('Monto Limpieza')
                ->numeric()
                ->required()
                ->reactive(),
            Forms\Components\TextInput::make('montoGarantia')
                ->label('Monto Garantía')
                ->numeric()
                ->reactive()
                ->default(0)
                ->afterStateUpdated(function (callable $get, callable $set) {
                    static::calcularMontoReserva($get, $set);
                    static::calcularMontosDistribucion($get, $set);
                }),
            Forms\Components\TextInput::make('cantidadHuespedes')
                ->label('Cantidad de Huéspedes')
                ->numeric()
                ->default(1)
                ->required(),
            Forms\Components\TextInput::make('descuentoAplicado')
                ->label('Descuento')
                ->helperText('Monto a descontar del total')
                ->numeric()
                ->default(0)
                ->reactive()
                ->afterStateUpdated(function (callable $get, callable $set) {
                    static::calcularMontoReserva($get, $set);
                    static::calcularMontosDistribucion($get, $set);
                }),
            Forms\Components\Section::make('Montos de la Reserva')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Placeholder::make('totalBrutoCalculado')
                                ->label('Total Bruto')
                                ->content(function (callable $get) {
                                    $costoPorNoche = floatval($get('costoPorNoche') ?? 0);
                                    $cantidadNoches = intval($get('cantidadNoches') ?? 0);
                                    return '$' . number_format($costoPorNoche * $cantidadNoches, 2);
                                }),

                            Forms\Components\Placeholder::make('montoReservaCalculado')
                                ->label('Monto de Reserva')
                                ->content(function (callable $get) {
                                    return '$' . number_format($get('montoReserva') ?? 0, 2);
                                }),

                            Forms\Components\Placeholder::make('comisionCanalCalculada')
                                ->label('Comisión del Canal')
                                ->content(function (callable $get) {
                                    return '$' . number_format($get('comisionCanal') ?? 0, 2);
                                }),
                        ]),

                    Forms\Components\Section::make('Distribución de Ingresos')
                        ->collapsible()
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Placeholder::make('montoEmpresaCalculado')
                                        ->label('Empresa Administradora')
                                        ->content(function (callable $get) {
                                            return '$' . number_format($get('montoEmpresaAdministradora') ?? 0, 2);
                                        }),

                                    Forms\Components\Placeholder::make('montoPropietarioCalculado')
                                        ->label('Propietario')
                                        ->content(function (callable $get) {
                                            return '$' . number_format($get('montoPropietario') ?? 0, 2);
                                        }),
                                ]),
                        ]),
                ]),
            Forms\Components\Hidden::make('totalBruto'),
            Forms\Components\Hidden::make('montoReserva'),
            Forms\Components\Hidden::make('comisionCanal'),
            Forms\Components\Hidden::make('montoBase'),
            Forms\Components\Hidden::make('montoEmpresaAdministradora'),
            Forms\Components\Hidden::make('montoPropietario'),
        ];
    }
    protected static function getRecalculationHandler(): callable
    {
        return function (...$args) {
            $get = null;
            $set = null;
            if (count($args) >= 3 && is_callable($args[1]) && is_callable($args[2])) {
                $set = $args[1];
                $get = $args[2];
            } elseif (count($args) >= 2 && is_callable($args[0]) && is_callable($args[1])) {
                $get = $args[0];
                $set = $args[1];
            } else {
                $callables = array_values(array_filter($args, 'is_callable'));
                if (count($callables) >= 2) {
                    $get = $callables[0];
                    $set = $callables[1];
                }
            }
            if (!is_callable($get) || !is_callable($set)) {
                return;
            }
            try {
                static::calcularTotalBruto($get, $set);
                static::calcularComision($get, $set);
                static::calcularMontoReserva($get, $set);
                static::calcularMontosDistribucion($get, $set);
            } catch (\Throwable $e) {
                // No interrumpir la UI si algo falla en los cálculos
            }
        };
    }
    protected static function getEstadoField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('estado')
            ->label('Estado')
            ->options([
                'Confirmada' => 'Confirmada',
                'Cancelada' => 'Cancelada',
                'Pendiente' => 'Pendiente',
            ])
            ->required();
    }
}