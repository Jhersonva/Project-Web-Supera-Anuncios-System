<div class="bottom-nav bg-white shadow-lg p-2 d-flex justify-content-around 
            position-fixed w-100" 
     style="bottom:0; left:0;">

    <!-- INICIO -->
    <a href="{{ route('home') }}" 
       class="text-center {{ request()->routeIs('home') ? 'nav-item-active' : '' }}">
        <i class="fas fa-house"></i><br>Inicio
    </a>

    <!-- SOLICITUDES DE ANUNCIOS -->
    <a href="{{ route('admin.ads-history.index') }}" 
       class="text-center {{ request()->routeIs('admin.ads-history.index') ? 'nav-item-active' : '' }}">
        <i class="fa-solid fa-table-cells-large"></i><br>Hist. Anuncios
    </a>

    <!-- SOLICITUD DE RECARGAS -->
    <a href="{{ route('admin.reload-request.index') }}"
    class="text-center position-relative {{ request()->routeIs('admin.reload-request.index') ? 'nav-item-active' : '' }}">
        <i class="fa-solid fa-dollar-sign"></i><br>Soli. Recarga
        <span id="badge-recargas" 
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            0
        </span>
    </a>


    <!-- CONFIG -->
    <a href="{{ route('admin.config') }}"
    class="text-center {{ request()->routeIs('admin.config') ? 'nav-item-active' : '' }}">
        <i class="fa-solid fa-gear"></i><br>Config
    </a>

</div>
