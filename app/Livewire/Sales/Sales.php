<?php

namespace App\Livewire\Sales;

use Livewire\Component;

class Sales extends Component
{
    
    public $title = 'Sales Listings Page';
    public $itemsPerPage = 10;
    public $search = '';
    public $model = 'App\Models\Sale';

    public function render()
    {
        return view('livewire.sales.sales');
    }
}
