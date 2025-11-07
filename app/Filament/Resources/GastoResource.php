<?php

namespace App\Filament\Resources;

use Maatwebsite\Excel\Facades\Excel;
use App\Filament\Resources\GastoResource\Pages;
use App\Models\Gasto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Finanzas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('idDepartamento')
                ->label('Departamento')
                ->relationship('departamento', 'nombreEdificio')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('monto')
                ->label('Monto')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('tipo')
                ->label('Tipo de Gasto')
                ->options([
                    'Mantenimiento' => 'Mantenimiento',
                    'Servicios' => 'Servicios',
                    'Suministros' => 'Suministros',
                    'Otro' => 'Otro',
                ])
                ->required(),

            Forms\Components\DatePicker::make('fecha')
                ->label('Fecha')
                ->required()
                ->default(now()),

            Forms\Components\Select::make('estado')
                ->label('Estado')
                ->options([
                    'Pendiente' => 'Pendiente',
                    'Cubierto' => 'Cubierto',
                ])
                ->required()
                ->default('Pendiente'),

            Forms\Components\Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Exportar Gastos')
                    ->icon('heroicon-o-document-arrow-down')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->label('Fecha Inicio')
                            ->required(),
                        Forms\Components\DatePicker::make('fecha_fin')
                            ->label('Fecha Fin')
                            ->required(),
                        Forms\Components\Select::make('idDepartamento')
                            ->label('Departamento')
                            ->relationship('departamento', 'nombreEdificio')
                            ->searchable(),
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
                        $query = Gasto::query()
                            ->whereBetween('fecha', [$data['fecha_inicio'], $data['fecha_fin']]);
                        
                        if (!empty($data['idDepartamento'])) {
                            $query->where('idDepartamento', $data['idDepartamento']);
                        }

                        $gastos = $query->with(['departamento'])->get();

                        if ($gastos->isEmpty()) {
                            Filament\Notifications\Notification::make()
                                ->warning()
                                ->title('No hay datos para exportar')
                                ->body('No se encontraron gastos en el rango de fechas seleccionado.')
                                ->send();
                            return;
                        }

                        return Excel::download(
                            new \App\Exports\GastosExport($gastos),
                            'reporte-gastos.' . $data['formato']
                        );
                    })
                    ->color('success')
            ])
            ->columns([
                TextColumn::make('departamento.nombreEdificio')->label('Departamento')->searchable(),
                TextColumn::make('monto')->label('Monto')->money('usd', true),
                TextColumn::make('tipo')->label('Tipo'),
                TextColumn::make('fecha')->label('Fecha')->date(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'danger',
                        'Cubierto' => 'success',
                        default => 'secondary',
                    }),
                TextColumn::make('descripcion')->label('Descripción')->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('idDepartamento')
                    ->label('Departamento')
                    ->relationship('departamento', 'nombreEdificio'),

                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Cubierto' => 'Cubierto',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGastos::route('/'),
            'create' => Pages\CreateGasto::route('/create'),
            'edit' => Pages\EditGasto::route('/{record}/edit'),
        ];
    }
}
