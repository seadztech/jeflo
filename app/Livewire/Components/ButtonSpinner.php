<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ButtonSpinner extends Component
{
    public $target;
    public function mount($target){
        $this->target = $target;
    }
    public function render()
    {
        return view('livewire.components.button-spinner');
    }
}
