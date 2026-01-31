<?php

namespace App\Livewire\Components;

use App\Livewire\Actions\Logout;
use Livewire\Component;

class HeaderLogout extends Component
{
     public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.components.header-logout');
    }
}
