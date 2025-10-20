<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartamentoResource\Pages;
use App\Filament\Resources\DepartamentoResource\RelationManagers;
use App\Models\Departamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartamentoResource extends Resource
{
    protected static ?string $model = Departamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('idEmpresaAdministradora')
                    ->label('Empresa Administradora')
                    ->relationship('empresaAdministradora', 'nombre')
                    ->required(),
                Forms\Components\TextInput::make('nombreEdificio')->label('Nombre Edificio')->required()->maxLength(100),
                Forms\Components\TextInput::make('direccion')->label('Dirección')->required()->maxLength(255),
                Forms\Components\TextInput::make('piso')->label('Piso')->numeric()->nullable(),
                Forms\Components\TextInput::make('numero')->label('Número')->numeric()->nullable(),
                Forms\Components\TextInput::make('capacidadNormal')->label('Capacidad Normal')->numeric()->required(),
                Forms\Components\TextInput::make('capacidadExtra')->label('Capacidad Extra')->numeric()->required(),
                Forms\Components\TextInput::make('nombrePropietario')->label('Nombre Propietario')->required()->maxLength(100),
                Forms\Components\TextInput::make('telefonoPropietario')->label('Teléfono Propietario')->required()->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('empresaAdministradora.nombre')->label('Empresa'),
                Tables\Columns\TextColumn::make('nombreEdificio')->label('Edificio'),
                Tables\Columns\TextColumn::make('direccion')->label('Dirección'),
                Tables\Columns\TextColumn::make('piso')->label('Piso'),
                Tables\Columns\TextColumn::make('numero')->label('Número'),
                Tables\Columns\TextColumn::make('capacidadNormal')->label('Capacidad Normal'),
                Tables\Columns\TextColumn::make('capacidadExtra')->label('Capacidad Extra'),
                Tables\Columns\TextColumn::make('nombrePropietario')->label('Propietario'),
                Tables\Columns\TextColumn::make('telefonoPropietario')->label('Teléfono'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('idEmpresaAdministradora')
                    ->label('Empresa Administradora')
                    ->relationship('empresaAdministradora', 'nombre'),
                Tables\Filters\Filter::make('nombreEdificio')
                    ->form([
                        Forms\Components\TextInput::make('nombreEdificio')
                            ->label('Nombre Edificio'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['nombreEdificio'], function ($query, $value) {
                            $query->where('nombreEdificio', 'like', "%$value%");
                        });
                    }),
                Tables\Filters\Filter::make('direccion')
                    ->form([
                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['direccion'], function ($query, $value) {
                            $query->where('direccion', 'like', "%$value%");
                        });
                    }),
                Tables\Filters\Filter::make('nombrePropietario')
                    ->form([
                        Forms\Components\TextInput::make('nombrePropietario')
                            ->label('Propietario'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['nombrePropietario'], function ($query, $value) {
                            $query->where('nombrePropietario', 'like', "%$value%");
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
            'index' => Pages\ListDepartamentos::route('/'),
            'create' => Pages\CreateDepartamento::route('/create'),
            'edit' => Pages\EditDepartamento::route('/{record}/edit'),
        ];
    }
}
