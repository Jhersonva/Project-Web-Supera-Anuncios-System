<!--views/admin/reload-request/index.blade.php-->
@extends('layouts.app')

@section('title', 'Solicitudes de Recarga')

@section('content')


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin-reload-request.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="container mt-4 mb-5">
    <h3 class="mb-4">Solicitudes de Recargas</h3>
    @foreach ($recharges as $r)
        <div class="recarga-card">

            {{-- HEADER --}}
            <div class="recarga-header">
                <h5 class="m-0">{{ $r->user->full_name }}</h5>

                @if($r->status === 'pendiente')
                    <span class="badge bg-warning">Pendiente</span>
                @elseif($r->status === 'aceptado')
                    <span class="badge bg-success">Aprobado</span>
                @else
                    <span class="badge bg-danger">Rechazado</span>
                @endif
            </div>

            <hr>

            {{-- INFO PRINCIPAL --}}
            <p class="m-0"><strong>Monto:</strong> S/. {{ number_format($r->monto, 2) }}</p>

            <p class="m-0 mt-1">
                <strong>Método:</strong>
                <span class="tag-metodo">{{ ucfirst($r->metodo_pago) }}</span>
            </p>

            <p class="mt-2 m-0">
                <strong>Comprobante:</strong>
                @if($r->img_cap_pago)
                    <a href="{{ asset('storage/' . $r->img_cap_pago) }}" class="text-primary" target="_blank">
                        Ver comprobante <i class="fa fa-image"></i>
                    </a>
                @else
                    <span>No adjuntado</span>
                @endif
            </p>

            {{-- ACCIONES --}}
            <div class="recarga-btns mt-3">

                @if($r->status === 'pendiente')

                    <form action="{{ route('admin.reload-request.approve', $r->id) }}" 
                          method="POST" class="d-inline-block w-100">
                        @csrf
                        <button class="btn btn-success w-100">
                            <i class="fa fa-check"></i> Aprobar
                        </button>
                    </form>

                    <form action="{{ route('admin.reload-request.reject', $r->id) }}"
                          method="POST" class="d-inline-block w-100">
                        @csrf
                        <button class="btn btn-danger w-100 mt-2">
                            <i class="fa fa-times"></i> Rechazar
                        </button>
                    </form>

                @else
                    <div class="text-muted mt-2">No disponible</div>
                @endif

            </div>

        </div>
    @endforeach
</div>

<script src="{{ asset('js/admin-reload-request.js') }}"></script>

@endsection
