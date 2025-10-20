<?php

namespace App\Filament\Resources\HuespedResource\Pages;

use App\Filament\Resources\HuespedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHuespeds extends ListRecords
{
    protected static string $resource = HuespedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
