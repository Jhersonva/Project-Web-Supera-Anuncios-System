@extends('layouts.app')

@section('title', 'Solicitudes de Recarga')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.selector-btn {
    padding: 10px;
    font-weight: 600;
    border-radius: 10px;
    border: 2px solid #dc3545;
    transition: .3s;
}
.selector-btn.active {
    background: #dc3545;
    color: white;
}
.selector-btn:not(.active) {
    background: white;
    color: #dc3545;
}

.card-recarga {
    border-radius: 18px;
    padding: 20px;
    background: #fff;
    border: 1px solid #eee;
    margin-bottom: 20px;
    transition: .2s ease-in-out;
}
.card-recarga:hover {
    transform: translateY(-2px);
    box-shadow: 0px 6px 20px rgba(0,0,0,0.12);
}

.btn-accion {
    border-radius: 12px;
    font-weight: 600;
    padding: 10px;
}
</style>

<div class="container mt-5 mb-5">

    <h4 class="fw-bold text-center mb-2">Solicitudes de Recarga</h4>
    <p class="text-secondary text-center mb-4">
        Revisa, aprueba o rechaza las solicitudes enviadas por los usuarios.
    </p>

    <!-- SELECTORES -->
    <div class="d-flex gap-2 mb-4">
        <button id="btnPendientes" class="selector-btn w-50 active"> Solicitudes Pendientes</button>
        <button id="btnHistorial" class="selector-btn w-50">Historial</button>
    </div>

    <!-- ====== SECCIÓN PENDIENTES ====== -->
    <div id="seccionPendientes">

        @if($rechargesPendientes->isEmpty())
            <p class="text-center text-muted mt-4">No hay recargas pendientes.</p>
        @else

            @foreach ($rechargesPendientes as $r)
            <div class="card-recarga">

                <div class="d-flex justify-content-between">
                    <h5 class="fw-bold">{{ $r->user->full_name }}</h5>
                    <span class="badge bg-warning text-dark px-3 py-2">Pendiente</span>
                </div>

                <hr>

                <p><strong>Monto:</strong> S/. {{ number_format($r->monto, 2) }}</p>
                <p>
                    <strong>Método:</strong>
                    <span class="badge bg-light text-dark">{{ ucfirst($r->metodo_pago) }}</span>
                </p>
                <p>
                    <strong>Comprobante:</strong>
                    @if($r->img_cap_pago)
                        <a href="{{ asset($r->img_cap_pago) }}" target="_blank">
                            Ver <i class="fa-solid fa-eye"></i>
                        </a>
                    @else
                        <span class="text-muted">No adjuntado</span>
                    @endif
                </p>

                <div class="mt-3">
                    <!-- Aprobar -->
                    <button class="btn btn-success btn-accion w-100"
                        data-bs-toggle="modal"
                        data-bs-target="#approveModal{{ $r->id }}">
                        <i class="fa fa-check me-1"></i> Aprobar
                    </button>

                    <!-- Rechazar -->
                    <button class="btn btn-danger btn-accion w-100 mt-2"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal{{ $r->id }}">
                        <i class="fa fa-times me-1"></i> Rechazar
                    </button>
                </div>

            </div>

            {{-- MODAL APROBAR --}}
            <div class="modal fade" id="approveModal{{ $r->id }}">
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
            <div class="modal fade" id="rejectModal{{ $r->id }}">
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

        @endif

    </div>

    <!-- ====== SECCIÓN HISTORIAL ====== -->
    <div id="seccionHistorial" style="display:none;">

        @if($rechargesHistorial->isEmpty())
            <p class="text-center text-muted mt-4">No hay historial.</p>
        @else

            @foreach ($rechargesHistorial as $r)
            <div class="card-recarga">

                <div class="d-flex justify-content-between">
                    <h5 class="fw-bold">{{ $r->user->full_name }}</h5>

                    @if($r->status === 'aceptado')
                        <span class="badge bg-success px-3 py-2">Aprobado</span>
                    @else
                        <span class="badge bg-danger px-3 py-2">Rechazado</span>
                    @endif
                </div>

                <hr>

                <p><strong>Monto:</strong> S/. {{ number_format($r->monto, 2) }}</p>
                <p>
                    <strong>Método:</strong>
                    <span class="badge bg-light text-dark">{{ ucfirst($r->metodo_pago) }}</span>
                </p>
                <p>
                    <strong>Fecha:</strong>
                    {{ $r->created_at->format('d/m/Y H:i') }}
                </p>

            </div>
            @endforeach

        @endif

    </div>

</div>

<script>
// SELECTORES
const btnPendientes = document.getElementById("btnPendientes");
const btnHistorial  = document.getElementById("btnHistorial");

const seccionPendientes = document.getElementById("seccionPendientes");
const seccionHistorial  = document.getElementById("seccionHistorial");

btnPendientes.addEventListener("click", () => {
    btnPendientes.classList.add("active");
    btnHistorial.classList.remove("active");

    seccionPendientes.style.display = "block";
    seccionHistorial.style.display  = "none";
});

btnHistorial.addEventListener("click", () => {
    btnHistorial.classList.add("active");
    btnPendientes.classList.remove("active");

    seccionPendientes.style.display = "none";
    seccionHistorial.style.display  = "block";
});
</script>

@endsection
