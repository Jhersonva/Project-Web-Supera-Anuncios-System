@extends('layouts.app')

@section('title', 'Métodos de Pago')

@section('content')

<div class="container mt-5 mb-5">

    {{-- BOTÓN VOLVER --}}
    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Métodos de Pago</h4>
    <p class="text-secondary text-center mb-4">
        Administración completa de los métodos de pago disponibles.
    </p>

    <!-- CARD CONTENEDOR PRINCIPAL -->
    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary text-white p-3 rounded-circle me-3"
                style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-money-bill-transfer fa-lg"></i>
            </div>

            <div>
                <h5 class="fw-bold m-0">Gestión de Métodos de Pago</h5>
                <small class="text-muted">Configura los métodos disponibles para recargas</small>
            </div>
        </div>

        {{-- BOTÓN CREAR --}}
        <div class="mb-4 text-end">
            <a href="{{ route('admin.config.payment_methods.create') }}" class="btn btn-success">
                <i class="fa-solid fa-plus me-2"></i>Nuevo Método de Pago
            </a>
        </div>

        @if ($methods->isEmpty())
            <p class="text-center text-muted">No existen métodos de pago registrados.</p>
        @else

            {{-- ========================= --}}
            {{-- TABLA ESCRITORIO --}}
            {{-- ========================= --}}
            <div class="table-responsive desktop-table">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Número</th>
                            <th>Cuenta</th>
                            <th>CCI</th>
                            <th>QR</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($methods as $method)
                            <tr>
                                <td class="fw-semibold">{{ $method->nombre }}</td>
                                <td>{{ $method->tipo ?? '-' }}</td>
                                <td>{{ $method->numero ?? '-' }}</td>
                                <td>{{ $method->cuenta ?? '-' }}</td>
                                <td>{{ $method->cci ?? '-' }}</td>

                                <td>
                                    @if ($method->qr)
                                        <img src="{{ asset($method->qr) }}" width="45" height="45"
                                            class="rounded shadow-sm" style="object-fit:cover;">
                                    @else
                                        <span class="text-muted">Sin QR</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($method->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Desactivado</span>
                                    @endif
                                </td>

                                <td>

                                    {{-- EDITAR --}}
                                    <a href="{{ route('admin.config.payment_methods.edit', $method->id) }}"
                                        class="btn btn-sm btn-primary mb-1">
                                        Editar
                                    </a>

                                    {{-- ELIMINAR --}}
                                    <form action="{{ route('admin.config.payment_methods.delete', $method->id) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Seguro de eliminar este método de pago?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            {{-- ========================= --}}
            {{-- VISTA MÓVIL (CARDS) --}}
            {{-- ========================= --}}
            <div class="mobile-card">

                @foreach ($methods as $method)
                    <div class="card mb-3 shadow-sm border-0" style="border-radius: 14px;">
                        <div class="card-body">

                            <h6 class="fw-bold mb-1">{{ $method->nombre }}</h6>
                            <p class="text-muted mb-2">{{ $method->tipo ?? 'Sin tipo' }}</p>

                            <div class="small mb-3">
                                <div><strong>Número:</strong> {{ $method->numero ?? '-' }}</div>
                                <div><strong>Cuenta:</strong> {{ $method->cuenta ?? '-' }}</div>
                                <div><strong>CCI:</strong> {{ $method->cci ?? '-' }}</div>

                                <div class="mt-2">
                                    <strong>QR:</strong><br>
                                    @if ($method->qr)
                                        <img src="{{ asset($method->qr) }}" width="100"
                                            class="rounded shadow-sm mt-1">
                                    @else
                                        <span class="text-muted">No disponible</span>
                                    @endif
                                </div>

                                <div class="mt-2">
                                    <strong>Estado:</strong>
                                    @if ($method->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Desactivado</span>
                                    @endif
                                </div>
                            </div>

                            {{-- ACCIONES --}}
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.config.payment_methods.edit', $method->id) }}"
                                    class="btn btn-primary btn-sm w-100">
                                    Editar
                                </a>

                                <form action="{{ route('admin.config.payment_methods.delete', $method->id) }}"
                                    method="POST" class="w-100"
                                    onsubmit="return confirm('¿Eliminar este método?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm w-100">Eliminar</button>
                                </form>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>

        @endif

    </div>
</div>

<style>
@media (max-width: 768px) {
    .desktop-table {
        display: none !important;
    }
    .mobile-card {
        display: block !important;
    }
}
@media (min-width: 769px) {
    .mobile-card {
        display: none !important;
    }
}
</style>

@endsection
