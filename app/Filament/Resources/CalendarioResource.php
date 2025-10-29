<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarioResource\Pages;
use App\Models\Reserva;
use App\Models\Departamento;
use App\Models\Huesped;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class CalendarioResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Herramientas';
    protected static ?string $label = 'Calendario';
    protected static ?string $pluralLabel = 'Calendarios';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('idDepartamento')
                    ->label('Departamento')
                    ->options(fn(): array => Departamento::all()->pluck('nombreEdificio', 'id')->toArray())
                    ->required(),
                Forms\Components\Select::make('idHuesped')
                    ->label('Huésped')
                    ->options(fn(): array => Huesped::all()->pluck('nombre', 'id')->toArray())
                    ->searchable(),
                Forms\Components\DatePicker::make('fechaInicio')
                    ->label('Fecha Inicio')
                    ->required(),
                Forms\Components\DatePicker::make('fechaFin')
                    ->label('Fecha Fin')
                    ->required(),
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'Confirmada' => 'Confirmada',
                        'Pendiente' => 'Pendiente',
                        'Cancelada' => 'Cancelada',
                    ])->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('departamento.nombreEdificio')->label('Departamento'),
                Tables\Columns\TextColumn::make('huesped.nombre')->label('Huésped'),
                Tables\Columns\TextColumn::make('fechaInicio')->label('Inicio'),
                Tables\Columns\TextColumn::make('fechaFin')->label('Fin'),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'gray',
                        'Confirmada' => 'success',
                        'Cancelada' => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'Confirmada' => 'Confirmada',
                        'Pendiente' => 'Pendiente',
                        'Cancelada' => 'Cancelada',
                    ]),
                SelectFilter::make('idDepartamento')
                    ->label('Departamento')
                    ->relationship('departamento', 'nombreEdificio'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalendarios::route('/'),
            'create' => Pages\CreateCalendario::route('/create'),
            'edit' => Pages\EditCalendario::route('/{record}/edit'),
        ];
    }
}
