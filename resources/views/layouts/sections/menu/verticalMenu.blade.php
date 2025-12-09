@php
use Illuminate\Support\Facades\Route;
@endphp
<aside id="layout-menu" class="layout-menu menu-vertical menu">

    <!-- ! Hide app brand if navbar-full -->
    <div class="d-flex justify-content-start align-items-left py-1 px-5 shadow-sm p-3 mb-5 bg-body rounded">
        <a href="{{ url('/') }}">
            <img src="{{ asset('assets/img/logo/logo-light.png') }}" style="width: 3rem;" /> 
        </a>
        <h2 class="fw-bold fs-4">Smart PJU</h2>

        {{-- <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="menu-toggle-icon d-xl-inline-block align-middle"></i>
        </a> --}}
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @foreach ($menuData[0]->menu as $menu)

        {{-- adding active and open class if child is active --}}

        {{-- menu headers --}}
        @if (isset($menu->menuHeader))
        <li class="menu-header mt-7">
            <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
        </li>
        @else

        {{-- active menu method --}}
        @php
            $activeClass = null;
            $currentPath = request()->path();
            
            if (isset($menu->active_on) && is_array($menu->active_on)) {
                foreach ($menu->active_on as $path) {
                    if (str_starts_with($currentPath, trim($path, '/'))) {
                        $activeClass = 'active';
                        break;
                    }
                }
            } else {
                
                if (str_starts_with($currentPath, trim($menu->slug, '/'))) {
                    $activeClass = 'active';
                }
            }

            if (isset($menu->submenu) && $activeClass) {
                $activeClass .= ' open';
            }
        @endphp


        {{-- main menu --}}
        <li class="menu-item {{$activeClass}}">
            <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}" class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
                @isset($menu->icon)
                <i class="mx-2 tf-icons mdi {{ $menu->icon }}"></i>
                @endisset
                <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                @isset($menu->badge)
                <div class="badge rounded-pill bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                @endisset
            </a>

            {{-- submenu --}}
            @isset($menu->submenu)
            @include('layouts.sections.menu.submenu',['menu' => $menu->submenu])
            @endisset
        </li>
        @endif
        @endforeach
    </ul>

</aside>
