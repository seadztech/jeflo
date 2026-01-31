<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
class Users extends Component
{
     use WithPagination;

    public $title = 'System User Listings page';
    public $itemsPerPage = 10;
    public $search = '';
    public $model = 'App\Models\User';

    public function render()
    {
        return view('livewire.users.users');
    }
}
