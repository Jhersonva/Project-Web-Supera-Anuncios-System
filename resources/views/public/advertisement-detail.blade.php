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
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ asset($img->image) }}" class="d-block mx-auto detalle-img">
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
        S/. {{ number_format($ad->amount, 2) }}
    </p>

    {{-- UBICACIÓN --}}
    <p><i class="fa-solid fa-location-dot text-danger"></i> {{ $ad->contact_location ?? 'Sin ubicación' }}</p>

    {{-- CATEGORÍA --}}
    <p><i class="fa-solid fa-tag"></i> 
        {{ $ad->category->name ?? 'Categoría' }} — 
        {{ $ad->subcategory->name ?? '' }}
    </p>

    {{-- FECHA --}}
    <p><i class="fa-solid fa-calendar"></i> Publicado el: {{ $ad->created_at->format('d/m/Y') }}</p>

    {{-- DESCRIPCIÓN --}}
    <h5 class="fw-bold mt-4">Descripción</h5>
    <p class="text-muted">{{ $ad->description }}</p>

    {{-- CALIFICACIÓN VISUAL --}}
    <div class="my-3">
        <small class="fw-bold">CALIFICACIÓN DE LA PUBLICACIÓN</small><br>
        <span class="text-warning fs-5">★★★★★</span>
    </div>

    {{-- CARACTERÍSTICAS DINÁMICAS --}}
    <h5 class="fw-bold">Características</h5>
    <div class="d-flex flex-wrap gap-2 my-2">

        @foreach($ad->fields_values as $valueField)
            <span class="badge bg-light text-dark border px-3 py-2">
                <strong>{{ $valueField->field->name }}:</strong> {{ $valueField->value }}
            </span>
        @endforeach

    </div>

    <hr>

</div>

@endsection
