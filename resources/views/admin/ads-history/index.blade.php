@extends('layouts.app')

@section('title', 'Historial de Anuncios')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container mt-5 mb-5">

    <h4 class="fw-bold text-center mb-2">Historial de Anuncios</h4>
    <p class="text-muted text-center mb-4">
        Listado completo de anuncios publicados y expirados.
    </p>

    {{-- FILTROS --}}
    <form method="GET" class="row mb-4 g-2 justify-content-center">

        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}"
                class="form-control" placeholder="Buscar por título o usuario...">
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Estado --</option>
                <option value="publicado" {{ request('status') == 'publicado' ? 'selected' : '' }}>Publicado</option>
                <option value="expirado" {{ request('status') == 'expirado' ? 'selected' : '' }}>Expirado</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">
                <i class="fa-solid fa-filter me-1"></i> Filtrar
            </button>
        </div>
    </form>

    {{-- TARJETA PRINCIPAL --}}
    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

        {{-- ICONO Y TÍTULO --}}
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary text-white p-3 rounded-circle me-3"
                style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-rectangle-ad fa-lg"></i>
            </div>

            <div>
                <h5 class="fw-bold m-0">Listado de Anuncios</h5>
                <small class="text-muted">Historial completo del sistema</small>
            </div>
        </div>

        @if($ads->count() == 0)
            <div class="alert alert-warning text-center">
                No se encontraron anuncios.
            </div>
        @endif

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
                        <th>Usuario</th>
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
                                @endphp

                                <img src="{{ asset($image->image ?? 'assets/img/not-found-image/failed-image.jpg') }}"
                                    class="rounded"
                                    width="70">
                            </td>

                            <td class="fw-semibold">{{ $ad->title }}</td>

                            <td>{{ $ad->category->name }} > {{ $ad->subcategory->name }}</td>

                            <td>
                                @if($ad->user->account_type === 'business')
                                    {{ $ad->user->company_reason }}
                                @else
                                    {{ $ad->user->full_name }}
                                @endif
                            </td>

                            <td>
                                @if($ad->status === 'draft')
                                    <span class="badge bg-dark">Borrador</span>

                                @elseif($ad->expires_at && $ad->expires_at < now())
                                    <span class="badge bg-secondary">Expirado</span>

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

                                    @if($ad->receipt_file)
                                        <a href="{{ asset($ad->receipt_file) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="Ver comprobante">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('my-ads.show', $ad->id) }}"
                                        class="btn btn-sm btn-outline-success">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <a href="{{ route('my-ads.editAd', $ad->id) }}"
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form class="delete-ad-form" action="{{ route('my-ads.deleteAd', $ad->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ======================= --}}
        {{--     MÓVIL (CARDS)      --}}
        {{-- ======================= --}}
        <div class="mobile-card">
            @foreach ($ads as $ad)
                <div class="card mb-3 shadow-sm border-0" style="border-radius: 14px;">
                    <div class="card-body">

                        {{-- Imagen --}}
                        <div class="mb-3 text-center">
                            @php
                                $image = $ad->images->first();
                            @endphp

                            <img src="{{ asset($image->image ?? 'assets/img/not-found-image/failed-image.jpg') }}"
                                class="rounded"
                                width="90">
                        </div>

                        <h6 class="fw-bold">{{ $ad->title }}</h6>

                        <div class="small mb-3">
                            <div><strong>Categoría:</strong> {{ $ad->category->name }} > {{ $ad->subcategory->name }}</div>
                            <div>
                                <strong>Usuario:</strong>
                                @if($ad->user->account_type === 'business')
                                    {{ $ad->user->company_reason }}
                                @else
                                    {{ $ad->user->full_name }}
                                @endif
                            </div>
                            <div><strong>Fecha:</strong> {{ $ad->created_at->format('d/m/Y') }}</div>

                            <div class="mt-2">
                                <strong>Estado:</strong>
                                @if($ad->status === 'draft')
                                    <span class="badge bg-dark">Borrador</span>

                                @elseif($ad->expires_at && $ad->expires_at < now())
                                    <span class="badge bg-secondary">Expirado</span>

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

                            @if($ad->receipt_file)
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

                            <a href="{{ route('my-ads.editAd', $ad->id) }}" 
                            class="btn btn-sm btn-warning text-white">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <form action="{{ route('my-ads.deleteAd', $ad->id) }}" 
                                method="POST"
                                onsubmit="return confirm('¿Eliminar anuncio?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        </div>
                        
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PAGINACIÓN --}}
        {{ $ads->links('vendor.pagination.custom') }}
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
function sendWhatsApp(phone, message) {
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}

document.addEventListener('DOMContentLoaded', function() {

    // Seleccionamos todos los botones eliminar
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('form'); 

            Swal.fire({
                title: '¿Eliminar anuncio?',
                html: `
                    <p class="mb-2">Esta acción no se puede deshacer.</p>
                    <small class="text-muted">
                        ⚠️ Si el anuncio se encuentra en estado 
                        <strong>pendiente</strong> de aprobación, 
                        el monto pagado será <strong>devuelto al usuario anunciante</strong>.
                    </small>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

});

</script>

<style>
@media (max-width: 768px) {
    .desktop-table { display: none !important; }
    .mobile-card { display: block !important; }
}
@media (min-width: 769px) {
    .mobile-card { display: none !important; }
}
</style>

@endsection
