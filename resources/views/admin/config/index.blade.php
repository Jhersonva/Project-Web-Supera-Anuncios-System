@extends('layouts.app')

@section('title', 'Configuración')

@section('content')

<div class="container mt-5 mb-5">

    <h4 class="fw-bold mb-3 text-center">Configuración del Sistema</h4>
    <p class="text-secondary text-center mb-4">
        Aquí podrás administrar ajustes avanzados del sistema.
    </p>

    <div class="row">

    <!-- CARD: GESTIÓN DE CATEGORÍAS -->
    <div class="col-md-6">
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
                Organiza la estructura de categorías para las publicaciones del sistema.
            </p>

            <a href="{{ route('admin.config.categories') }}" class="btn btn-danger w-100 fw-semibold py-2">
                Administrar categorías
                <i class="fa-solid fa-chevron-right ms-2"></i>
            </a>

        </div>
    </div>



    <!-- CARD: GESTIÓN DE CLIENTES -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary text-white p-3 rounded-circle me-3"
                    style="width: 55px; height: 55px; display:flex; justify-content:center; align-items:center;">
                    <i class="fa-solid fa-users fa-lg"></i>
                </div>

                <div>
                    <h5 class="fw-bold m-0">Gestión de Clientes</h5>
                    <small class="text-muted">Administra información y seguimiento de clientes</small>
                </div>
            </div>

            <p class="text-secondary mb-3">
                Registra, actualiza y gestiona la información completa de tus clientes.
            </p>

            <a href="{{ route('admin.config.clients') }}" class="btn btn-primary w-100 fw-semibold py-2">

                Administrar clientes
                <i class="fa-solid fa-chevron-right ms-2"></i>
            </a>
        </div>
    </div>

    <!-- CARD: GESTIÓN DE PERSONAL -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

            <div class="d-flex align-items-center mb-3">
                <div class="bg-warning text-white p-3 rounded-circle me-3"
                    style="width: 55px; height: 55px; display:flex; justify-content:center; align-items:center;">
                    <i class="fa-solid fa-user-gear fa-lg"></i>
                </div>

                <div>
                    <h5 class="fw-bold m-0">Gestión de Personal</h5>
                    <small class="text-muted">Control de empleados, roles y permisos</small>
                </div>
            </div>

            <p class="text-secondary mb-3">
                Gestiona empleados, roles, niveles de acceso y estados laborales.
            </p>

            <a href="{{ route('admin.config.employees') }}" class="btn btn-warning w-100 fw-semibold py-2 text-white">
                Administrar personal
                <i class="fa-solid fa-chevron-right ms-2"></i>
            </a>

        </div>
    </div>

    <!-- CARD: GESTIÓN DE CAJA -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

            <div class="d-flex align-items-center mb-3">
                <div class="bg-success text-white p-3 rounded-circle me-3"
                    style="width: 55px; height: 55px; display:flex; justify-content:center; align-items:center;">
                    <i class="fa-solid fa-cash-register fa-lg"></i>
                </div>

                <div>
                    <h5 class="fw-bold m-0">Gestión de Caja</h5>
                    <small class="text-muted">Movimientos, arqueos e ingresos</small>
                </div>
            </div>

            <p class="text-secondary mb-3">
                Control total de ingresos, egresos, arqueos de caja y reportes diarios.
            </p>

            <a href="{{ route('admin.config.cash') }}" class="btn btn-success w-100 fw-semibold py-2 text-white">
                Administrar caja
                <i class="fa-solid fa-chevron-right ms-2"></i>
            </a>

        </div>
    </div>

</div>

</div>

@endsection
