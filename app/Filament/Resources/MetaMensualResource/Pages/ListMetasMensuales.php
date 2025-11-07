<?php

namespace App\Filament\Resources\MetaMensualResource\Pages;

use App\Filament\Resources\MetaMensualResource;
use App\Filament\Widgets\MetasGastosChart;
use App\Filament\Widgets\MetasGastosOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Exports\MetasMensualesExport;
use Maatwebsite\Excel\Facades\Excel;

class ListMetasMensuales extends ListRecords
{
    protected static string $resource = MetaMensualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Exportar Reporte')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    return Excel::download(
                        new MetasMensualesExport(),
                        'reporte-metas-' . now()->format('Y-m-d') . '.xlsx'
                    );
                }),
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MetasGastosOverview::class,
            MetasGastosChart::class,
        ];
    }
}