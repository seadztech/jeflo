<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Breadcrum extends Component
{
    public $title;
    public function mount($title){
        $this->title = $title;
    }
    public function render()
    {
        return view('livewire.components.breadcrum');
    }
}
