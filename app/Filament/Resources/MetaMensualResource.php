<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MetaMensualResource\Pages;
use App\Models\MetaMensual;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Support\Colors\Color;
use Maatwebsite\Excel\Facades\Excel;
use App\Filament\Widgets;

class MetaMensualResource extends Resource
{
    protected static ?string $model = MetaMensual::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationLabel = 'Metas Mensuales';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'departamento.nombreEdificio';

    public static function getWidgets(): array
    {
        return [
            Widgets\MetasGastosChart::class,
            Widgets\MetasGastosOverview::class,
            Widgets\GastosLimpiezaWidget::class,
        ];
    }

    public static function form(Form $form): Form
    {
        $meses = array_combine(range(1, 12), [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ]);

        return $form->schema([
            Forms\Components\Select::make('idDepartamento')
                ->relationship('departamento', 'nombreEdificio')
                ->label('Departamento')
                ->required()
                ->searchable(),

            Forms\Components\Select::make('mes')
                ->label('Mes')
                ->options($meses)
                ->required(),

            Forms\Components\TextInput::make('anio')
                ->label('Año')
                ->default(date('Y'))
                ->required()
                ->numeric()
                ->minValue(2020)
                ->maxValue(2050),

            Forms\Components\TextInput::make('valor_meta')
                ->label('Meta Mensual ($)')
                ->required()
                ->numeric()
                ->minValue(0)
                ->prefix('$'),

            Forms\Components\TextInput::make('valor_actual')
                ->label('Valor Actual ($)')
                ->numeric()
                ->minValue(0)
                ->prefix('$')
                ->disabled()
                ->dehydrated(false),

            Forms\Components\TextInput::make('porcentaje_alcanzado')
                ->label('Porcentaje Alcanzado')
                ->numeric()
                ->suffix('%')
                ->disabled()
                ->dehydrated(false),

            Forms\Components\Textarea::make('observaciones')
                ->label('Observaciones')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('departamento.nombreEdificio')
                    ->label('Departamento')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mes')
                    ->label('Mes')
                    ->formatStateUsing(fn ($state) => [
                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
                        4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
                        10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                    ][$state])
                    ->sortable(),

                TextColumn::make('anio')
                    ->label('Año')
                    ->sortable(),

                TextColumn::make('valor_meta')
                    ->label('Meta')
                    ->money('usd')
                    ->sortable(),

                TextColumn::make('valor_actual')
                    ->label('Actual')
                    ->money('usd')
                    ->sortable(),

                TextColumn::make('porcentaje_alcanzado')
                    ->label('Progreso')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . '%')
                    ->alignment('center')
                    ->color(fn ($state) => match(true) {
                        floatval($state) >= 100 => 'success',
                        floatval($state) >= 75 => 'warning',
                        floatval($state) >= 50 => 'info',
                        default => 'danger',
                    }),

                BadgeColumn::make('estado')
                    ->colors([
                        'danger' => 'Pendiente',
                        'warning' => 'En Camino',
                        'info' => 'En Progreso',
                        'success' => 'Alcanzada',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('idDepartamento')
                    ->relationship('departamento', 'nombreEdificio')
                    ->label('Departamento'),

                Tables\Filters\SelectFilter::make('mes')
                    ->options([
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre',
                    ])
                    ->label('Mes'),

                Tables\Filters\SelectFilter::make('anio')
                    ->options(array_combine(
                        range(2020, date('Y') + 1),
                        range(2020, date('Y') + 1)
                    ))
                    ->label('Año'),
            ])
            ->defaultSort('created_at', 'desc')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMetasMensuales::route('/'),
            'create' => Pages\CreateMetaMensual::route('/create'),
            'edit' => Pages\EditMetaMensual::route('/{record}/edit'),
        ];
    }
}