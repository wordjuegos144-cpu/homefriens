<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResenaResource\Pages;
use App\Models\Resena;
use App\Models\Huesped;
use App\Models\Propietario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ResenaResource extends Resource
{
    protected static ?string $model = Resena::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $label = 'Reseña Huésped';
    protected static ?string $pluralLabel = 'Reseñas Huéspedes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('idHuesped')
                    ->label('Huésped')
                    ->relationship('huesped', 'nombre')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('idPropietario')
                    ->label('Propietario')
                    ->relationship('propietario', 'nombre')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('valor')
                    ->label('Calificación')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ])
                    ->required()
                    ->rules(['integer', 'between:1,5']),

                Forms\Components\Textarea::make('argumento')
                    ->label('Argumento')
                    ->rows(3)
                    ->nullable(),

                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('huesped.nombre')->label('Huésped')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('propietario.nombre')->label('Propietario')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('valor')->label('Calificación')->sortable(),
                Tables\Columns\TextColumn::make('argumento')->limit(50)->label('Argumento'),
                Tables\Columns\TextColumn::make('fecha')->label('Fecha')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResenas::route('/'),
            'create' => Pages\CreateResena::route('/create'),
            'edit' => Pages\EditResena::route('/{record}/edit'),
        ];
    }
}
