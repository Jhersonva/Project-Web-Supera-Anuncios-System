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

        <form action="{{ route('admin.payment_methods.update', $method->id) }}"
              method="POST" enctype="multipart/form-data">
            @csrf

            {{-- NOMBRE --}}
            <div class="mb-3">
                <label class="form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control" value="{{ $method->nombre }}" required>
            </div>

            {{-- TIPO --}}
            <div class="mb-3">
                <label class="form-label">Tipo</label>
                <input type="text" name="tipo" class="form-control" value="{{ $method->tipo }}">
            </div>

            {{-- NÚMERO --}}
            <div class="mb-3">
                <label class="form-label">Número</label>
                <input type="text" name="numero" class="form-control" value="{{ $method->numero }}">
            </div>

            {{-- CUENTA --}}
            <div class="mb-3">
                <label class="form-label">Cuenta</label>
                <input type="text" name="cuenta" class="form-control" value="{{ $method->cuenta }}">
            </div>

            {{-- CCI --}}
            <div class="mb-3">
                <label class="form-label">CCI</label>
                <input type="text" name="cci" class="form-control" value="{{ $method->cci }}">
            </div>

            {{-- QR --}}
            <div class="mb-4">
                <label class="form-label">Código QR</label><br>

                @if ($method->qr)
                    <img src="{{ asset($method->qr) }}" width="120" class="rounded shadow-sm mb-2">
                @endif

                <input type="file" name="qr" class="form-control mt-2">
            </div>

            {{-- ACTIVO --}}
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="activo"
                        @if($method->activo) checked @endif>
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
