<?php

namespace App\Livewire;

use Livewire\Component;

class FormularioDireccion extends Component
{
     public $data = [
        'direccion' => '',
        'showMap' => false,
    ];

    public function render()
    {
        return view('livewire.formulario-direccion');
    }
}
