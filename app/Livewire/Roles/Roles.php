<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Roles extends Component
{
    use WithPagination;

    public $title = 'Roles page';
    public $itemsPerPage = 10;
    public $search = '';
    public $model = 'Spatie\Permission\Models\Role';


    public function render()
    {
        return view('livewire.roles.roles');
    }
}
