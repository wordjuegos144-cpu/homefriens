<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContratoResource\Pages;
use App\Filament\Resources\ContratoResource\RelationManagers;
use App\Models\Contrato;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContratoResource extends Resource
{
    protected static ?string $model = Contrato::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('idDepartamento')
                    ->label('Departamento')
                    ->relationship('departamento', 'nombreEdificio')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('idEmpresaAdministradora')
                    ->label('Empresa Administradora')
                    ->relationship('empresaAdministradora', 'nombre')
                    ->required()
                    ->searchable(),
                Forms\Components\DatePicker::make('fechaInicioContrato')
                    ->label('Fecha Inicio')
                    ->required(),
                Forms\Components\DatePicker::make('fechaFinContrato')
                    ->label('Fecha Fin')
                    ->required(),
                Forms\Components\TextInput::make('comisionContrato')
                    ->label('Comisión (%)')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('departamento.nombreEdificio')->label('Departamento'),
                Tables\Columns\TextColumn::make('empresaAdministradora.nombre')->label('Empresa Administradora'),
                Tables\Columns\TextColumn::make('fechaInicioContrato')->label('Inicio'),
                Tables\Columns\TextColumn::make('fechaFinContrato')->label('Fin'),
                Tables\Columns\TextColumn::make('comisionContrato')->label('Comisión'),
            ])
            ->filters([
                Tables\Filters\Filter::make('fechaInicioContrato')
                    ->form([
                        Forms\Components\DatePicker::make('fechaInicioContrato')->label('Fecha Inicio'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['fechaInicioContrato'], function ($query, $value) {
                            $query->where('fechaInicioContrato', $value);
                        });
                    }),
                Tables\Filters\Filter::make('fechaFinContrato')
                    ->form([
                        Forms\Components\DatePicker::make('fechaFinContrato')->label('Fecha Fin'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['fechaFinContrato'], function ($query, $value) {
                            $query->where('fechaFinContrato', $value);
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
            'index' => Pages\ListContratos::route('/'),
            'create' => Pages\CreateContrato::route('/create'),
            'edit' => Pages\EditContrato::route('/{record}/edit'),
        ];
    }
}
