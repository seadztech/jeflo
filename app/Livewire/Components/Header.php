<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    public $darkMode = false;
    public $showUserDropdown = false;
    public $sidebarCollapsed = false;
    public $mobileMenuOpen = false;
    public $getGreetingProperty;

    protected $listeners = ['closeDropdowns' => 'closeAllDropdowns'];

    public function mount()
    {
        // Initialize dark mode from session or cookie
        $this->darkMode = session()->get('dark_mode', 
            isset($_COOKIE['dark_mode']) ? $_COOKIE['dark_mode'] === 'true' : false
        );
        
        // Initialize sidebar state from localStorage
        $this->sidebarCollapsed = isset($_COOKIE['sidebar_collapsed']) 
            ? $_COOKIE['sidebar_collapsed'] === 'true' 
            : false;
           
    }

    public function toggleSidebar()
    {
        $this->sidebarCollapsed = !$this->sidebarCollapsed;
        $this->dispatch('sidebar-toggled', collapsed: $this->sidebarCollapsed);
        
        // Set cookie to persist across sessions
        setcookie('sidebar_collapsed', $this->sidebarCollapsed ? 'true' : 'false', [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'secure' => true,
            'samesite' => 'lax',
        ]);
    }

    public function toggleMobileMenu()
    {
        $this->mobileMenuOpen = !$this->mobileMenuOpen;
        $this->dispatch('mobile-menu-toggled', open: $this->mobileMenuOpen);
    }

    public function toggleUserDropdown()
    {
        $this->showUserDropdown = !$this->showUserDropdown;
    }

    public function closeAllDropdowns()
    {
        $this->showUserDropdown = false;
    }

    public function getGreetingProperty()
    {
        $hour = now()->hour;
        
        return match(true) {
            $hour < 12 => ['Good Morning', '🌅'],
            $hour < 17 => ['Good Afternoon', '☀️'],
            default => ['Good Evening', '🌙'],
        };
    }

    public function render()
    {
        return view('livewire.components.header');
    }
}