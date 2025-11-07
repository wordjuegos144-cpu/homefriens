<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LimpiezaResource\Pages;
use App\Models\Limpieza;
use App\Models\Reserva;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;

class LimpiezaResource extends Resource
{
    protected static ?string $model = Limpieza::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $label = 'Limpieza';
    protected static ?string $pluralLabel = 'Limpiezas';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('reserva_id')
                    ->label('Reserva')
                    ->options(Reserva::all()->mapWithKeys(function($reserva) {
                        $nombre = optional($reserva->departamento)->nombreEdificio ?? 'Departamento';
                        $numero = optional($reserva->departamento)->numero ?? '-';
                        return [
                            $reserva->id => $nombre . ' - Nro: ' . $numero . ' - ' . $reserva->fechaInicio . ' / ' . $reserva->fechaFin
                        ];
                    }))
                    ->searchable()
                    ->nullable(),
                Forms\Components\DatePicker::make('fecha_programada')
                    ->label('Fecha Programada')
                    ->required(),
                Forms\Components\TimePicker::make('hora_programada')
                    ->label('Hora Programada')
                    ->nullable(),
                Forms\Components\TextInput::make('monto')
                    ->numeric()
                    ->maxValue(9999.99)
                    ->nullable(),
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Programada' => 'Programada',
                        'Cancelada' => 'Cancelada',
                        'Finalizada' => 'Finalizada',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('reserva_id')
                    ->label('Reserva')
                    ->formatStateUsing(function($state, $record) {
                        if ($record->reserva) {
                            $dep = optional(optional($record->reserva)->departamento);
                            $nombre = $dep->nombreEdificio ?? 'Departamento';
                            $numero = $dep->numero ?? '-';
                            return $nombre . ' - Nro: ' . $numero . ' - ' . $record->reserva->fechaInicio . ' / ' . $record->reserva->fechaFin;
                        }
                        return $state;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_programada')->date()->label('Fecha'),
                Tables\Columns\TextColumn::make('hora_programada')->time()->label('Hora'),
                Tables\Columns\TextColumn::make('monto')->money('USD'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'gray',
                        'Programada' => 'info',
                        // backward-compat: some existing records used 'Programado'
                        'Programado' => 'info',
                        'Cancelada' => 'danger',
                        'Finalizada' => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Creado'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('reserva_id')
                    ->label('Reserva')
                    ->options(\App\Models\Reserva::all()->pluck('id', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLimpiezas::route('/'),
            'create' => Pages\CreateLimpieza::route('/create'),
            'edit' => Pages\EditLimpieza::route('/{record}/edit'),
        ];
    }
}
