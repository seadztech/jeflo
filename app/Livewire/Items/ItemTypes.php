<?php

namespace App\Livewire\Items;

use Livewire\Component;

class ItemTypes extends Component
{
    public $title = 'Item Type Listng Page';
    public $itemsPerPage = 10;
    public $search = '';
    public $model = 'App\Models\ItemType';

    public function render()
    {
        return view('livewire.items.item-types');
    }
}
