<?php

// Placeholder page: original 'Calendario' Page has been deprecated in favor of the
// `CalendarioResource`. This file remains only as a non-navigable placeholder to
// avoid accidental route or class-not-found errors during development.

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Calendario extends Page
{
    // NOTE: navigation properties removed so Filament will not display this Page
    // in the admin navigation. The Resource `CalendarioResource` provides the
    // CRUD and navigation entry instead.

    // Provide a simple placeholder view name (keeps class valid if referenced).
    protected static string $view = 'filament.pages.calendario_removed';

    public array $events = [];

    public function mount(): void
    {
        // intentionally empty
        $this->events = [];
    }
}

