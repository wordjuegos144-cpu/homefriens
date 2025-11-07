<?php
namespace App\Filament\Resources;

use App\Exports\ReservasPropietarioExport;
use Maatwebsite\Excel\Facades\Excel;

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
                Forms\Components\Select::make('idEmpresaAdministradora')
                    ->relationship('empresaAdministradora', 'nombre')
                    ->required(),
                Forms\Components\Select::make('idPropietario')
                    ->relationship('propietario', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $propietario = \App\Models\Propietario::find($state);
                            if ($propietario) {
                                $set('telefonoPropietario', $propietario->telefono);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('nombreEdificio')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('direccion')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-map-pin'),
                Forms\Components\Textarea::make('descripcion')
                    ->maxLength(1000)
                    ->rows(5),
                Forms\Components\TextInput::make('piso')
                    ->numeric(),
                Forms\Components\TextInput::make('numero')
                    ->maxLength(255),
                Forms\Components\TextInput::make('capacidadNormal')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('capacidadExtra')
                    ->numeric(),
                Forms\Components\TextInput::make('telefonoPropietario')
                    ->tel()
                    ->maxLength(255)
                    ->label('Teléfono de contacto')
                    ->helperText('Este número se prellenará con el teléfono del propietario, pero puedes modificarlo si es necesario')
                    ->prefixIcon('heroicon-o-phone'),
                Forms\Components\TextInput::make('cuartos')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('baños')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('camas')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('servicios')
                    ->multiple()
                    ->searchable()
                    ->options([
                        'WiFi' => 'WiFi',
                        'Aire Acondicionado' => 'Aire Acondicionado',
                        'Calefacción' => 'Calefacción',
                        'TV Cable' => 'TV Cable',
                        'Netflix' => 'Netflix',
                        'Cocina Equipada' => 'Cocina Equipada',
                        'Lavadora' => 'Lavadora',
                        'Secadora' => 'Secadora',
                        'Estacionamiento' => 'Estacionamiento',
                        'Piscina' => 'Piscina',
                        'Gimnasio' => 'Gimnasio',
                        'Seguridad 24/7' => 'Seguridad 24/7',
                        'Terraza' => 'Terraza',
                        'Balcón' => 'Balcón',
                        'Vista al Mar' => 'Vista al Mar',
                        'Acceso a Playa' => 'Acceso a Playa',
                        'Área de BBQ' => 'Área de BBQ',
                        'Sala de Juegos' => 'Sala de Juegos',
                        'Área Infantil' => 'Área Infantil',
                        'Pet Friendly' => 'Pet Friendly'
                    ])
                    ->createOptionForm([
                        Forms\Components\TextInput::make('value')
                            ->label('Nuevo Servicio')
                            ->required()
                            ->maxLength(255)
                    ])
                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                        return $action
                            ->label('Agregar Nuevo Servicio')
                            ->modalHeading('Crear Nuevo Servicio')
                            ->modalButton('Crear Servicio');
                    })
                    ->label('Servicios Disponibles')
                    ->helperText('Selecciona los servicios disponibles o crea uno nuevo')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('imagenes')
                    ->multiple()
                    ->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::getTableColumns())
            ->filters(static::getTableFilters())
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Exportar Reporte')
                    ->icon('heroicon-o-document-arrow-down')
                    ->form([
                        Forms\Components\DatePicker::make('fechaInicio')
                            ->label('Fecha Inicio'),
                        Forms\Components\DatePicker::make('fechaFin')
                            ->label('Fecha Fin'),
                        Forms\Components\Select::make('idDepartamento')
                            ->label('Departamento')
                            ->relationship('departamento', 'nombreEdificio')
                            ->searchable(),
                        Forms\Components\Select::make('formato')
                            ->label('Formato de Exportación')
                            ->options([
                                'csv' => 'CSV',
                                'xlsx' => 'Excel',
                                'pdf' => 'PDF',
                            ])
                            ->default('xlsx')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        return Excel::download(
                            new ReservasPropietarioExport(
                                $data['fechaInicio'] ?? null,
                                $data['fechaFin'] ?? null,
                                $data['idDepartamento'] ?? null
                            ),
                            'reporte-reservas.' . $data['formato']
                        );
                    })
            ]);
    }

    protected static function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
            Tables\Columns\TextColumn::make('empresaAdministradora.nombre')->label('Empresa')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('propietario.nombre')->label('Propietario')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('nombreEdificio')->label('Nombre')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('direccion')->label('Dirección')->limit(40),
            Tables\Columns\TextColumn::make('capacidadNormal')->label('Capacidad')->sortable(),
            Tables\Columns\TextColumn::make('capacidadExtra')->label('Extra')->sortable(),
            Tables\Columns\TextColumn::make('cuartos')->label('Cuartos'),
            Tables\Columns\TextColumn::make('banos')->label('Baños'),
            Tables\Columns\TextColumn::make('camas')->label('Camas'),
        ];
    }

    protected static function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('idEmpresaAdministradora')
                ->relationship('empresaAdministradora', 'nombre')
                ->label('Empresa'),

            Tables\Filters\SelectFilter::make('idPropietario')
                ->relationship('propietario', 'nombre')
                ->label('Propietario'),

            Tables\Filters\Filter::make('servicios')->form([
                \Filament\Forms\Components\Select::make('servicio')
                    ->label('Servicio')
                    ->options([
                        'WiFi' => 'WiFi',
                        'Aire Acondicionado' => 'Aire Acondicionado',
                        'Piscina' => 'Piscina',
                        'Estacionamiento' => 'Estacionamiento',
                    ])
            ])->query(function ($query, $data) {
                if (!empty($data['servicio'])) {
                    $query->whereJsonContains('servicios', $data['servicio']);
                }
            }),
        ];
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
