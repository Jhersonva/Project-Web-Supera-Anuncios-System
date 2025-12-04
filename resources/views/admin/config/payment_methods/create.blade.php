@extends('layouts.app')

@section('title', 'Crear Método de Pago')

@section('content')
<div class="container mt-5 mb-5">

    {{-- VOLVER --}}
    <a href="{{ route('admin.config.payment_methods.index') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Crear Método de Pago</h4>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">
        
        <form action="{{ route('admin.config.payment_methods.store') }}" method="POST"
              enctype="multipart/form-data">
            @csrf

            {{-- NOMBRE DEL MÉTODO --}}
            <div class="mb-3">
                <label class="form-label">Nombre del Método *</label>
                <input type="text" name="name_method" class="form-control" required>
            </div>

            {{-- TIPO --}}
            <div class="mb-3">
                <label class="form-label">Tipo</label>
                <input type="text" name="type" class="form-control" placeholder="Yape / Plin / Banco / etc">
            </div>

            {{-- LOGO --}}
            <div class="mb-4">
                <label class="form-label">Logo</label>
                <input type="file" name="logo" class="form-control">
            </div>

            {{-- TITULAR --}}
            <div class="mb-3">
                <label class="form-label">Nombre del Titular</label>
                <input type="text" name="holder_name" class="form-control">
            </div>

            {{-- CELULAR --}}
            <div class="mb-3">
                <label class="form-label">Número de Celular</label>
                <input type="text" name="cell_phone_number" class="form-control">
            </div>

            {{-- CUENTA --}}
            <div class="mb-3">
                <label class="form-label">Número de Cuenta</label>
                <input type="text" name="account_number" class="form-control">
            </div>

            {{-- CCI --}}
            <div class="mb-3">
                <label class="form-label">CCI</label>
                <input type="text" name="cci" class="form-control">
            </div>

            {{-- QR --}}
            <div class="mb-4">
                <label class="form-label">Código QR</label>
                <input type="file" name="qr" class="form-control">
            </div>

            {{-- ACTIVO --}}
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="active" checked>
                <label class="form-check-label">Activo</label>
            </div>

            {{-- BOTÓN --}}
            <button class="btn btn-success w-100">
                <i class="fa-solid fa-check me-2"></i>Guardar
            </button>
        </form>

    </div>
</div>
@endsection
