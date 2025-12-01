@extends('layouts.app')

@section('title', 'Solicitudes de Recarga')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .card-recarga {
        border-radius: 18px;
        padding: 20px;
        background: #fff;
        border: none;
        box-shadow: 0px 4px 15px rgba(0,0,0,0.07);
        margin-bottom: 25px;
        transition: .2s ease-in-out;
    }
    .card-recarga:hover {
        transform: translateY(-2px);
        box-shadow: 0px 6px 20px rgba(0,0,0,0.12);
    }

    .header-user {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .tag-metodo {
        background: #f1f1f1;
        padding: 3px 10px;
        border-radius: 10px;
        font-size: 0.85rem;
    }

    .btn-accion {
        border-radius: 12px;
        font-weight: 600;
        padding: 10px;
    }

    .btn-success { background: #28a745; }
    .btn-danger { background: #dc3545; }

</style>

<div class="container mt-5 mb-5">

    <h4 class="fw-bold text-center mb-2">Solicitudes de Recarga</h4>
    <p class="text-secondary text-center mb-4">
        Revisa, aprueba o rechaza las solicitudes enviadas por los usuarios.
    </p>

    @foreach ($recharges as $r)
    <div class="card-recarga">

        {{-- HEADER --}}
        <div class="header-user">
            <div>
                <h5 class="fw-bold m-0">{{ $r->user->full_name }}</h5>
            </div>

            {{-- ESTADO --}}
            @if($r->status === 'pendiente')
                <span class="badge bg-warning text-dark px-3 py-2">Pendiente</span>
            @elseif($r->status === 'aceptado')
                <span class="badge bg-success px-3 py-2">Aprobado</span>
            @else
                <span class="badge bg-danger px-3 py-2">Rechazado</span>
            @endif
        </div>

        <hr>

        {{-- INFO PRINCIPAL --}}
        <div class="mt-2">
            <p class="m-0"><strong>Monto:</strong> S/. {{ number_format($r->monto, 2) }}</p>

            <p class="m-0 mt-1">
                <strong>Método:</strong>
                <span class="tag-metodo">
                    {{ ucfirst($r->metodo_pago) }}
                </span>
            </p>

            <p class="mt-2 m-0">
                <strong>Comprobante:</strong>
                @if($r->img_cap_pago)
                    <a href="{{ asset($r->img_cap_pago) }}" class="text-success" target="_blank">
                        Ver <i class="fa-solid fa-eye"></i>
                    </a>
                @else
                    <span class="text-muted">No adjuntado</span>
                @endif
            </p>
        </div>

        {{-- ACCIONES --}}
        <div class="mt-4">

            @if($r->status === 'pendiente')
                
                <!-- BOTÓN APROBAR -->
                <button class="btn btn-success btn-accion w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#approveModal{{ $r->id }}">
                    <i class="fa fa-check me-1"></i> Aprobar
                </button>

                <!-- BOTÓN RECHAZAR -->
                <button class="btn btn-danger btn-accion w-100 mt-2"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectModal{{ $r->id }}">
                    <i class="fa fa-times me-1"></i> Rechazar
                </button>

            @else
                <div class="text-muted text-center mt-3">
                    <i class="fa fa-ban me-1"></i> Acciones no disponibles
                </div>
            @endif

        </div>

    </div>


    {{-- MODAL APROBAR --}}
    <div class="modal fade" id="approveModal{{ $r->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.reload-request.approve', $r->id) }}">
                @csrf
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Aprobar Recarga</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <p><strong>Usuario:</strong> {{ $r->user->full_name }}</p>

                        <label class="form-label fw-bold">Número de Operación</label>
                        <input type="text" class="form-control" name="operation_number" required>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success w-100">Confirmar aprobación</button>
                    </div>

                </div>
            </form>
        </div>
    </div>


    {{-- MODAL RECHAZAR --}}
    <div class="modal fade" id="rejectModal{{ $r->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.reload-request.reject', $r->id) }}">
                @csrf
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Rechazar Recarga</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <p><strong>Usuario:</strong> {{ $r->user->full_name }}</p>

                        <label class="form-label fw-bold">Motivo del rechazo</label>
                        <textarea class="form-control" name="reject_message" rows="3" required></textarea>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger w-100">Rechazar</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @endforeach

</div>

@endsection
