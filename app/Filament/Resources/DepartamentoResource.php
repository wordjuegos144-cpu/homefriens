<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartamentoResource\Pages;
use App\Models\Departamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DepartamentoResource extends Resource
{
    protected static ?string $model = Departamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Empresas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('idEmpresaA')
                    ->relationship('empresaAdministradora', 'nombre')
                    ->required(),
                Forms\Components\Select::make('idEmpresaC')
                    ->relationship('empresaConstructora', 'nombre')
                    ->required(),
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('direccion')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('numeroCuartos')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('capacidadPersonas')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('precioRenta')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ubicacionMaps')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('idDepartamento')->label('ID'),
                Tables\Columns\TextColumn::make('empresaAdministradora.nombre')->label('Empresa Administradora'),
                Tables\Columns\TextColumn::make('empresaConstructora.nombre')->label('Empresa Constructora'),
                Tables\Columns\TextColumn::make('nombre')->label('Nombre'),
                Tables\Columns\TextColumn::make('direccion')->label('Dirección'),
                Tables\Columns\TextColumn::make('numeroCuartos')->label('Número de Cuartos'),
                Tables\Columns\TextColumn::make('capacidadPersonas')->label('Capacidad de Personas'),
                Tables\Columns\TextColumn::make('precioRenta')->label('Precio de Renta'),
                Tables\Columns\TextColumn::make('ubicacionMaps')->label('Ubicación Maps'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListDepartamentos::route('/'),
            'create' => Pages\CreateDepartamento::route('/create'),
            'edit' => Pages\EditDepartamento::route('/{record}/edit'),
        ];
    }
}
