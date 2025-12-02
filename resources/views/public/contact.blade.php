@extends('layouts.app')

@section('title', 'Contactar Anunciante')

@section('content')

<div class="container mt-5 mb-5">

    <h4 class="fw-bold text-center mb-3">Contactar al Anunciante</h4>

    <div class="ad-card shadow-sm p-4">

        @if($ad->images->isNotEmpty())
            <div class="text-center mb-3">
                <img src="{{ asset($ad->images->first()->image) }}" 
                     alt="Imagen del anuncio" 
                     style="width:450px; height:350px; object-fit:contain; border-radius:8px;">
            </div>
        @endif

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

        <div class="mt-3">
            <label for="message" class="form-label">Mensaje para el anunciante:</label>
            <textarea id="message" class="form-control" rows="4" placeholder="Escribe tu mensaje aquí..."></textarea>
        </div>

        <div class="text-center mt-3">
            <button class="btn btn-primary">
                <i class="fa-solid fa-paper-plane me-2"></i> Enviar Mensaje
            </button>
        </div>

    </div>
</div>

@endsection
