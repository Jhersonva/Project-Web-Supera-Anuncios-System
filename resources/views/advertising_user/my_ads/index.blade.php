@extends('layouts.app')

@section('title', 'Mis Anuncios')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container mt-5 mb-5">

    <h4 class="fw-bold text-center mb-2">Mis Anuncios</h4>
    <p class="text-muted text-center mb-4">
        Gestión completa de tus anuncios publicados, pendientes y expirados.
    </p>

    {{-- FILTROS --}}
    <form method="GET" class="row mb-4 g-2 justify-content-center">

        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}"
                class="form-control" placeholder="Buscar por título, ubicación...">
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Estado --</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Borradores</option>
                <option value="publicado" {{ request('status') == 'publicado' ? 'selected' : '' }}>Publicado</option>
                <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="expirado"  {{ request('status') == 'expirado'  ? 'selected' : '' }}>Expirado</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">
                <i class="fa-solid fa-magnifying-glass"></i> Filtrar
            </button>
        </div>
    </form>

    {{-- TARJETA PRINCIPAL --}}
    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

        {{-- ICONO Y TÍTULO --}}
        <div class="d-flex align-items-center mb-4">
            <div class="bg-danger text-white p-3 rounded-circle me-3"
                style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-rectangle-ad fa-lg"></i>
            </div>

            <div>
                <h5 class="fw-bold m-0">Listado de Mis Anuncios</h5>
                <small class="text-muted">Administración completa</small>
            </div>
        </div>

        {{-- ========================= --}}
        {{--   ESCRITORIO (TABLA) --}}
        {{-- ========================= --}}
        <div class="table-responsive desktop-table">
            <table class="table table-hover align-middle">
                
                <thead class="table-light">
                    
                    <tr>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($ads as $ad)
                        <tr>
                            <td width="80">
                                @php
                                    $image = $ad->images->first();
                                    $crop = $image?->crop_data;

                                    $thumbSize = 70;

                                    if ($crop && isset($crop['width'])) {
                                        $scale = $thumbSize / $crop['width'];
                                    } else {
                                        $scale = 1;
                                    }
                                @endphp

                                @if($image)
                                    <div class="img-crop-box">
                                        <img
                                            src="{{ asset($image->image) }}"
                                            style="
                                                transform:
                                                    scale({{ $scale }})
                                                    translate(
                                                        -{{ $crop['x'] ?? 0 }}px,
                                                        -{{ $crop['y'] ?? 0 }}px
                                                    );
                                            "
                                        >
                                    </div>
                                @else
                                    <div class="img-crop-box">
                                        <img src="{{ asset('assets/img/not-found-image/failed-image.jpg') }}">
                                    </div>
                                @endif
                            </td>

                            <td class="fw-semibold text-truncate" style="max-width:220px;">
                                {{ $ad->title }}
                            </td>

                            <td>
                                {{ $ad->category->name }} > {{ $ad->subcategory->name }}
                            </td>

                            {{-- Estado --}}
                            <td>
                                @if($ad->expires_at && $ad->expires_at < now())
                                    <span class="badge bg-secondary">Expirado</span>

                                @elseif($ad->status === 'draft')
                                    <span class="badge bg-dark">
                                        Borrador
                                    </span>

                                @elseif($ad->status === 'pendiente')
                                    <span class="badge bg-warning text-dark">Pendiente</span>

                                @elseif($ad->status === 'rechazado')
                                    <span class="badge bg-danger">Rechazado</span>

                                @elseif($ad->status === 'publicado')
                                    <span class="badge bg-success">Publicado</span>

                                @else
                                    <span class="badge bg-secondary">Desconocido</span>
                                @endif
                            </td>

                            <td>{{ $ad->created_at->format('d/m/Y') }}</td>

                            {{-- ACCIONES --}}
                            <td>
                                <div class="d-flex gap-2">

                                    {{-- CONTINUAR BORRADOR --}}
                                    @if($ad->status === 'draft')
                                        <a href="{{ route('my-ads.editDraft', $ad->id) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Continuar edición">
                                            <i class="fa-solid fa-play"></i>
                                        </a>
                                    @endif

                                    @if($ad->published)
                                        <button type="button"
                                            class="btn btn-sm btn-outline-info"
                                            onclick="confirmDeactivate({{ $ad->id }})"
                                            title="Dar de baja">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>

                                        <form id="deactivateForm-{{ $ad->id }}"
                                            action="{{ route('my-ads.deactivate', $ad->id) }}"
                                            method="POST" style="display:none;">
                                            @csrf
                                        </form>
                                    @endif

                                    @if($ad->receipt_type === 'nota_venta' && $ad->receipt_file)
                                        <a href="{{ asset($ad->receipt_file) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('my-ads.show', $ad->id) }}"
                                        class="btn btn-sm btn-outline-success">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    @if(
                                        $ad->status !== 'draft' &&
                                        $ad->status !== 'rechazado'
                                    )
                                        <a href="{{ route('my-ads.editAd', $ad->id) }}"
                                        class="btn btn-sm btn-outline-warning"
                                        title="Editar anuncio">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endif

                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $ad->id }})">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>

                                    <form id="deleteForm-{{ $ad->id }}" 
                                        action="{{ route('my-ads.deleteAd', $ad->id) }}" 
                                        method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                
            </table>
            @if($ads->count() == 0)
                <div class="alert alert-warning text-center">
                    No se encontraron anuncios.
                </div>
            @endif
        </div>

        {{-- ======================= --}}
        {{--     MÓVIL (CARDS) --}}
        {{-- ======================= --}}
        <div class="mobile-card">
            @foreach ($ads as $ad)
                <div class="card mb-3 shadow-sm border-0" style="border-radius: 14px;">
                    <div class="card-body">

                        {{-- Imagen --}}
                        <div class="mb-3 d-flex justify-content-center">
                            @php
                                $image = $ad->images->first();
                                $crop  = $image?->crop_data;

                                $thumbSize = 90; // un poco más grande en móvil

                                if ($crop && isset($crop['width'])) {
                                    $scale = $thumbSize / $crop['width'];
                                } else {
                                    $scale = 1;
                                }
                            @endphp

                            @if($image)
                                <div class="img-crop-box img-crop-box-mobile">
                                    <img
                                        src="{{ asset($image->image) }}"
                                        style="
                                            transform:
                                                scale({{ $scale }})
                                                translate(
                                                    -{{ $crop['x'] ?? 0 }}px,
                                                    -{{ $crop['y'] ?? 0 }}px
                                                );
                                        "
                                    >
                                </div>
                            @else
                                <div class="img-crop-box img-crop-box-mobile">
                                    <img src="{{ asset('assets/img/not-found-image/failed-image.jpg') }}">
                                </div>
                            @endif
                        </div>

                        <h6 class="fw-bold text-truncate">{{ $ad->title }}</h6>

                        <div class="small mb-3">
                            <div><strong>Categoría:</strong> {{ $ad->category->name }} > {{ $ad->subcategory->name }}</div>
                            <div><strong>Fecha:</strong> {{ $ad->created_at->format('d/m/Y') }}</div>

                            <div class="mt-2">
                                <strong>Estado:</strong>

                                @if($ad->expires_at && $ad->expires_at < now())
                                    <span class="badge bg-secondary">Expirado</span>

                                @elseif($ad->status === 'draft')
                                    <span class="badge bg-dark">
                                        Borrador
                                    </span>

                                @elseif($ad->status === 'pendiente')
                                    <span class="badge bg-warning text-dark">Pendiente</span>

                                @elseif($ad->status === 'rechazado')
                                    <span class="badge bg-danger">Rechazado</span>

                                @elseif($ad->status === 'publicado')
                                    <span class="badge bg-success">Publicado</span>

                                @else
                                    <span class="badge bg-secondary">Desconocido</span>
                                @endif
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="d-flex gap-2 justify-content-center flex-wrap">

                            {{-- CONTINUAR BORRADOR --}}
                            @if($ad->status === 'draft')
                                <a href="{{ route('my-ads.editDraft', $ad->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-play"></i>
                                    <span class="d-none d-sm-inline"> Editar</span>
                                </a>
                            @endif
                            
                            @if($ad->published)
                                <button class="btn btn-sm btn-outline-info"
                                    onclick="confirmDeactivate({{ $ad->id }})">
                                    <i class="fa-solid fa-ban"></i>
                                </button>

                                <form id="deactivateForm-{{ $ad->id }}"
                                    action="{{ route('my-ads.deactivate', $ad->id) }}"
                                    method="POST" style="display:none;">
                                    @csrf
                                </form>
                            @endif

                            @if($ad->receipt_type === 'nota_venta' && $ad->receipt_file)
                                <a href="{{ asset($ad->receipt_file) }}"
                                target="_blank"
                                class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </a>
                            @endif

                            <a href="{{ route('my-ads.show', $ad->id) }}" class="btn btn-sm btn-outline-success">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            @if(
                                $ad->status !== 'draft' &&
                                $ad->status !== 'publicado' &&
                                $ad->status !== 'rechazado'
                            )
                                <a href="{{ route('my-ads.editAd', $ad->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            @endif

                            <button type="button"
                                class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete({{ $ad->id }})">
                                <i class="fa-solid fa-trash"></i>
                            </button>

                            <form id="deleteForm-{{ $ad->id }}"
                                action="{{ route('my-ads.deleteAd', $ad->id) }}"
                                method="POST"
                                style="display:none;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="return_to" value="{{ url()->full() }}">
                            </form>

                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- BOTONES FLOTANTES --}}
        @auth
            @if(!in_array((int) auth()->user()->role_id, [1, 3]))
                <div class="floating-actions">

                    <!-- WhatsApp -->
                    <a href="https://wa.me/51{{ $systemSettings->whatsapp_number }}?text={{ urlencode('Hola quiero más información') }}"
                    target="_blank"
                    class="btn btn-success shadow d-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-2">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>

                    <!-- Crear anuncio -->
                    <button onclick="location.href='{{ route('my-ads.createAd') }}'"
                        class="btn btn-danger shadow d-flex align-items-center gap-2 px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-plus"></i>
                        <span>Crear Anuncio</span>
                    </button>

                </div>
            @endif
        @endauth

        {{-- PAGINACIÓN --}}
        @if ($ads->hasPages())
            <div class="my-pagination mt-4">
                {{ $ads->links('vendor.pagination.custom') }}
            </div>
        @endif

        <style>
            .my-pagination {
                display: flex;
                justify-content: center;
            }

            .my-pagination-list {
                list-style: none;
                padding: 0;
                display: flex;
                gap: 6px;
            }

            .my-pagination-list li a,
            .my-pagination-list li span {
                padding: 8px 12px;
                border-radius: 6px;
                background: #f1f1f1;
                color: #444;
                text-decoration: none;
                font-size: 14px;
                transition: 0.2s;
            }

            .my-pagination-list li a:hover {
                background: #007bff;
                color: white;
            }

            .my-pagination-list li.active span {
                background: #007bff;
                color: #fff;
                font-weight: bold;
            }

            .my-pagination-list li.disabled span {
                opacity: 0.4;
                cursor: not-allowed;
            }
        </style>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: "¿Eliminar anuncio?",
        html: `
            <p class="mb-2">Esta acción no se puede deshacer.</p>
            <small class="text-muted">
                ⚠️ Si el anuncio se encuentra en estado 
                <strong>pendiente</strong> de aprobación, 
                se realizará la devolución del monto pagado por el anuncio.
            </small>
        `,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`deleteForm-${id}`).submit();
        }
    });
}

function confirmDeactivate(id) {
    Swal.fire({
        title: "¿Dar de baja el anuncio?",
        text: "El anuncio dejará de publicarse inmediatamente.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#f0ad4e",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, dar de baja",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`deactivateForm-${id}`).submit();
        }
    });
}

// ALERTA DE ÉXITO
@if (session('success'))
Swal.fire({
    icon: 'success',
    title: 'Éxito',
    text: "{{ session('success') }}",
    confirmButtonColor: '#3085d6'
});
@endif

// ALERTA DE ERROR
@if (session('error'))
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: "{{ session('error') }}",
    confirmButtonColor: '#d33'
});
@endif

// ERRORES DE VALIDACIÓN
@if ($errors->any())
Swal.fire({
    icon: 'error',
    title: 'Corrige los errores',
    html: `
        <ul style="text-align:left;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    `,
});
@endif
</script>

<style>
.img-crop-box {
    width: 70px;
    height: 70px;
    overflow: hidden;
    position: relative;
}

.img-crop-box img {
    position: absolute;
    top: 0;
    left: 0;
    max-width: none;
    transform-origin: top left;
}

.img-crop-box-mobile {
    width: 90px;
    height: 90px;
}

.floating-actions {
    position: fixed;
    bottom: 85px; 
    right: 20px;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    align-items: center; 
}

.floating-actions .btn {
    padding: 12px 20px;   
    font-size: 15px;      
    border-radius: 1000px; 
}

.floating-actions .fa-whatsapp {
    font-size: 20px;
}

@media (max-width: 768px) {
    .desktop-table { display: none !important; }
    .mobile-card { display: block !important; }
}
@media (min-width: 769px) {
    .mobile-card { display: none !important; }
}
</style>

@endsection
