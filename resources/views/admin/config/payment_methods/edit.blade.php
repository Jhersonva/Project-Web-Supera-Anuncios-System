@extends('layouts.app')

@section('title', 'Editar Método de Pago')

@section('content')
<div class="container mt-5 mb-5">

    {{-- VOLVER --}}
    <a href="{{ route('admin.config.payment_methods.index') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Editar Método de Pago</h4>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

        <form action="{{ route('admin.config.payment_methods.update', $method->id) }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- NOMBRE DEL MÉTODO --}}
            <div class="mb-3">
                <label class="form-label">Nombre *</label>
                <input type="text" name="name_method" class="form-control"
                    value="{{ $method->name_method }}" required>
            </div>

            {{-- TIPO --}}
            <div class="mb-3">
                <label class="form-label">Tipo</label>
                <input type="text" name="type" class="form-control"
                    value="{{ $method->type }}">
            </div>

            {{-- LOGO --}}
            <div class="mb-4">
                <label class="form-label">Logo</label><br>

                @if ($method->logo)
                    <img src="{{ asset($method->logo) }}" width="120"
                        class="rounded shadow-sm mb-2">
                @endif

                <input type="file" name="logo" class="form-control mt-2">
            </div>

            {{-- TITULAR --}}
            <div class="mb-3">
                <label class="form-label">Nombre del Titular</label>
                <input type="text" name="holder_name" class="form-control"
                    value="{{ $method->holder_name }}">
            </div>

            {{-- CELULAR --}}
            <div class="mb-3">
                <label class="form-label">Número de Celular</label>
                <input type="text" name="cell_phone_number" class="form-control"
                    value="{{ $method->cell_phone_number }}">
            </div>

            {{-- CUENTA --}}
            <div class="mb-3">
                <label class="form-label">Número de Cuenta</label>
                <input type="text" name="account_number" class="form-control"
                    value="{{ $method->account_number }}">
            </div>

            {{-- CCI --}}
            <div class="mb-3">
                <label class="form-label">CCI</label>
                <input type="text" name="cci" class="form-control"
                    value="{{ $method->cci }}">
            </div>

            {{-- QR --}}
            <div class="mb-4">
                <label class="form-label">Código QR</label><br>

                @if ($method->qr)
                    <img src="{{ asset($method->qr) }}" width="120"
                        class="rounded shadow-sm mb-2">
                @endif

                <input type="file" name="qr" class="form-control mt-2">
            </div>

            {{-- ACTIVO --}}
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="active"
                    @if($method->active) checked @endif>
                <label class="form-check-label">Activo</label>
            </div>

            {{-- BOTÓN --}}
            <button class="btn btn-primary w-100">
                <i class="fa-solid fa-save me-2"></i>Actualizar
            </button>

        </form>
    </div>

</div>
@endsection
