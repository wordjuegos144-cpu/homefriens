<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HuespedResource\Pages;
use App\Filament\Resources\HuespedResource\RelationManagers;
use App\Models\Huesped;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HuespedResource extends Resource
{
    protected static ?string $model = Huesped::class;
     protected static ?string $navigationGroup = 'Huespedes';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('Whatsapp')
                    ->label('Whatsapp')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('numeroDocumento')
                    ->label('Número Documento')
                    ->maxLength(100),
                Forms\Components\Toggle::make('enListaNegra')
                    ->label('En lista negra'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('nombre')->label('Nombre'),
                Tables\Columns\TextColumn::make('Whatsapp')->label('Whatsapp'),
                Tables\Columns\TextColumn::make('numeroDocumento')->label('Documento'),
                Tables\Columns\IconColumn::make('enListaNegra')
                    ->label('Lista negra')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('enListaNegra')
                    ->form([
                        Forms\Components\Toggle::make('enListaNegra')->label('En lista negra'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(isset($data['enListaNegra']), function ($query) use ($data) {
                            $query->where('enListaNegra', $data['enListaNegra']);
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
            'index' => Pages\ListHuespeds::route('/'),
            'create' => Pages\CreateHuesped::route('/create'),
            'edit' => Pages\EditHuesped::route('/{record}/edit'),
        ];
    }
}
