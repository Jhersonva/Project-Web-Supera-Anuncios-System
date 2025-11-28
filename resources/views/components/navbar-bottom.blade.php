<!-- views/components/navbar-bottom.blade.php -->
<div class="bottom-nav bg-white shadow-lg p-2 d-flex justify-content-around 
            position-fixed w-100" 
     style="bottom:0; left:0;">

    @auth
        @if(auth()->user()->role->name === 'admin')
            @include('components.navbar-bottom-admin')
        @else
            {{-- NAV PARA USUARIO NORMAL --}}
            <div class="bottom-nav bg-white shadow-lg p-2 d-flex justify-content-around 
                        position-fixed w-100" 
                style="bottom:0; left:0;">

                <!-- INICIO -->
                <a href="{{ route('home') }}" 
                    class="text-center {{ request()->routeIs('home') ? 'nav-item-active' : '' }}">
                    <i class="fas fa-house"></i><br>Inicio
                </a>

                <!-- MIS ANUNCIOS -->
                <a href="{{ route('my-ads.index') }}" 
                    class="text-center {{ request()->routeIs('my-ads.*') ? 'nav-item-active' : '' }}">
                    <i class="fa-solid fa-table-cells-large"></i><br>Mis Anuncios
                </a>

                <!-- RECARGAS -->
                <a href="{{ route('recharges.index') }}" 
                    class="text-center {{ request()->routeIs('recharges.*') ? 'nav-item-active' : '' }}">
                    <i class="fa-solid fa-dollar-sign"></i><br>Recargar
                </a>

            </div>
        @endif
    @endauth
</div>
