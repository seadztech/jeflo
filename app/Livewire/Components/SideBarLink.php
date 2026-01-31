<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class SideBarLink extends Component
{
    public $routes = [];

    public function mount() {
        // Each route now has an optional 'permission' field
        $this->routes = [
            ['name'=> 'dashboard', 'icon' => 'house', 'label' => 'Dashboard', 'permission' => null],
            ['name'=> 'roles', 'icon' => 'shield-halved', 'label' => 'Roles', 'permission' => 'view roles'],
            ['name'=> 'branches', 'icon' => 'building', 'label' => 'Branches', 'permission' => 'view branches'],
            ['name'=> 'users', 'icon' => 'users', 'label' => 'Users', 'permission' => 'view users'],
            ['name'=> 'itemTypes', 'icon' => 'list', 'label' => 'Item Types', 'permission' => 'view item types'],
            ['name'=> 'items', 'icon' => 'list', 'label' => 'Items', 'permission' => 'view items'],
            ['name'=> 'sales', 'icon' => 'square-poll-vertical', 'label' => 'Sales', 'permission' => 'view sales'],
            ['name'=> 'transactions', 'icon' => 'money-bill', 'label' => 'Transactions', 'permission' => 'view transactions'],
            ['name'=> 'reports', 'icon' => 'chart-simple', 'label' => 'Reports', 'permission' => 'view reports'],
            ['name'=> 'pos', 'icon' => 'calculator', 'label' => 'POS', 'permission' => 'access pos'],
        ];
    }

    // check if current route is active
    public function isActive($routeName)
    {
        return request()->routeIs($routeName) || request()->routeIs($routeName . '.*');
    }

    // check if user can view the link
    public function canView($permission = null)
    {
        // If no permission is required, show to all authenticated users
        if (!$permission) return true;

        return Auth::user()?->can($permission);
    }

    public function render()
    {
        return view('livewire.components.side-bar-link');
    }
}
