@extends('layouts.app')

@section('title', 'Crear Empleado')

@section('content')

<div class="container mt-5 mb-5">

    {{-- VOLVER --}}
    <a href="{{ route('admin.config.employees') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold text-center mb-2">Registrar Nuevo Empleado</h4>
    <p class="text-muted text-center mb-4">
        Complete los datos del empleado con rol asignado.
    </p>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

        <form action="{{ route('admin.config.employees.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre Completo</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">DNI</label>
                    <input type="text" name="dni" maxlength="8" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Localidad</label>
                    <input type="text" name="locality" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Número de llamadas</label>
                    <input type="text" name="call_phone" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email de contacto</label>
                    <input type="email" name="contact_email" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Dirección</label>
                    <input type="text" name="address" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

            </div>

            <div class="text-end mt-4">
                <button class="btn btn-success px-4">
                    <i class="fa-solid fa-check me-2"></i> Registrar Empleado
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
