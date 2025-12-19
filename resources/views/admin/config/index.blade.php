@extends('layouts.app')

@section('title', 'Configuración')

@section('content')

<div class="container mt-5 mb-5">

    <h4 class="fw-bold mb-3 text-center">Configuración del Sistema</h4>
    <p class="text-secondary text-center mb-4">
        Aquí podrás administrar ajustes avanzados del sistema.
    </p>

    @php
        $role = auth()->user()->role_id;
    @endphp

    <div class="row">

        {{-- SOLO ADMIN (1): Categorías, Personal --}}
        @if($role == 1)
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
        @endif

        {{-- ADMIN (1) Y EMPLOYEE (3): Clientes --}}
        @if(in_array($role, [1, 3]))
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
        @endif



        {{-- SOLO ADMIN (1): Gestión de Personal --}}
        @if($role == 1)
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
        @endif

        {{-- ADMIN (1) Y EMPLOYEE (3): Caja --}}
        @if(in_array($role, [1, 3]))
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

                <a href="{{ route('admin.config.cash.index') }}" class="btn btn-success w-100 fw-semibold py-2 text-white">
                    Administrar caja
                    <i class="fa-solid fa-chevron-right ms-2"></i>
                </a>

            </div>
        </div>
        @endif

        <!-- SOLO ADMIN (1): Gestión de Métodos de Pago -->
        @if($role == 1)
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info text-white p-3 rounded-circle me-3"
                        style="width: 55px; height: 55px; display:flex; justify-content:center; align-items:center;">
                        <i class="fa-solid fa-credit-card fa-lg"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold m-0">Métodos de Pago</h5>
                        <small class="text-muted">Administra los métodos de pago disponibles</small>
                    </div>
                </div>

                <p class="text-secondary mb-3">
                    Agrega, edita o elimina métodos de pago que los usuarios podrán usar para sus recargas.
                </p>

                <a href="{{ route('admin.config.payment_methods.index') }}" 
                class="btn btn-info w-100 fw-semibold py-2 text-white">
                    Administrar Métodos de Pago
                    <i class="fa-solid fa-chevron-right ms-2"></i>
                </a>

            </div>
        </div>
        @endif

        {{-- SOLO ADMIN (1): Configuración General del Sistema --}}
        @if($role == 1)
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

                <div class="d-flex align-items-center mb-3">
                    <div class="bg-dark text-white p-3 rounded-circle me-3"
                        style="width: 55px; height: 55px; display:flex; justify-content:center; align-items:center;">
                        <i class="fa-solid fa-gear fa-lg"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold m-0">Configuración del Sistema</h5>
                        <small class="text-muted">
                            Datos generales, logo y presentación
                        </small>
                    </div>
                </div>

                <p class="text-secondary mb-3">
                    Administra el nombre del sistema, descripción general y el logo principal
                    que se mostrará en todo el sistema.
                </p>

                <a href="{{ route('admin.config.system') }}"
                class="btn btn-dark w-100 fw-semibold py-2">
                    Administrar configuración
                    <i class="fa-solid fa-chevron-right ms-2"></i>
                </a>

            </div>
        </div>
        @endif

        {{-- ADMIN (1) Y EMPLOYEE (3): Libro de Reclamaciones --}}
        @if(in_array($role, [1, 3]))
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

                <div class="d-flex align-items-center mb-3">
                    <div class="bg-danger text-white p-3 rounded-circle me-3"
                        style="width: 55px; height: 55px; display:flex; justify-content:center; align-items:center;">
                        <i class="fa-solid fa-book fa-lg"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold m-0">Libro de Reclamaciones</h5>
                        <small class="text-muted">Texto legal y datos de la empresa</small>
                    </div>
                </div>

                <p class="text-secondary mb-3">
                    Administra el texto legal y la información que verán los usuarios al enviar un reclamo.
                </p>

                <a href="{{ route('admin.config.complaint_book_settings.index') }}"
                class="btn btn-danger w-100 fw-semibold py-2 text-white">
                    Gestionar libro
                    <i class="fa-solid fa-chevron-right ms-2"></i>
                </a>
            </div>
        </div>
        @endif

        {{-- ADMIN (1) Y EMPLOYEE (3): Gestión de Reclamos --}}
        @if(in_array($role, [1, 3]))
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning text-white p-3 rounded-circle me-3"
                        style="width: 55px; height: 55px; display:flex; justify-content:center; align-items:center;">
                        <i class="fa-solid fa-book-open"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold m-0">Gestión de Reclamos</h5>
                        <small class="text-muted">Reclamos y quejas recibidas</small>
                    </div>
                </div>

                <p class="text-secondary mb-3">
                    Revisa, responde, actualiza y elimina reclamos enviados por los usuarios desde aqui.
                </p>

                <a href="{{ route('admin.config.complaints.index') }}"
                class="btn btn-warning w-100 fw-semibold py-2 text-white">
                    Ver reclamos
                    <i class="fa-solid fa-chevron-right ms-2"></i>
                </a>

            </div>
        </div>
        @endif

        {{-- ADMIN (1) Y EMPLOYEE (3): Políticas de Privacidad --}}
        @if(in_array($role, [1, 3]))
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 16px;">

                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white p-3 rounded-circle me-3"
                        style="width: 55px; height: 55px; display:flex; justify-content:center; align-items:center;">
                        <i class="fa-solid fa-shield-halved fa-lg"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold m-0">Políticas de Privacidad</h5>
                        <small class="text-muted">Contenido legal y restricciones</small>
                    </div>
                </div>

                <p class="text-secondary mb-3">
                    Administra el texto de privacidad, contenido explícito y validación de edad.
                </p>

                <a href="{{ route('admin.config.privacy-policy.index') }}"
                class="btn btn-primary w-100 fw-semibold py-2 text-white">
                    Gestionar políticas
                    <i class="fa-solid fa-chevron-right ms-2"></i>
                </a>

            </div>
        </div>
        @endif


    </div>
</div>
@endsection
