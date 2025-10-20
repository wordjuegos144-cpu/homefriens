<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpresaLimpiezaResource\Pages;
use App\Models\EmpresaLimpieza;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\TernaryFilter;

class EmpresaLimpiezaResource extends Resource
{
    protected static ?string $model = EmpresaLimpieza::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Empresas';
    protected static ?string $label = 'Empresa de Limpieza';
    protected static ?string $pluralLabel = 'Empresas de Limpieza';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('contacto')
                    ->maxLength(100),
                Forms\Components\TextInput::make('telefono')
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contacto')->searchable(),
                Tables\Columns\TextColumn::make('telefono'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\IconColumn::make('activo')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Creado'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
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
            'index' => Pages\ListEmpresaLimpiezas::route('/'),
            'create' => Pages\CreateEmpresaLimpieza::route('/create'),
            'edit' => Pages\EditEmpresaLimpieza::route('/{record}/edit'),
        ];
    }
}
