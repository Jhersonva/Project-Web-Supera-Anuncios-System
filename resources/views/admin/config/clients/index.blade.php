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
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Localidad</th>
                            <th>DNI</th>
                            <th>Registrado</th>
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
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        @endif

    </div>

</div>

@endsection
