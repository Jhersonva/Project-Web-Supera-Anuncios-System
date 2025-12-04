@extends('layouts.app')

@section('title', 'Estadísticas del anuncio')

@section('content')
<div class="container mt-4 mb-5">

    <a href="{{ url()->previous() }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h3 class="fw-bold text-center mb-4">Estadísticas (Simulación)</h3>

    <div class="card shadow-sm border-0 p-4 text-center">
        <h5 class="fw-bold mb-3">{{ $ad->title }}</h5>

        <p><strong>Vistas:</strong> {{ $stats['views'] }}</p>
        <p><strong>Contactos:</strong> {{ $stats['contacts'] }}</p>
        <p><strong>Favoritos:</strong> {{ $stats['favorites'] }}</p>
    </div>

</div>
@endsection
