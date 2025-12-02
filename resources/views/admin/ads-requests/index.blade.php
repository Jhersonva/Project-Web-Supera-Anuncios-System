@extends('layouts.app')

@section('title', 'Solicitudes de Publicación')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

<style>
.selector-btn {
    padding: 10px;
    font-weight: 600;
    border-radius: 10px;
    border: 2px solid #dc3545;
    transition: .3s;
}
.selector-btn.active {
    background: #dc3545;
    color: white;
}
.selector-btn:not(.active) {
    background: white;
    color: #dc3545;
}
.ad-card {
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 14px;
    background: #fff;
    border: 1px solid #eee;
}
</style>


<div class="container mt-5 mb-5">

    <h4 class="fw-bold text-center mb-2">Solicitudes de Publicación</h4>
    <p class="text-secondary text-center mb-4">
        Revisa, aprueba o rechaza las solicitudes enviadas por los usuarios.
    </p>

    <!-- SELECTOR (IGUAL QUE PROMOS / RECARGA LIBRE) -->
    <div class="d-flex gap-2 mb-4">
        <button id="btnPendientes" class="selector-btn w-50 active">Solicitudes Pendientes</button>
        <button id="btnHistorial" class="selector-btn w-50">Historial</button>
    </div>

    <!-- SECCIÓN PENDIENTES -->
    <div id="seccionPendientes">
        @if($adsPendientes->isEmpty())
            <p class="text-center text-muted mt-4">No hay solicitudes pendientes.</p>
        @else
            @foreach($adsPendientes as $ad)
                <div class="ad-card shadow-sm">

                    @if($ad->images->isNotEmpty())
                        <div class="text-center mb-2">
                            <img src="{{ asset($ad->images->first()->image) }}" 
                                alt="Imagen del anuncio" 
                                style="width:350px; height:250px; object-fit:contain; border-radius:8px;">
                        </div>
                    @endif

                    <p class="fw-bold">{{ $ad->title }}</p>
                    <p class="small">{{ $ad->description }}</p>

                    <p class="text-muted small">
                        <i class="fa-solid fa-user"></i> {{ $ad->user->full_name }} <br>
                        <i class="fa-solid fa-clock"></i> {{ $ad->created_at->format('d/m/Y H:i') }} <br>
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <form action="{{ route('admin.ads-requests.approve', $ad->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-success">
                                <i class="fa-solid fa-check"></i> Aprobar
                            </button>
                        </form>

                        <form action="{{ route('admin.ads-requests.reject', $ad->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-danger">
                                <i class="fa-solid fa-xmark"></i> Rechazar
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- SECCIÓN HISTORIAL -->
    <div id="seccionHistorial" style="display:none;">
        @if($adsHistorial->isEmpty())
            <p class="text-center text-muted mt-4">No hay historial de solicitudes.</p>
        @else
            @foreach($adsHistorial as $ad)
                <div class="ad-card shadow-sm">

                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-primary">{{ $ad->category->name }}</span>
                        <span class="badge bg-info">{{ ucfirst($ad->status) }}</span>
                    </div>

                    <p class="fw-bold">{{ $ad->title }}</p>
                    <p class="small">{{ $ad->description }}</p>

                    <p class="text-muted small">
                        <i class="fa-solid fa-clock"></i> {{ $ad->created_at->format('d/m/Y H:i') }} <br>
                    </p>

                </div>
            @endforeach
        @endif
    </div>

</div>


<script>
// ===== SELECTOR EXACTO COMO EN PROMOCIONES =====

const btnPendientes = document.getElementById("btnPendientes");
const btnHistorial  = document.getElementById("btnHistorial");

const seccionPendientes = document.getElementById("seccionPendientes");
const seccionHistorial  = document.getElementById("seccionHistorial");

btnPendientes.addEventListener("click", () => {
    btnPendientes.classList.add("active");
    btnHistorial.classList.remove("active");

    seccionPendientes.style.display = "block";
    seccionHistorial.style.display  = "none";
});

btnHistorial.addEventListener("click", () => {
    btnHistorial.classList.add("active");
    btnPendientes.classList.remove("active");

    seccionPendientes.style.display = "none";
    seccionHistorial.style.display  = "block";
});
</script>

@endsection
