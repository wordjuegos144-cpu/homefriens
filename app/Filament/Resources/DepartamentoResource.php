<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartamentoResource\Pages;
use App\Models\Departamento;
use App\Models\EmpresaAdministradora;
use App\Models\Propietario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class DepartamentoResource extends Resource
{
    protected static ?string $model = Departamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Empresas';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $commonServices = [
            'Guardia' => 'Guardia',
            'Piscina' => 'Piscina',
            'Parque' => 'Parque',
            'Estacionamiento' => 'Estacionamiento',
            'Wi-Fi' => 'Wi-Fi',
            'Aire acondicionado' => 'Aire acondicionado',
            'Televisión' => 'Televisión',
            'Limpieza' => 'Limpieza',
            'Ascensor' => 'Ascensor',
            'Calefacción' => 'Calefacción',
        ];

        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([

                        Forms\Components\Select::make('idEmpresaAdministradora')
                            ->label('Empresa Administradora')
                            ->options(fn (): array => EmpresaAdministradora::all()
                                ->mapWithKeys(fn ($m) => [$m->{$m->getKeyName()} => $m->nombre])
                                ->toArray())
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('idPropietario')
                            ->label('Propietario')
                            ->options(fn (): array => Propietario::all()
                                ->mapWithKeys(fn ($m) => [$m->{$m->getKeyName()} => $m->nombre])
                                ->toArray())
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Cuando se selecciona un propietario, rellenar el teléfono automáticamente
                                $telefono = $state ? Propietario::find($state)?->telefono : null;
                                $set('telefonoPropietario', $telefono);
                            }),

                        Forms\Components\TextInput::make('nombreEdificio')
                            ->label('Nombre del Edificio')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ingrese el nombre del edificio o condominio')
                            ->helperText('Nombre con el que se identifica el edificio')
                            ->suffixIcon('heroicon-o-building-office')
                            ->suffixIconColor('primary'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('direccion')
                                    ->id('direccion')
                                    ->label('Dirección')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Escriba la ubicación')
                                    ->helperText('Escriba la dirección completa')
                                    ->prefixIcon('heroicon-o-map-pin')
                                    ->prefixIconColor('primary'),
                                Forms\Components\View::make('filament.components.location-picker')->columnSpan(2)->reactive(),

                                // latitud/longitud fields removed
                            ]),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->maxLength(1000),

                        Forms\Components\TextInput::make('piso')
                            ->label('Piso')
                            ->numeric(),

                        Forms\Components\TextInput::make('numero')
                            ->label('Número')
                            ->numeric(),

                        Forms\Components\TextInput::make('capacidadNormal')
                            ->label('Capacidad normal')
                            ->numeric(),

                        Forms\Components\TextInput::make('capacidadExtra')
                            ->label('Capacidad extra')
                            ->numeric(),

                        Forms\Components\TextInput::make('telefonoPropietario')
                            ->label('Teléfono del propietario')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('camas')
                            ->label('Camas')
                            ->numeric(),

                        Forms\Components\TextInput::make('cuartos')
                            ->label('Cuartos')
                            ->numeric(),

                        Forms\Components\TextInput::make('banos')
                            ->label('Baños')
                            ->numeric(),

                        Forms\Components\Repeater::make('imagenes')
                            ->label('Imágenes')
                            ->schema([
                                Forms\Components\FileUpload::make('imagen')->image(),
                            ])
                            ->columnSpan('full'),

                        Forms\Components\CheckboxList::make('servicios_check')
                            ->label('Servicios (marque los que apliquen)')
                            ->options($commonServices)
                            ->default(fn ($record) => array_values(array_intersect(($record?->servicios ?? []) ?: [], array_keys($commonServices))))
                            ->reactive()
                            ->dehydrated(false),

                        Forms\Components\TagsInput::make('servicios_custom')
                            ->label('Agregar etiquetas personalizadas')
                            ->suggestions(array_values($commonServices))
                            ->default(fn ($record) => array_values(array_diff(($record?->servicios ?? []) ?: [], array_keys($commonServices))))
                            ->placeholder('Escribe y presiona Enter para agregar una etiqueta')
                            ->reactive()
                            ->dehydrated(false)
                            ->columnSpan('full'),

                        Forms\Components\Hidden::make('servicios')
                            ->default(fn ($record) => $record?->servicios ?? [])
                            ->dehydrateStateUsing(fn ($state, $get) => array_values(array_unique(array_filter(array_merge(
                                $get('servicios_check') ?? [],
                                $get('servicios_custom') ?? []
                            ))))),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('nombreEdificio')->label('Nombre'),
                Tables\Columns\TextColumn::make('empresaAdministradora.nombre')
                    ->label('Empresa administradora')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')->label('Dirección'),
                Tables\Columns\TextColumn::make('capacidadNormal')->label('Capacidad'),
                Tables\Columns\TextColumn::make('camas')->label('Camas'),
                Tables\Columns\TextColumn::make('cuartos')->label('Cuartos'),
                Tables\Columns\TextColumn::make('banos')->label('Baños'),
                Tables\Columns\TextColumn::make('telefonoPropietario')->label('Teléfono del propietario'),
                Tables\Columns\TagsColumn::make('servicios')->label('Servicios'),
            ])
            ->filters([])
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
        return [];
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
