<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CanalReservaResource\Pages;
use App\Models\CanalReserva;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class CanalReservaResource extends Resource
{
    protected static ?string $model = CanalReserva::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationGroup = 'Reservas';
    protected static ?string $label = 'Canal de Reserva';
    protected static ?string $pluralLabel = 'Canales de Reserva';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('comision')
                    ->label('Comisión')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(100),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('comision')->label('Comisión')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Creado'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListCanalReservas::route('/'),
            'create' => Pages\CreateCanalReserva::route('/create'),
            'edit' => Pages\EditCanalReserva::route('/{record}/edit'),
            'view' => Pages\ViewCanalReserva::route('/{record}'),
        ];
    }
}
