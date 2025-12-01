@extends('layouts.app')

@section('title', 'Gestión de Caja')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4 mb-5">

    {{-- IZQUIERDA: BOTÓN VOLVER --}}
    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Gestión de Caja</h4>
    <p class="text-secondary text-center mb-4">
        Aquí podrás administrar el ingrese y egreso de dinero, como tambien el historial de movimientos.
    </p>

    <!-- BOTÓN ABRIR CAJA (Admin o Employee) -->
    @php
        $hasOpenCash = $cashBoxes->where('status', 'open')->isNotEmpty();
    @endphp

    @if(!$hasOpenCash)
    <div class="text-center mb-4">
        <button class="btn btn-success fw-semibold px-4 py-2"
            data-bs-toggle="modal"
            data-bs-target="#openCashModal">
            <i class="fa-solid fa-cash-register me-2"></i>Abrir Caja
        </button>
    </div>
    @endif


    <!-- LISTA DE CAJAS -->
    @foreach ($cashBoxes as $box)
    <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="fw-bold m-0">
                Caja #{{ $box->id }}
            </h5>

            @if($box->status === 'open')
                <span class="badge bg-success px-3 py-2">Abierta</span>
            @else
                <span class="badge bg-secondary px-3 py-2">Cerrada</span>
            @endif
        </div>

        <p class="m-0">
            <strong>Empleado:</strong>
            {{ $box->employee->full_name }}
        </p>

        <p class="m-0"><strong>Apertura:</strong> S/. {{ number_format($box->opening_balance, 2) }}</p>
        <p class="m-0"><strong>Saldo Actual:</strong> S/. {{ number_format($box->current_balance, 2) }}</p>

        <div class="mt-3 text-end">
            <a href="{{ route('admin.config.cash.show', $box->id) }}"
                class="btn btn-primary fw-semibold px-3">
                Ver detalle <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

    </div>
    @endforeach

</div>


<!-- MODAL ABRIR CAJA -->
<div class="modal fade" id="openCashModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.config.cash.open') }}" method="POST" class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Abrir Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="fw-semibold mb-1">Monto inicial:</label>
                <input type="number" name="opening_balance" class="form-control" min="0" required>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success fw-semibold px-3" type="submit">
                    Abrir Caja
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
