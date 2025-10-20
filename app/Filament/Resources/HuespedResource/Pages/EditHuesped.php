<?php

namespace App\Filament\Resources\HuespedResource\Pages;

use App\Filament\Resources\HuespedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHuesped extends EditRecord
{
    protected static string $resource = HuespedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
