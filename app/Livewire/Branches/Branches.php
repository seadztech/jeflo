<?php

namespace App\Livewire\Branches;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Branch;

class Branches extends Component
{
    use WithPagination;

    public $title = 'Branch Listing Page';
    public $itemsPerPage = 10;
    public $search = '';
    public $model = 'App\Models\Branch';


    public function render()
    {
        return view('livewire.branches.branches');
    }
}
