<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class LocationPicker extends TextInput
{
    protected string $view = 'forms.components.location-picker';

    public static function make(string $name): static
    {
        $static = parent::make($name);

        $static->extraAlpineAttributes([
            'x-on:map-location-selected' => 'state = $event.detail',
        ]);

        return $static;
    }
}