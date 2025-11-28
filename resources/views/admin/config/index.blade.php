@extends('layouts.app')

@section('title', 'Configuración')

@section('content')

<div class="container mt-5 mb-5">

    <h3 class="fw-bold mb-3 text-center">Configuración del Sistema</h3>
    <p class="text-secondary text-center mb-4">
        Aquí podrás administrar ajustes avanzados del sistema.
    </p>

    <!-- CARD PRINCIPAL -->
    <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

        <div class="d-flex align-items-center mb-3">
            <div class="bg-danger text-white p-3 rounded-circle me-3" 
                style="width: 55px; height: 55px; display:flex; justify-content:center; align-items:center;">
                <i class="fa-solid fa-folder-tree fa-lg"></i>
            </div>

            <div>
                <h5 class="fw-bold m-0">Configuración de Categorías</h5>
                <small class="text-muted">Administra categorías, sub-categorías y campos personalizados</small>
            </div>
        </div>

        <p class="text-secondary mb-3">
            Organiza la estructura de categorías que verán los usuarios al publicar sus anuncios.
            Esto incluye nombres, íconos, campos dinámicos y jerarquías.
        </p>

        <a href="{{ route('admin.config.categories') }}" class="btn btn-danger w-100 fw-semibold py-2">
            Administrar categorías
            <i class="fa-solid fa-chevron-right ms-2"></i>
        </a>

    </div>

</div>

@endsection
