<?php

namespace App\Livewire\Items;

use Livewire\Component;
use Livewire\WithPagination;

class Items extends Component
{
    use WithPagination;

    public $title = 'Items Listing Page';
    public $itemsPerPage = 10;
    public $search = '';
    public $model = 'App\Models\Items';

    public function render()
    {
        return view('livewire.items.items');
    }
}
