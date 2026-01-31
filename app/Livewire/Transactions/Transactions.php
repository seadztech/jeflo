<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
     use WithPagination;

    public $title = 'Transactions Listing Page';
    public $itemsPerPage = 10;
    public $search = '';
    public $model = 'App\Models\Transactions';


    public function render()
    {
        return view('livewire.transactions.transactions');
    }
}
