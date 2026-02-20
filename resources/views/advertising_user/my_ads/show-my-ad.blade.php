@extends('layouts.app')

@section('title', $ad->title)

@section('content')

@php
    $esExpirado = $ad->expires_at < now();
    $esPendiente = $ad->status === 'pendiente' && !$esExpirado;
    $esAprobado  = $ad->status === 'publicado' && !$esExpirado;
    $esRechazado = $ad->status === 'rechazado' && !$esExpirado;
@endphp

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
        object-fit: contain !important;
        object-position: center center !important;
        background: #f5f5f5;
        border-radius: 12px;
        max-height: none !important;
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
        background: #0fb839;
        padding: 12px;
        border-radius: 10px;
        font-size: 1.2rem;
        font-weight: bold;
        color: #ffffff;
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
    @auth
        @if(in_array(auth()->user()->role_id, [1,3]))
            <a href="{{ route('admin.ads-history.index') }}" class="text-dark">
        @else
            <a href="{{ route('my-ads.index') }}" class="text-dark">
        @endif
    @else
        <a href="{{ route('home') }}" class="text-dark">
    @endauth
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    {{-- TÍTULO --}}
    <h3 class="info-title mb-2">{{ $ad->title }}</h3>

    <p class="text-muted">
        {{ $ad->category->name }} → {{ $ad->subcategory->name }}
    </p>

    {{-- GALERÍA DE IMÁGENES --}}
    <div class="mb-4">
        <div class="main-image-wrapper">
            <img id="mainImage"
                class="main-img"
                src="{{ asset($ad->mainImage->image ?? 'images/noimage.jpg') }}">
        </div>

        <style>
            .main-image-wrapper{
    width: 100%;
    height: 320px;
    overflow: visible; /* CLAVE */
    background: #f5f5f5;
    border-radius: 12px;
}

        </style>


        <div class="d-flex gap-2 mt-3 flex-wrap">
            @foreach ($ad->images as $img)
                <img class="thumb-img"
                     src="{{ asset($img->image) }}"
                     onclick="document.getElementById('mainImage').src=this.src;">
            @endforeach
        </div>
    </div>

    @auth
        @if(in_array(auth()->user()->role_id, [1, 3]))

        <hr>

        {{-- PANEL ADMIN --}}

        <h4 class="fw-bold text-center mb-4">Panel de Control</h4>

        {{-- VERIFICACIÓN DEL ANUNCIO --}}
        @if($ad->verification_requested && !$ad->is_verified)

            <div class="verification-card mb-4">
                <div class="d-flex align-items-start gap-3">

                    <div class="verification-icon">
                        <img
                            src="{{ asset('assets/img/verified-icon/verified.png') }}"
                            alt="Anuncio verificado"
                            title="Anuncio verificado"
                            class="verification-img"
                        >
                    </div>

                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">Solicitud de verificación</h6>
                        <p class="text-muted mb-3 small">
                            El anunciante ha solicitado que este anuncio sea revisado y marcado como
                            <strong>ANUNCIO VERIFICADO</strong>.
                        </p>

                        <form action="{{ route('admin.ads.verify', $ad->id) }}" method="POST">
                            @csrf
                            <div class="text-end">
                                <button class="btn btn-success btn-sm verification-btn">
                                    <i class="fa-solid fa-certificate me-1"></i>
                                    Aprobar y verificar
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        @endif


        <div class="admin-box mb-4">

            {{-- ESTADO DEL ANUNCIO --}}
            @php
                $esExpirado = $ad->expires_at < now();
                $esPendiente = $ad->status === 'pendiente' && !$esExpirado;
                $esAprobado  = $ad->status === 'publicado' && !$esExpirado;
                $esRechazado = $ad->status === 'rechazado' && !$esExpirado;
            @endphp

            <h6 class="fw-bold mb-2">Estado actual</h6>

            @if($esExpirado)
                <span class="status-badge bg-secondary text-white">
                    <i class="fa-solid fa-clock me-1"></i> Expirado
                </span>

            @elseif($esPendiente)
                <span class="status-badge bg-warning text-dark">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> Pendiente de aprobación
                </span>

            @elseif($esAprobado)
                <span class="status-badge bg-success text-white">
                    <i class="fa-solid fa-check me-1"></i> Publicado
                </span>

            @elseif($esRechazado)
                <span class="status-badge bg-danger text-white">
                    <i class="fa-solid fa-xmark me-1"></i> Rechazado
                </span>
            @endif

            {{-- BOTONES DE WHATSAPP --}}
            <div class="mt-4">
                <h6 class="fw-bold mb-2">Enviar mensaje al anunciante</h6>

                <div id="waBox" class="mt-3" style="display:none;">
                    <label class="fw-bold">Mensaje a enviar:</label>
                    <textarea id="waMsg" class="form-control" rows="4"></textarea>

                    <button onclick="sendWA()" class="btn btn-success mt-2 w-100">
                        <i class="fa-brands fa-whatsapp"></i> Enviar por WhatsApp
                    </button>
                </div>

                @if($esPendiente)
                <button class="btn btn-warning wa-btn w-100"
                        onclick="loadMessage('{{ route('admin.ads.notify', [$ad->id, 'pendiente']) }}')">
                    <i class="fa-brands fa-whatsapp"></i> Notificar: Pendiente
                </button>
                @endif

                @if($esAprobado)
                <button class="btn btn-success text-white wa-btn w-100 mt-2"
                        onclick="loadMessage('{{ route('admin.ads.notify', [$ad->id, 'publicado']) }}')">
                    <i class="fa-brands fa-whatsapp"></i> Notificar: Publicado
                </button>
                @endif

                @if($esRechazado)
                <button class="btn btn-danger text-white wa-btn w-100 mt-2"
                        onclick="loadMessage('{{ route('admin.ads.notify', [$ad->id, 'rechazado']) }}')">
                    <i class="fa-brands fa-whatsapp"></i> Notificar: Rechazado
                </button>
                @endif

                @if($esExpirado)
                <button class="btn btn-secondary text-white wa-btn w-100 mt-2"
                        onclick="loadMessage('{{ route('admin.ads.notify', [$ad->id, 'expirado']) }}')">
                    <i class="fa-brands fa-whatsapp"></i> Notificar: Expirado
                </button>
                @endif

            </div>

            {{-- BOTONES DE CAMBIO DE ESTADO --}}
            <div class="mt-4">
                <h6 class="fw-bold mb-2">Acciones administrativas</h6>

                @if($esPendiente)
                    <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success action-btn w-100">
                            <i class="fa-solid fa-check"></i> Aprobar anuncio
                        </button>
                    </form>

                    <form action="{{ route('admin.ads.reject', $ad->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button class="btn btn-danger action-btn w-100">
                            <i class="fa-solid fa-xmark"></i> Rechazar anuncio
                        </button>
                    </form>
                @endif

                @if($esAprobado)
                    <form action="{{ route('admin.ads.reject', $ad->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button class="btn btn-danger action-btn w-100">
                            <i class="fa-solid fa-xmark"></i> Rechazar anuncio
                        </button>
                    </form>
                @endif

                @if($esRechazado)
                    <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button class="btn btn-success action-btn w-100">
                            <i class="fa-solid fa-check"></i> Aprobar anuncio
                        </button>
                    </form>
                @endif

                @if($esExpirado)
                    <p class="text-muted mt-3 text-center">
                        <i class="fa-solid fa-clock"></i> Este anuncio expiró automáticamente.
                    </p>
                @endif
            </div>
        </div>
        @endif
        @endauth

    <div class="row">
        {{-- INFORMACIÓN PRINCIPAL --}}
        <div class="col-md-8">

            @if($esRechazado)
                <div class="alert alert-danger mt-3">
                    <strong><i class="fa-solid fa-circle-info me-1"></i> Anuncio rechazado</strong>
                    <p class="mb-0 mt-1">
                        El anuncio fue rechazado por el administrador.<br>
                        <strong>El monto pagado fue devuelto automáticamente a tu monedero virtual.</strong>
                    </p>
                </div>
            @endif


            {{-- PRECIO --}}
            @php
                $currencySymbol = match($ad->amount_currency) {
                    'USD' => '$',
                    'PEN' => 'S/.',
                    default => '',
                };
            @endphp

            <div class="price-box mb-4">
                Monto / Precio / Sueldo:<br>

                @if($ad->amount_visible == 1)
                    {{ $currencySymbol }} {{ number_format($ad->amount, 0) }}
                @elseif(!empty($ad->amount_text))
                    {{ $currencySymbol }} {{ $ad->amount_text }}
                @else
                    No especificado
                @endif
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
                <li>Publicado: {{ $ad->created_at->locale('es')->diffForHumans() }}</li>
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

                <div class="d-flex justify-content-center">
                    <div class="position-relative d-inline-block">


                    <img
                        src="{{ $ad->user->profile_image
                            ? asset($ad->user->profile_image)
                            : asset('assets/img/profile-image/default-user.png') }}"
                        class="rounded-circle border border-2 border-danger"
                        style="width:110px; height:110px; object-fit:cover;"
                    >

                    {{-- INSIGNIA VERIFICADO --}}
                    @if($ad->user->is_verified)
                        <img
                            src="{{ asset('assets/img/verified-icon/verified.png') }}"
                            alt="Usuario verificado"
                            title="Usuario verificado"
                            class="position-absolute"
                            style="
                                width:52px;
                                height:52px;
                                top:0;
                                right:0;
                                transform: translate(25%, -25%);
                            "
                        >
                    @endif
                    </div>
                </div>

                <p class="mt-2 fw-bold text-center">
                    {{ $ad->user->full_name }}
                </p>

                <hr>
            </div>

        </div>
    </div>
</div>

<script>
let waPhone = "";

function loadMessage(url) {
    fetch(url)
        .then(res => res.json())
        .then(data => {
            waPhone = data.phone;
            document.getElementById("waMsg").value = data.text;
            document.getElementById("waBox").style.display = "block";
        });
}

function sendWA() {
    let msg = document.getElementById("waMsg").value;
    let link = "https://wa.me/51" + waPhone + "?text=" + encodeURIComponent(msg);
    window.open(link, "_blank");
}
</script>

<style>

.verification-card{
    background: linear-gradient(135deg, #f0fff4, #ffffff);
    border: 1px solid #d1fae5;
    border-radius: 14px;
    padding: 18px;
}

.verification-icon{
    width: 76px;
    height: 76px;
    background: #e6f9ef; 
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.verification-img{
    width: 70px;      
    height: 70px;
    object-fit: contain;
}

.verification-card:hover .verification-icon{
    box-shadow: 0 0 0 4px rgba(34,197,94,.15);
    transition: .2s ease;
}

.verification-btn{
    padding: 6px 14px;
    font-weight: 600;
    border-radius: 8px;
    font-size: .9rem;
    width: fit-content;
}

.admin-box{
    background: #f9fafb;
    padding: 20px;
    border-radius: 14px;
    border: 1px solid #ececec;
}
.status-badge{
    font-size: .95rem;
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 600;
}
.wa-btn{
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    padding: 10px;
    border-radius: 10px;
}
.action-btn{
    padding: 11px;
    border-radius: 10px;
    font-weight: 600;
}
</style>

@endsection
