<?php

namespace App\Filament\Resources\EmpresaAdministradoraResource\Pages;

use App\Filament\Resources\EmpresaAdministradoraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmpresaAdministradoras extends ListRecords
{
    protected static string $resource = EmpresaAdministradoraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
