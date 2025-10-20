<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DevolucionResource\Pages;
use App\Filament\Resources\DevolucionResource\RelationManagers;
use App\Models\Devolucion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

class DevolucionResource extends Resource
{
    protected static ?string $model = Devolucion::class;
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('idReserva')
                    ->label('Reserva')
                    ->options(function () {
                        return \App\Models\Reserva::where('montoGarantia', '>', 0)
                            ->get()
                            ->pluck('id', 'id');
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('monto')
                    ->label('Monto')
                    ->numeric()
                    ->required(),
                Forms\Components\DatePicker::make('fechaDevolucion')
                    ->label('Fecha de Devolución')
                    ->required(),
                Forms\Components\DatePicker::make('fechaProcesada')
                    ->label('Fecha Procesada')
                    ->rules(['date', 'before_or_equal:today'])
                    ->helperText('No se permiten fechas futuras.'),
                Forms\Components\Select::make('estadoPago')
                    ->label('Estado del Pago')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Completado' => 'Completado',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('comprobante')
                    ->label('Comprobante')
                    ->disk('public')
                    ->directory('comprobantes')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('reserva.id')->label('Reserva'),
                Tables\Columns\TextColumn::make('monto')->label('Monto'),
                Tables\Columns\TextColumn::make('fechaDevolucion')->label('Fecha Devolución'),
                Tables\Columns\TextColumn::make('fechaProcesada')->label('Fecha Procesada'),
                Tables\Columns\TextColumn::make('estadoPago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'gray',
                        'Completado' => 'success',
                    }),
                Tables\Columns\TextColumn::make('comprobante')->label('Comprobante'),
            ])
            ->filters([
                SelectFilter::make('estadoPago')
                ->options([
                    'Completado' => 'Completado',
                    'Pendiente' => 'Pendiente',
                ]),
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
            'index' => Pages\ListDevolucions::route('/'),
            'create' => Pages\CreateDevolucion::route('/create'),
            'edit' => Pages\EditDevolucion::route('/{record}/edit'),
        ];
    }
}
