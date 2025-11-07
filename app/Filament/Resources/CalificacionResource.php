<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalificacionResource\Pages;
use App\Filament\Resources\CalificacionResource\RelationManagers;
use App\Models\Calificacion;
use App\Models\Reserva;
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
                    ->options(Reserva::all()->mapWithKeys(function ($r) {
                        $nombre = optional($r->departamento)->nombreEdificio ?? 'Departamento';
                        $numero = optional($r->departamento)->numero ?? '-';
                        return [
                            $r->id => $nombre . ' - Nro: ' . $numero . ' - ' . $r->fechaInicio . ' / ' . $r->fechaFin
                        ];
                    }))
                    ->searchable()
                    ->reactive()
                    ->required(),

                Forms\Components\Placeholder::make('departamento')
                    ->label('Departamento')
                    ->content(function (callable $get) {
                        $id = $get('idReserva');
                        if (!$id) return '-';
                        $res = Reserva::find($id);
                        if (!$res || !$res->departamento) return '-';
                        return $res->departamento->nombreEdificio . ' - Nro: ' . ($res->departamento->numero ?? '-');
                    }),

                Forms\Components\Placeholder::make('huesped')
                    ->label('Huésped')
                    ->content(function (callable $get) {
                        $id = $get('idReserva');
                        if (!$id) return '-';
                        $res = Reserva::find($id);
                        if (!$res || !$res->huesped) return '-';
                        return $res->huesped->nombre . ' - ' . ($res->huesped->Whatsapp ?? '-');
                    }),

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

                Forms\Components\Textarea::make('comentario')
                    ->label('Reseña')
                    ->rows(3)
                    ->nullable(),
                // Fecha se asigna automáticamente al crear la calificación
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('reserva.departamento.nombreEdificio')
                    ->label('Departamento')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reserva.huesped.nombre')
                    ->label('Huésped')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor')->label('Calificación')->sortable(),
                Tables\Columns\TextColumn::make('comentario')->limit(50)->label('Reseña'),
                Tables\Columns\TextColumn::make('fecha')->label('Fecha')->dateTime()->sortable(),
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
