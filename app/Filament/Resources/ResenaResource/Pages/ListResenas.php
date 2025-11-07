<?php

namespace App\Filament\Resources\ResenaResource\Pages;

use App\Filament\Resources\ResenaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResenas extends ListRecords
{
    protected static string $resource = ResenaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
