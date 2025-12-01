@extends('layouts.app')

@section('title', 'Gestión de Empleados')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .card-employee {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: .2s ease-in-out;
    }
    .card-employee:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 22px rgba(0,0,0,0.12);
    }
    .profile-icon {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: #ffc107;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff;
        font-size: 22px;
    }
</style>

<div class="container mt-4 mb-5">

    {{-- IZQUIERDA: BOTÓN VOLVER --}}
    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold text-center mb-2">Gestión de Empleados</h4>
    <p class="text-muted text-center mb-4">
        Administrar los usuarios que cuentan con rol de empleado.
    </p>

    @if($employees->count() == 0)
        <div class="alert alert-warning text-center">
            No hay empleados registrados.
        </div>
    @endif

    <div class="row">
        @foreach ($employees as $emp)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card-employee">

                <div class="d-flex align-items-center mb-3">
                    <div class="profile-icon me-3">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold m-0">{{ $emp->full_name }}</h5>
                        <small class="text-muted">{{ $emp->email }}</small>
                    </div>
                </div>

                <p class="m-0"><strong>DNI:</strong> {{ $emp->dni }}</p>
                <p class="m-0"><strong>Teléfono:</strong> {{ $emp->phone }}</p>
                <p class="m-0"><strong>Localidad:</strong> {{ $emp->locality }}</p>

                <hr>

                <p class="m-0"><strong>WhatsApp:</strong> {{ $emp->whatsapp }}</p>
                <p class="m-0"><strong>Llamadas:</strong> {{ $emp->call_phone }}</p>
                <p class="m-0"><strong>Email de contacto:</strong> {{ $emp->contact_email }}</p>

                <hr>

                <p class="m-0"><strong>Dirección:</strong> {{ $emp->address }}</p>

            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
