@extends('layouts.app')

@section('title', 'Contactar Anunciante')

<link rel="stylesheet" href="{{ asset('css/advertisement-detail.css') }}">

@section('content')

<div class="container mt-4 pt-3">

    <a href="{{ url()->previous() }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold text-center mb-3">Contactar al Anunciante</h4>

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


    <div class="ad-card shadow-sm p-4">
        <h5 class="fw-bold">{{ $ad->title }}</h5>
        <p class="text-muted mb-1">
            <strong>Categoría:</strong> {{ $ad->category->name }} <br>
            <strong>Subcategoría:</strong> {{ $ad->subcategory->name }}
        </p>
        <p>{{ $ad->description }}</p>
        <p class="text-success fw-bold">S/ {{ $ad->amount }}</p>

        <p class="small text-muted">
            <i class="fa-solid fa-user"></i> Publicado por: {{ $ad->user->full_name }}
        </p>

        <label for="message" class="form-label">Mensaje para el anunciante:</label>
        <form action="{{ route('chat.start', $ad->id) }}" method="POST">
            @csrf
            <textarea name="message" class="form-control" rows="4" required></textarea>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane me-2"></i> Enviar Mensaje
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
