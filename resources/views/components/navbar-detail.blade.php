<!-- Navbar detalle anuncio -->
<nav class="navbar bg-white px-3 shadow-sm fixed-top d-flex justify-content-between align-items-center">

    {{-- IZQUIERDA: BOTÓN VOLVER --}}
    <a href="{{ url()->previous() ?? route('home') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    {{-- CENTRO: TÍTULO --}}
    <span class="fw-bold">Detalle del Anuncio</span>

    {{-- DERECHA: FECHA --}}
    <span class="text-muted small">
        {{ $ad->created_at->format('d/m/Y') }}
    </span>
</nav>
