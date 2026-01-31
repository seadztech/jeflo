<div>
    @foreach ($routes as $route)
        @if(Route::has($route['name']) && $this->canView($route['permission']))
            <li class="pc-item pc-hasmenu {{ $this->isActive($route['name']) ? 'active' : '' }}">
                <a wire:navigate href="{{ route($route['name']) }}" class="pc-link">
                    <span class="pc-micon">
                        <i class="fa fa-{{ $route['icon'] }}"></i>
                    </span>
                    <span class="text-lg pc-mtext">
                        {{ $route['label'] ?? ucfirst(str_replace('.', ' ', $route['name'])) }}
                    </span>
                </a>
            </li>
        @endif
    @endforeach
</div>
