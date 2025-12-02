@extends('layouts.app')

@section('title', 'Mis Anuncios')

@section('content')

<link rel="stylesheet" href="{{ asset('css/my-ads.css') }}">

<div class="container mt-5 mb-5 pb-5">

    <!-- BOTÓN CREAR ANUNCIO -->
    <button onclick="location.href='{{ route('my-ads.createAd') }}'" 
        class="btn btn-danger shadow btn-float d-flex align-items-center gap-2 px-3 py-2 rounded-pill">
        <i class="fa-solid fa-plus"></i>
        <span>Crear Anuncio</span>
    </button>

    <!-- TÍTULO -->
    <div class="text-center mb-4 mt-4">
        <h3 class="fw-bold">Mis Anuncios</h3>
        <p class="text-secondary">Aquí puedes ver, editar y gestionar todos tus anuncios publicados.</p>
    </div>

    <!-- GRID DE ANUNCIOS -->
    <div class="ad-grid">

        @forelse ($ads as $ad)

        <div class="ad-card shadow-sm">
            <div class="ad-img-container">
                <img src="{{ $ad->mainImage ? asset($ad->mainImage->image) : asset('assets/default-ad.png') }}"
                     alt="Imagen del anuncio" class="ad-img">
                <span class="status-tag status-{{ strtolower($ad->status) }}">
                    {{ ucfirst($ad->status) }}
                </span>
            </div>

            <div class="p-3 d-flex flex-column justify-content-between">

                <div>
                    <h6 class="fw-bold m-0 text-truncate">{{ $ad->title }}</h6>
                    <p class="m-0 text-secondary small text-truncate">{{ $ad->location }}</p>
                </div>

                <!-- Acciones -->
                <div class="ad-actions mt-3 d-flex justify-content-between">
                    <a href="#" class="ad-btn edit">
                        <i class="fa-solid fa-pen"></i> Editar
                    </a>

                    <a href="#" class="ad-btn view">
                        <i class="fa-solid fa-eye"></i> Ver
                    </a>

                    <a href="#" class="ad-icon stats">
                        <i class="fa-solid fa-chart-column"></i>
                    </a>
                </div>

            </div>
        </div>

        @empty

        <div class="text-center mt-5">
            <img src="{{ asset('assets/empty.png') }}" width="130" class="mb-3">
            <h6 class="fw-bold">Aún no tienes anuncios</h6>
            <p class="text-secondary small">Crea tu primer anuncio y empieza a publicar tus ofertas.</p>
        </div>

        @endforelse

    </div>

</div>

@endsection
