@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@section('content')

<div class="container mt-5 mb-5">

    {{-- IZQUIERDA: BOTÓN VOLVER --}}
    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Gestión de Clientes</h4>
    <p class="text-secondary text-center mb-4">
        Lista completa de clientes registrados en el sistema.
    </p>

    <!-- CARD CONTENEDOR -->
    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary text-white p-3 rounded-circle me-3"
                style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-users fa-lg"></i>
            </div>

            <div>
                <h5 class="fw-bold m-0">Clientes Registrados</h5>
                <small class="text-muted">Usuarios con rol publicitario (advertising_user)</small>
            </div>
        </div>

        @if ($clients->isEmpty())
            <p class="text-center text-muted">No existen clientes registrados.</p>
        @else
            <div class="table-responsive">
                @if ($clients->isEmpty())
                    <p class="text-center text-muted">No existen clientes registrados.</p>
                @else

                    {{-- ========================= --}}
                    {{-- VISTA ESCRITORIO (TABLA) --}}
                    {{-- ========================= --}}
                    <div class="table-responsive desktop-table">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Localidad</th>
                                    <th>DNI</th>
                                    <th>Registrado</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($clients as $client)
                                    <tr>
                                        <td class="fw-semibold">{{ $client->full_name }}</td>
                                        <td>{{ $client->email }}</td>
                                        <td>{{ $client->phone }}</td>
                                        <td>{{ $client->locality }}</td>
                                        <td>{{ $client->dni }}</td>
                                        <td>{{ $client->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($client->is_active)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Desactivado</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.config.clients.edit', $client) }}" class="btn btn-sm btn-primary mb-1">
                                                Editar
                                            </a>

                                            <form action="{{ route('admin.config.clients.toggle', $client) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')

                                                @if($client->is_active)
                                                    <button class="btn btn-sm btn-warning">Desactivar</button>
                                                @else
                                                    <button class="btn btn-sm btn-success">Activar</button>
                                                @endif
                                            </form>
                                            @if(!$client->is_verified)
                                            <button
                                                class="btn btn-sm btn-success"
                                                onclick="verifyUser({{ $client->id }})">
                                                <i class="fa-solid fa-circle-check"></i> Verificar
                                            </button>
                                            @else
                                            <span class="badge bg-primary">
                                                <i class="fa-solid fa-shield-check"></i> Verificado
                                            </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>


                    {{-- VISTA MÓVIL (CARDS) --}}
                    <div class="mobile-card">
                        @foreach ($clients as $client)
                            <div class="card mb-3 shadow-sm border-0" style="border-radius: 14px;">
                                <div class="card-body">

                                    <h6 class="fw-bold mb-1">{{ $client->full_name }}</h6>
                                    <p class="text-muted mb-2">{{ $client->email }}</p>

                                    <div class="small mb-3">
                                        <div><strong>Teléfono:</strong> {{ $client->phone }}</div>
                                        <div><strong>Localidad:</strong> {{ $client->locality }}</div>
                                        <div><strong>DNI:</strong> {{ $client->dni }}</div>
                                        <div><strong>Registrado:</strong> {{ $client->created_at->format('d/m/Y') }}</div>
                                        <div>
                                            <strong>Estado:</strong>
                                            @if($client->is_active)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Desactivado</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.config.clients.edit', $client) }}"
                                        class="btn btn-sm btn-primary mb-1">
                                            Editar
                                        </a>

                                        <form action="{{ route('admin.config.clients.toggle', $client) }}"
                                            method="POST" class="w-100">
                                            @csrf
                                            @method('PUT')

                                            @if($client->is_active)
                                                <button class="btn btn-warning btn-sm w-100">Desactivar</button>
                                            @else
                                                <button class="btn btn-success btn-sm w-100">Activar</button>
                                            @endif
                                        </form>

                                        @if(!$client->is_verified)
                                            <button
                                                class="btn btn-sm btn-success"
                                                onclick="verifyUser({{ $client->id }})">
                                                <i class="fa-solid fa-circle-check"></i> Verificar
                                            </button>
                                            @else
                                            <span class="badge bg-primary">
                                                <i class="fa-solid fa-shield-check"></i> Verificado
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                @endif
            </div>
        @endif
    </div>
</div>

<script>
function verifyUser(userId) {
    Swal.fire({
        title: '¿Verificar usuario?',
        text: 'Al activar esta acción, este usuario será marcado como verificado, confiable y visible con insignia.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, verificar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {

            fetch(`/admin/config/clients/${userId}/verify`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Verificado',
                        text: data.message,
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            });
        }
    });
}
</script>

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
