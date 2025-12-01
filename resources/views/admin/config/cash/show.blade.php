@extends('layouts.app')

@section('title', 'Detalle de Caja')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4 mb-5">

    <a href="{{ route('admin.cash.index') }}" class="btn btn-light mb-3">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>

    <h3 class="fw-bold text-center mb-4">Detalle de Caja #{{ $cashBox->id }}</h3>

    <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

        <div class="d-flex justify-content-between">
            <h5 class="fw-semibold">Información General</h5>

            @if($cashBox->status === 'open')
                <span class="badge bg-success px-3 py-2">Abierta</span>
            @else
                <span class="badge bg-secondary px-3 py-2">Cerrada</span>
            @endif
        </div>

        <p class="m-0 mt-2"><strong>Empleado:</strong> {{ $cashBox->employee->full_name }}</p>
        <p class="m-0"><strong>Apertura:</strong> S/. {{ number_format($cashBox->opening_balance, 2) }}</p>
        <p class="m-0"><strong>Saldo Actual:</strong> S/. {{ number_format($cashBox->current_balance, 2) }}</p>

        <!-- CERRAR CAJA -->
        @if($cashBox->status === 'open')
        <form action="{{ route('admin.cash.close', $cashBox->id) }}" method="POST" class="mt-3">
            @csrf
            <button class="btn btn-danger fw-semibold w-100">
                <i class="fa-solid fa-lock me-2"></i> Cerrar Caja
            </button>
        </form>
        @endif

    </div>


    <!-- MOVIMIENTOS -->
    <h5 class="fw-bold mt-4 mb-3">Movimientos</h5>

    @foreach($cashBox->movements as $mv)
    <div class="card shadow-sm border-0 p-3 mb-3" style="border-radius: 14px;">
        <div class="d-flex justify-content-between">
            <h6 class="fw-bold m-0">
                @if($mv->type === 'income')
                    <span class="text-success">Ingreso</span>
                @else
                    <span class="text-danger">Egreso</span>
                @endif
            </h6>

            <span class="fw-bold">
                S/. {{ number_format($mv->amount, 2) }}
            </span>
        </div>

        <p class="text-muted m-0">
            {{ $mv->description ?: 'Sin descripción' }}
        </p>

        <small class="text-muted">
            Registrado por: {{ $mv->employee->full_name }}
        </small>
    </div>
    @endforeach


    <!-- AGREGAR MOVIMIENTO -->
    @if($cashBox->status === 'open')
    <div class="card shadow-sm border-0 p-3 mt-4" style="border-radius: 14px;">
        <h5 class="fw-bold mb-3">Registrar Movimiento</h5>

        <form action="{{ route('admin.cash.movement', $cashBox->id) }}" method="POST">
            @csrf

            <label class="fw-semibold">Tipo:</label>
            <select name="type" class="form-control mb-3" required>
                <option value="income">Ingreso</option>
                <option value="expense">Egreso</option>
            </select>

            <label class="fw-semibold">Monto:</label>
            <input type="number" name="amount" min="0.1" class="form-control mb-3" required>

            <label class="fw-semibold">Descripción:</label>
            <textarea name="description" class="form-control mb-3"></textarea>

            <button class="btn btn-primary fw-semibold w-100">
                Registrar Movimiento
            </button>
        </form>
    </div>
    @endif

</div>

@endsection
