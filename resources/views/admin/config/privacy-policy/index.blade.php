@extends('layouts.app')

@section('title', 'Términos y Condiciones')

@section('content')
<div class="container mt-5 mb-5">

    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Términos y Condiciones</h4>
    <p class="text-secondary text-center mb-5">
        Administra los textos legales y validaciones del sistema.
    </p>

    <div class="row g-4 justify-content-center">

        {{-- Términos y Condiciones Login/Registro --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 h-100" style="border-radius:16px;">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white p-3 rounded-circle me-3">
                        <i class="fa-solid fa-address-book"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Políticas de Privacidad</h6>
                        <small class="text-muted">Login / Registro</small>
                    </div>
                </div>

                <p class="text-secondary">
                    Texto legal visible para todos los usuarios al registrarse.
                </p>

                <a href="{{ route('admin.adult.authentication_terms.index') }}"
                   class="btn btn-primary w-100 fw-semibold">
                    Gestionar
                </a>
            </div>
        </div>

        {{-- Ver contenido adulto --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 h-100" style="border-radius:16px;">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning text-white p-3 rounded-circle me-3">
                        <i class="fa-solid fa-eye-slash"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Contenido Adulto (Ver)</h6>
                        <small class="text-muted">Advertencia al usuario</small>
                    </div>
                </div>

                <p class="text-secondary">
                    Términos que el usuario debe aceptar para visualizar contenido explícito.
                </p>

                <a href="{{ route('adult.view_terms.index') }}"
                   class="btn btn-warning w-100 fw-semibold text-white">
                    Gestionar
                </a>
            </div>
        </div>

        {{-- Publicar contenido adulto --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 h-100" style="border-radius:16px;">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-danger text-white p-3 rounded-circle me-3">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Contenido Adulto (Publicar)</h6>
                        <small class="text-muted">Responsabilidad del anunciante</small>
                    </div>
                </div>

                <p class="text-secondary">
                    Condiciones para permitir la publicación de contenido adulto.
                </p>

                <a href="{{ route('adult.publish_terms.index') }}"
                   class="btn btn-danger w-100 fw-semibold">
                    Gestionar
                </a>
            </div>
        </div>

        {{-- Alertas del sistema --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 h-100" style="border-radius:16px;">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info text-white p-3 rounded-circle me-3">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Alertas del Sistema</h6>
                        <small class="text-muted">Mensajes informativos</small>
                    </div>
                </div>

                <p class="text-secondary">
                    Administra alertas visibles para los usuarios del sistema.
                </p>

                <a href="{{ route('alerts.index') }}"
                class="btn btn-info w-100 fw-semibold text-white">
                    Gestionar
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
