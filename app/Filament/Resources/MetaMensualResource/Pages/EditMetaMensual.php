<?php

namespace App\Filament\Resources\MetaMensualResource\Pages;

use App\Filament\Resources\MetaMensualResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMetaMensual extends EditRecord
{
    protected static string $resource = MetaMensualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}