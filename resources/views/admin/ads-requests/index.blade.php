@extends('layouts.app')

@section('title', 'Solicitudes de Publicación')

@section('content')

<link rel="stylesheet" href="{{ asset('css/admin-ads-request.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

<style>
/* ----------- MOBILE FIRST ----------- */
.ad-card {
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 14px;
    background: #fff;
    border: 1px solid #eee;
}

.ad-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
}

.ad-meta {
    font-size: 0.85rem;
    color: #666;
}

.badge-cat {
    background: #0059ff;
    font-size: 0.8rem;
}

.badge-subcat {
    background: #1bb38c;
    font-size: 0.8rem;
}

.action-btn {
    width: 150px;
    padding: 10px;
    font-size: 0.9rem;
    border-radius: 8px;
}

.custom-badge {
    display: flex;
    color: white;
    justify-content: center;
    align-items: center;
    min-width: 130px;     /* ancho uniforme */
    height: 38px;         /* altura uniforme */
    padding: 0 10px;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 8px;
    white-space: nowrap;  /* evita que el texto salte de línea */
}


/* ----------- WEB ----------- */
@media (min-width: 768px) {
    .ad-card {
        padding: 20px;
    }
    .ad-title {
        font-size: 1.1rem;
    }
}

</style>

<div class="container mt-5 mb-5">

    <h4 class="fw-bold mb-3 text-center">Solicitudes de Publicación</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($ads->isEmpty())
        <p class="text-center text-muted mt-4">No hay solicitudes pendientes.</p>
    @else
        @foreach($ads as $ad)
            <div class="ad-card shadow-sm">
                
                <div class="d-flex justify-content-between align-items-center gap-2 mt-2">

                    <span class="badge-cat custom-badge">
                        {{ $ad->category->name }}
                    </span>

                    @if($ad->urgent_publication == 1)
                        <span class="badge bg-danger custom-badge">
                            🚨 Urgente
                        </span>
                    @else
                        <span class="badge bg-secondary custom-badge">
                            ℹ️ Irrelevante
                        </span>
                    @endif

                    <span class="badge-subcat custom-badge">
                        {{ $ad->subcategory->name }}
                    </span>

                </div>


                <div class="ad-banner mt-2">
                    @php
                        $img = $ad->images->first()
                            ? asset($ad->images->first()->image)
                            : asset('images/no-image.png');
                    @endphp

                    <img src="{{ $img }}" 
                        alt="Imagen del anuncio"
                        style="width: 100%; height: 280px; object-fit: cover; border-radius: 10px;">
                </div>

                <p class="ad-title mt-2">{{ $ad->title }}</p>
                <p class="ad-desc">{{ $ad->description }}</p>

                <p class="ad-meta">
                    <i class="fa-solid fa-user"></i> {{ $ad->user->full_name }} <br>
                    <i class="fa-solid fa-clock"></i> 
                    {{ $ad->created_at->format('d/m/Y H:i') }} <br>
                    <i class="fa-solid fa-tag"></i> 
                    <strong>S/. {{ number_format($ad->amount, 2) }}</strong>
                </p>

                
                <div class="d-flex justify-content-center gap-3 mt-2">
                    <form action="{{ route('admin.ads-requests.approve', $ad->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success action-btn">
                            <i class="fa-solid fa-check"></i> Aprobar
                        </button>
                    </form>

                    <form action="{{ route('admin.ads-requests.reject', $ad->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-danger action-btn">
                            <i class="fa-solid fa-xmark"></i> Rechazar
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>

@endsection
