<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalificacionResource\Pages;
use App\Filament\Resources\CalificacionResource\RelationManagers;
use App\Models\Calificacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CalificacionResource extends Resource
{
    protected static ?string $model = Calificacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('idReserva')
                    ->label('Reserva')
                    ->relationship('reserva', 'id')
                    ->required(),
                Forms\Components\Select::make('valor')
                    ->label('Valor')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ])
                    ->required()
                    ->reactive()
                    ->rules(['integer', 'between:1,5']),
                Forms\Components\TextInput::make('comentario')
                    ->label('Comentario')
                    ->required()
                    ->maxLength(50),
                // Fecha se asigna automáticamente al crear la calificación
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('reserva.id')->label('Reserva'),
                Tables\Columns\TextColumn::make('valor')->label('Valor'),
                Tables\Columns\TextColumn::make('comentario')->label('Comentario'),
                Tables\Columns\TextColumn::make('fecha')->label('Fecha'),
            ])
            ->filters([
                Tables\Filters\Filter::make('valor')
                    ->form([
                        Forms\Components\Select::make('valor')
                            ->label('Valor')
                            ->options([
                                1 => '1',
                                2 => '2',
                                3 => '3',
                                4 => '4',
                                5 => '5',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(isset($data['valor']) && $data['valor'] !== null, function ($query) use ($data) {
                            $query->where('valor', $data['valor']);
                        });
                    }),
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('fecha')->label('Fecha'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['fecha'], function ($query, $value) {
                            $query->where('fecha', $value);
                        });
                    }),
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
            'index' => Pages\ListCalificacions::route('/'),
            'create' => Pages\CreateCalificacion::route('/create'),
            'edit' => Pages\EditCalificacion::route('/{record}/edit'),
        ];
    }
    
}
