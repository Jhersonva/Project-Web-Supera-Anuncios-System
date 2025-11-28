<div class="bottom-nav bg-white shadow-lg p-2 d-flex justify-content-around 
            position-fixed w-100" 
     style="bottom:0; left:0;">

    <!-- INICIO -->
    <a href="{{ route('home') }}" 
       class="text-center {{ request()->routeIs('home') ? 'nav-item-active' : '' }}">
        <i class="fas fa-house"></i><br>Inicio
    </a>

    <!-- SOLICITUDES DE ANUNCIOS -->
    <a href="{{ route('admin.ads-requests.index') }}" 
       class="text-center {{ request()->routeIs('admin.ads-requests.index') ? 'nav-item-active' : '' }}">
        <i class="fa-solid fa-table-cells-large"></i><br>Soli. Anuncios
    </a>

    <!-- PUBLICACIONES -->
    <a href="" 
       class="text-center {{ request()->routeIs('admin.publicaciones') ? 'nav-item-active' : '' }}">
        <i class="fa-solid fa-bullhorn"></i><br>Publicaciones
    </a>

    <!-- SOLICITUD DE RECARGAS -->
    <a href="{{ route('admin.reload-request.index') }}"
        class="text-center {{ request()->routeIs('admin.reload-request.index') ? 'nav-item-active' : '' }}">
        <i class="fa-solid fa-dollar-sign"></i><br>Soli. Recarga
    </a>

    <!-- USUARIOS 
    <a href=""
       class="text-center {{ request()->routeIs('admin.usuarios') ? 'nav-item-active' : '' }}">
        <i class="fa-solid fa-users"></i><br>Usuarios
    </a>-->

    <!-- CONFIG -->
    <a href="{{ route('admin.config') }}"
    class="text-center {{ request()->routeIs('admin.config') ? 'nav-item-active' : '' }}">
        <i class="fa-solid fa-gear"></i><br>Config
    </a>

</div>
