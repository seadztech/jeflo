<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{

    use WithPagination;

    public $title = ' Customers Listing Page';
    public $itemsPerPage = 10;
    public $search = '';
    public $model = 'App\Models\Customer';

    public function render()
    {
        return view('livewire.customers.customers');
    }
}
