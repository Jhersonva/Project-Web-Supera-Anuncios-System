@extends('layouts.app')

@section('title', $ad->title)

@section('content')

<style>
    .ad-container{
        border-radius: 16px;
        background: #fff;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.07);
    }
    .main-img{
        width: 100%;
        height: 320px;
        object-fit: contain;       /* ⬅ Cambiado */
        background: #f5f5f5;       /* Fondo neutro para imágenes pequeñas */
        border-radius: 12px;
    }

    .thumb-img{
        width: 85px;
        height: 85px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid transparent;
        cursor: pointer;
    }
    .thumb-img.active{
        border-color: #ff2d2d;
    }
    .info-title{
        font-size: 1.5rem;
        font-weight: 700;
    }
    .price-box{
        background: #ffecec;
        padding: 12px;
        border-radius: 10px;
        font-size: 1.2rem;
        font-weight: bold;
        color: #d61c1c;
        text-align: center;
    }
    .field-item{
        background: #fafafa;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #eee;
        margin-bottom: 10px;
    }
</style>

<div class="container mt-4 mb-5 ad-container">

    {{-- VOLVER --}}
    <a href="{{ url()->previous() }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    {{-- TÍTULO --}}
    <h3 class="info-title mb-2">{{ $ad->title }}</h3>

    <p class="text-muted">
        {{ $ad->category->name }} → {{ $ad->subcategory->name }}
    </p>

    {{-- GALERÍA DE IMÁGENES --}}
    <div class="mb-4">
        <img id="mainImage" class="main-img" 
             src="{{ asset($ad->mainImage->image ?? 'images/noimage.jpg') }}">

        <div class="d-flex gap-2 mt-3 flex-wrap">
            @foreach ($ad->images as $img)
                <img class="thumb-img"
                     src="{{ asset($img->image) }}"
                     onclick="document.getElementById('mainImage').src=this.src;">
            @endforeach
        </div>
    </div>

    <div class="row">
        {{-- INFORMACIÓN PRINCIPAL --}}
        <div class="col-md-8">

            {{-- PRECIO --}}
            <div class="price-box mb-4">
                S/. {{ number_format($ad->amount, 2) }}
            </div>

            {{-- DESCRIPCIÓN --}}
            <h5 class="fw-bold">Descripción</h5>
            <p class="text-secondary">{{ $ad->description }}</p>

            {{-- CAMPOS DINÁMICOS --}}
            @if ($ad->fields_values->count() > 0)
                <h5 class="fw-bold mt-4">Características</h5>

                @foreach ($ad->fields_values as $item)
                    <div class="field-item">
                        <strong>{{ $item->field->name }}:</strong>
                        <span>{{ $item->value }}</span>
                    </div>
                @endforeach
            @endif

            {{-- UBICACIÓN --}}
            @if ($ad->contact_location)
                <h5 class="fw-bold mt-4">Ubicación</h5>
                <p class="text-secondary">
                    {{ $ad->contact_location }}
                </p>
            @endif

            {{-- DETALLES --}}
            <h5 class="fw-bold mt-4">Detalles del anuncio</h5>
            <ul class="text-secondary">
                <li>Publicado: {{ $ad->created_at->diffForHumans() }}</li>
                <li>Expira: {{ $ad->expires_at }}</li>
                @if ($ad->urgent_publication)
                    <li class="text-danger fw-bold">Publicación urgente</li>
                @endif
            </ul>

        </div>

        {{-- COLUMNA DERECHA --}}
        <div class="col-md-4">

            <div class="p-3 border rounded-3">

                <h5 class="fw-bold text-center">Anunciante</h5>

                <p class="text-center">
                    <i class="fa-solid fa-user-circle fs-1"></i><br>
                    <strong>{{ $ad->user->full_name }}</strong>
                </p>

                <hr>
            </div>

            {{-- SI EL ANUNCIO PERTENECE AL USUARIO 
            @if (auth()->id() == $ad->user_id)
                <div class="mt-4 p-3 border rounded-3 bg-light">
                    <h6 class="fw-bold mb-3">Acciones</h6>

                    <a class="btn btn-warning w-100 mb-2"
                       href="{{ route('my-ads.editAd', $ad->id) }}">
                        <i class="fa-solid fa-pen"></i> Editar anuncio
                    </a>

                    <form method="POST" action="{{ route('my-ads.deleteAd', $ad->id) }}"
                          onsubmit="return confirm('¿Eliminar anuncio?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger w-100">
                            <i class="fa-solid fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            @endif--}}

        </div>
    </div>
</div>

@endsection
