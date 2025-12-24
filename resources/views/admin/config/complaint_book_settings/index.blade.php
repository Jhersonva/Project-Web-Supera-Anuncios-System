@extends('layouts.app')

@section('title', 'Gestión del Libro de Reclamaciones')

@section('content')

<div class="container mt-5 mb-5">

    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Libro de Reclamaciones</h4>
    <p class="text-secondary text-center mb-4">
        Configura el texto legal y los datos que se mostrarán a los usuarios.
    </p>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

                <form method="POST" action="{{ route('admin.config.complaint_book_settings.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- Nombre del Negocio --}}
                    <div class="mb-3">
                        <label class="form-label">Nombre del Negocio</label>
                        <input type="text" name="business_name" class="form-control"
                               value="{{ old('business_name', $settings->business_name ?? '') }}">
                        @error('business_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- RUC --}}
                    <div class="mb-3">
                        <label class="form-label">RUC</label>
                        <input type="text" name="ruc" class="form-control"
                               value="{{ old('ruc', $settings->ruc ?? '') }}">
                    </div>

                    {{-- Dirección --}}
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="address" class="form-control"
                               value="{{ old('address', $settings->address ?? '') }}">
                    </div>

                    {{-- Correo de notificación --}}
                    <div class="mb-3">
                        <label class="form-label">Correo de Notificación</label>
                        <input type="email" name="notification_email" class="form-control"
                               value="{{ old('notification_email', $settings->notification_email ?? '') }}">
                    </div>

                    {{-- Texto Legal --}}
                    <div class="mb-4">
                        <label class="form-label">Texto Legal del Libro</label>
                        <textarea name="legal_text" rows="6" class="form-control"
                            placeholder="Texto legal visible para el usuario">{{ old('legal_text', $settings->legal_text ?? '') }}</textarea>
                        @error('legal_text')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-danger w-100 fw-semibold py-2">
                        Actualizar Libro de Reclamaciones
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

@endsection