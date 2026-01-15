@extends('layouts.app')

@section('title', 'Gestión de Empleados')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container mt-5 mb-5">

    {{-- BOTÓN VOLVER --}}
    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold text-center mb-2">Gestión de Empleados</h4>
    <p class="text-muted text-center mb-4">
        Administrar los usuarios que cuentan con rol de empleado.
    </p>

    {{-- BOTÓN CREAR EMPLEADO --}}
    <div class="text-end mb-3">
        <a href="{{ route('admin.config.employees.create') }}" class="btn btn-success">
            <i class="fa-solid fa-user-plus me-2"></i> Crear Empleado
        </a>
    </div>

    @if($employees->count() == 0)
        <div class="alert alert-warning text-center">
            No hay empleados registrados.
        </div>
    @endif

    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

        {{-- ICONO Y TÍTULO --}}
        <div class="d-flex align-items-center mb-4">
            <div class="bg-warning text-white p-3 rounded-circle me-3"
                style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-user-tie fa-lg"></i>
            </div>

            <div>
                <h5 class="fw-bold m-0">Empleados Registrados</h5>
                <small class="text-muted">Usuarios con rol de empleado</small>
            </div>
        </div>

        {{-- ========================= --}}
        {{--   VISTA ESCRITORIO (TABLA) --}}
        {{-- ========================= --}}
        <div class="table-responsive desktop-table">

            {{-- ===================== --}}
            {{-- ADMINISTRADOR --}}
            {{-- ===================== --}}
            @if($admin)
            <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius:16px;background:#f8f9fa;">

                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white p-3 rounded-circle me-3"
                        style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-user-shield fa-lg"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold m-0">Administrador</h5>
                        <small class="text-muted">Cuenta principal del sistema</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-semibold">{{ $admin->full_name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ $admin->dni }}</td>
                                <td>{{ $admin->phone }}</td>
                                <td>{{ $admin->locality }}</td>
                                <td>
                                    <span class="badge bg-primary">ADMIN</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.config.employees.edit', $admin) }}"
                                    class="btn btn-sm btn-primary">
                                        Editar Perfil
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            @endif

            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>DNI</th>
                        <th>Teléfono</th>
                        <th>Localidad</th>
                        <!--<th>WhatsApp</th>
                        <th>Llamadas</th>-->
                        <th>Email Contacto</th>
                        <th>Dirección</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($employees as $emp)
                        <tr>
                            <td class="fw-semibold">{{ $emp->full_name }}</td>
                            <td>{{ $emp->email }}</td>
                            <td>{{ $emp->dni }}</td>
                            <td>{{ $emp->phone }}</td>
                            <td>{{ $emp->locality }}</td>
                            <!--<td>{{ $emp->whatsapp }}</td>
                            <td>{{ $emp->call_phone }}</td>-->
                            <td>{{ $emp->contact_email }}</td>
                            <td>{{ $emp->address }}</td>

                            <td>
                                @if($emp->is_active)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>

                            <td>

                                {{-- BOTÓN EDITAR --}}
                                <a href="{{ route('admin.config.employees.edit', $emp) }}" class="btn btn-sm btn-primary mb-1">
                                    Editar
                                </a>

                                {{-- BOTÓN ACTIVAR / DESACTIVAR --}}
                                <form action="{{ route('admin.config.employees.toggle', $emp) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')

                                    @if($emp->is_active)
                                        <button class="btn btn-sm btn-warning">
                                            Desactivar
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-success">
                                            Activar
                                        </button>
                                    @endif
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        {{-- ======================= --}}
        {{-- VISTA MÓVIL (CARDS) --}}
        {{-- ======================= --}}
        <div class="mobile-card">
            @foreach ($employees as $emp)
                <div class="card mb-3 shadow-sm border-0" style="border-radius: 14px;">
                    <div class="card-body">

                        @if($admin)
                            <div class="card mb-3 shadow-sm border-0" style="border-radius:14px;background:#f8f9fa;">
                                <div class="card-body">
                                    <h6 class="fw-bold">
                                        <i class="fa-solid fa-user-shield me-1"></i>
                                        Administrador
                                    </h6>

                                    <div class="small mt-2">
                                        <div><strong>Nombre:</strong> {{ $admin->full_name }}</div>
                                        <div><strong>Email:</strong> {{ $admin->email }}</div>
                                        <div><strong>DNI:</strong> {{ $admin->dni }}</div>
                                    </div>

                                    <a href="{{ route('admin.config.employees.edit', $admin) }}"
                                    class="btn btn-primary btn-sm w-100 mt-3">
                                        Editar Perfil
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center mb-3">
                            <h6 class="fw-bold m-0">{{ $emp->full_name }}</h6>
                        </div>

                        <div class="small mb-3">
                            <div><strong>Email:</strong> {{ $emp->email }}</div>
                            <div><strong>DNI:</strong> {{ $emp->dni }}</div>
                            <div><strong>Teléfono:</strong> {{ $emp->phone }}</div>
                            <div><strong>Localidad:</strong> {{ $emp->locality }}</div>
                            <!--<div><strong>WhatsApp:</strong> {{ $emp->whatsapp }}</div>
                            <div><strong>Llamadas:</strong> {{ $emp->call_phone }}</div>-->
                            <div><strong>Email contacto:</strong> {{ $emp->contact_email }}</div>
                            <div><strong>Dirección:</strong> {{ $emp->address }}</div>
                            <div class="mt-2">
                                <strong>Estado:</strong>
                                @if($emp->is_active)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2">

                            {{-- EDITAR --}}
                            <a href="{{ route('admin.config.employees.edit', $emp) }}"
                                class="btn btn-primary btn-sm w-100">
                                Editar
                            </a>

                            {{-- ACTIVAR / DESACTIVAR --}}
                            <form action="{{ route('admin.config.employees.toggle', $emp) }}"
                                  method="POST" class="w-100">
                                @csrf
                                @method('PUT')
                                @if($emp->is_active)
                                    <button class="btn btn-warning btn-sm w-100">Desactivar</button>
                                @else
                                    <button class="btn btn-success btn-sm w-100">Activar</button>
                                @endif
                            </form>

                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>

<style>
@media (max-width: 768px) {
    .desktop-table { display: none !important; }
    .mobile-card { display: block !important; }
}
@media (min-width: 769px) {
    .mobile-card { display: none !important; }
}
</style>

@endsection
