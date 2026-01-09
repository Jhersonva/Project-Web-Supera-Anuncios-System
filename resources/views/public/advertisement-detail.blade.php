<!--views/public/advertisement-detail.blade-->
@extends('layouts.app')

@section('title', 'Detalle del anuncio')

@section('custom-navbar')
    @include('components.navbar-detail', ['ad' => $ad])
@endsection

@section('custom-bottom-nav')
    @include('components.navbar-bottom-detail', ['ad' => $ad])
@endsection

<link rel="stylesheet" href="{{ asset('css/advertisement-detail.css') }}">


@section('content')

<div class="container mt-4 pt-3">

    {{-- CARRUSEL --}}
    <div class="detalle-carousel-container">
        <div id="carouselAdImages" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                @if($ad->images && count($ad->images) > 0)
                    @foreach($ad->images as $index => $img)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}"
                            style="--bg-image: url('{{ asset($img->image) }}');">
                            <img src="{{ asset($img->image) }}" alt="Imagen del anuncio">
                        </div>
                    @endforeach
                @else
                    <div class="carousel-item active">
                        <img src="{{ asset('img/no-image.png') }}" class="d-block w-90 detalle-img">
                    </div>
                @endif

            </div>

            @if($ad->images && count($ad->images) > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselAdImages" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#carouselAdImages" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            @endif
        </div>
    </div>

    <br>
    
    {{-- TÍTULO --}}
    <h4 class="fw-bold">{{ $ad->title }}</h4>

    {{-- PRECIO --}}
    <p class="text-success fw-bold fs-5">
        @if($ad->amount_visible == 1)
            S/. {{ number_format($ad->amount, 2) }}
        @else
            S/. No especificado
        @endif
    </p>

    <div class="detalle-info-grid">

        <p><i class="fa-solid fa-location-dot text-danger"></i>
            {{ $ad->contact_location ?? 'Sin ubicación' }}
        </p>

        <p><i class="fa-solid fa-tag"></i>
            {{ $ad->category->name ?? 'Categoría' }} — {{ $ad->subcategory->name ?? '' }}
        </p>

        <p><i class="fa-solid fa-calendar"></i>
            {{ $ad->created_at->format('d/m/Y') }}
        </p>

        <p>
            <i class="fa-regular fa-clock"></i>
            {{ $ad->time_ago }}
        </p>
    </div>


    {{-- DESCRIPCIÓN --}}
    <h5 class="fw-bold mt-4">Descripción</h5>
    <p class="text-muted descripcion-texto">{{ $ad->description }}</p>


    {{-- CARACTERÍSTICAS DINÁMICAS --}}
    <h5 class="fw-bold">Características</h5>
    <div class="caracteristicas-grid my-2">

        @foreach($ad->fields_values as $valueField)
            <span class="badge bg-light text-dark border px-3 py-2">
                <strong>{{ $valueField->field->name }}:</strong> {{ $valueField->value }}
            </span>
        @endforeach

    </div>

    <hr>

</div>

<script src="{{ asset('js/auth-guard.js') }}"></script>

@if(!auth()->check())
<script>
document.addEventListener('DOMContentLoaded', function () {
    sessionStorage.setItem('redirect_after_login', "{{ request()->fullUrl() }}");
    requireLogin('view_ad');
});
</script>
@endif


@endsection
