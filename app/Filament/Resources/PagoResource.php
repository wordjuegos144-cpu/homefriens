<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoResource\Pages;
use App\Filament\Resources\PagoResource\RelationManagers;
use App\Models\Pago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipoPago')
                    ->label('Tipo de Pago')
                    ->options([
                        'Reserva' => 'Reserva',
                        'Limpieza' => 'Limpieza',
                        'Deposito' => 'Depósito',
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('idLimpieza')
                    ->label('Limpieza')
                    ->relationship('limpieza', 'id')
                    ->visible(fn(callable $get) => $get('tipoPago') === 'Limpieza')
                    ->required(fn(callable $get) => $get('tipoPago') === 'Limpieza')
                    ->reactive(),
                Forms\Components\Select::make('idReserva')
                    ->label('Reserva')
                    ->relationship('reserva', 'id')
                    ->visible(fn(callable $get) => in_array($get('tipoPago'), ['Reserva', 'Deposito']))
                    ->required(fn(callable $get) => in_array($get('tipoPago'), ['Reserva', 'Deposito']))
                    ->reactive(),
                Forms\Components\TextInput::make('monto')
                    ->label('Monto')
                    ->numeric()
                    ->required(),
                Forms\Components\DateTimePicker::make('fechaPago')
                    ->label('Fecha de Pago')
                    ->required(),
                Forms\Components\Select::make('formaPago')
                    ->label('Forma de Pago')
                    ->options([
                        'QR' => 'QR',
                        'Airtm' => 'Airtm',
                        'Efectivo BS' => 'Efectivo BS',
                        'Efectivo USD' => 'Efectivo USD',
                        'Otros' => 'Otros',
                    ])
                    ->required(),
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
                    ->image()
                    ->preserveFilenames()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('reserva.id')->label('Reserva'),
                Tables\Columns\TextColumn::make('tipoPago')->label('Tipo de Pago'),
                Tables\Columns\TextColumn::make('monto')->label('Monto'),
                Tables\Columns\TextColumn::make('fechaPago')->label('Fecha de Pago'),
                Tables\Columns\TextColumn::make('formaPago')->label('Forma de Pago'),
                Tables\Columns\TextColumn::make('estadoPago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'gray',
                        'Completado' => 'success',
                    }),
                Tables\Columns\TextColumn::make('comprobante')->label('Comprobante'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipoPago')
                    ->label('Tipo de Pago')
                    ->options([
                        'Reserva' => 'Reserva',
                        'Limpieza' => 'Limpieza',
                        'Deposito' => 'Deposito',
                    ]),
                Tables\Filters\SelectFilter::make('formaPago')
                    ->label('Forma de Pago')
                    ->options([
                        'QR' => 'QR',
                        'Airtm' => 'Airtm',
                        'Efectivo BS' => 'Efectivo BS',
                        'Efectivo USD' => 'Efectivo USD',
                    ]),
                Tables\Filters\SelectFilter::make('estadoPago')
                    ->label('Estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Completado' => 'Completado',
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
            'index' => Pages\ListPagos::route('/'),
            'create' => Pages\CreatePago::route('/create'),
            'edit' => Pages\EditPago::route('/{record}/edit'),
        ];
    }
}
