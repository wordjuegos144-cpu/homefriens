<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvidenciaLimpiezaResource\Pages;
use App\Models\EvidenciaLimpieza;
use App\Models\Limpieza;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;

class EvidenciaLimpiezaResource extends Resource
{
    protected static ?string $model = EvidenciaLimpieza::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $label = 'Evidencia de Limpieza';
    protected static ?string $pluralLabel = 'Evidencias de Limpieza';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('limpieza_id')
                    ->label('Limpieza')
                    ->options(Limpieza::all()->pluck('id', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'Limpieza' => 'Limpieza',
                        'Daño' => 'Daño',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('archivo')
                    ->label('Archivo de Evidencia')
                    ->directory('evidencias-limpieza')
                    ->required()
                    ->maxSize(10240)
                    ->acceptedFileTypes(['image/*', 'application/pdf'])
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('limpieza_id')->label('Limpieza')->sortable(),
                Tables\Columns\TextColumn::make('tipo')->label('Tipo')->sortable(),
                Tables\Columns\ImageColumn::make('archivo')->label('Archivo')->disk('public')->height(40),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Creado'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'Limpieza' => 'Limpieza',
                        'Daño' => 'Daño',
                    ]),
                Tables\Filters\SelectFilter::make('limpieza_id')
                    ->label('Limpieza')
                    ->options(\App\Models\Limpieza::all()->pluck('id', 'id')),
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
            'index' => Pages\ListEvidenciaLimpiezas::route('/'),
            'create' => Pages\CreateEvidenciaLimpieza::route('/create'),
            'edit' => Pages\EditEvidenciaLimpieza::route('/{record}/edit'),
        ];
    }
}
