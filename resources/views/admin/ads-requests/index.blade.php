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
    width: 48%;
    padding: 6px 0;
    font-size: 0.9rem;
    border-radius: 8px;
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

<div class="container mt-5 pt-4 mb-5">

    <h4 class="fw-semibold mb-3 text-center">Solicitudes de Publicación</h4>

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
                <div class="d-flex justify-content-between align-items-start">
                    <span class="badge badge-cat">{{ $ad->category->name }}</span>
                    <span class="badge badge-subcat">{{ $ad->subcategory->name }}</span>
                </div>

                <p class="ad-title mt-2">{{ $ad->title }}</p>

                <p class="ad-meta">
                    <i class="fa-solid fa-user"></i> {{ $ad->user->full_name }} <br>
                    <i class="fa-solid fa-clock"></i> 
                    {{ $ad->created_at->format('d/m/Y H:i') }} <br>
                    <i class="fa-solid fa-tag"></i> 
                    <strong>S/. {{ number_format($ad->price, 2) }}</strong>
                </p>

                <div class="d-flex justify-content-between mt-2">
                    
                    <form action="{{ route('admin.ads-requests.approve', $ad->id) }}" 
                          method="POST" class="w-50 me-2">
                        @csrf
                        <button class="btn btn-success action-btn">
                            <i class="fa-solid fa-check"></i> Aprobar
                        </button>
                    </form>

                    <form action="{{ route('admin.ads-requests.reject', $ad->id) }}" 
                          method="POST" class="w-50">
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
