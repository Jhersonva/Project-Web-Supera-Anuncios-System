@extends('layouts.app')

@section('title', 'Editar Empleado')

@section('content')

<div class="container mt-5 mb-5">

    {{-- VOLVER --}}
    <a href="{{ route('admin.config.employees') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold text-center mb-2">Editar Empleado</h4>
    <p class="text-muted text-center mb-4">
        Modifique los datos del empleado seleccionado.
    </p>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

        <form action="{{ route('admin.config.employees.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre Completo</label>
                    <input type="text" name="full_name" class="form-control" value="{{ $employee->full_name }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $employee->email }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">DNI</label>
                    <input type="text" name="dni" maxlength="8" class="form-control" value="{{ $employee->dni }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="phone" class="form-control" value="{{ $employee->phone }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Localidad</label>
                    <input type="text" name="locality" class="form-control" value="{{ $employee->locality }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control" value="{{ $employee->whatsapp }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Número de llamadas</label>
                    <input type="text" name="call_phone" class="form-control" value="{{ $employee->call_phone }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email de contacto</label>
                    <input type="email" name="contact_email" class="form-control" value="{{ $employee->contact_email }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Dirección</label>
                    <input type="text" name="address" class="form-control" value="{{ $employee->address }}">
                </div>

            </div>

            <div class="text-end mt-4">
                <button class="btn btn-primary px-4">
                    <i class="fa-solid fa-save me-2"></i> Guardar Cambios
                </button>
            </div>

        </form>

    </div>

</div>

@endsection