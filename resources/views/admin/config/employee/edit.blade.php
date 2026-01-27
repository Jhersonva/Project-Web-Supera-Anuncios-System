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

            <div class="row g-4">

                {{-- NOMBRE / EMAIL --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre completo</label>
                    <input type="text" name="full_name" class="form-control"
                        value="{{ $employee->full_name }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Email
                        <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email" class="form-control"
                        value="{{ $employee->email }}" required>
                    <small class="text-muted">
                        Este email será utilizado para ingresar al sistema
                    </small>
                </div>


                {{-- DNI / LOCALIDAD / WHATSAPP --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">DNI</label>
                    <input
                        type="text"
                        name="dni"
                        class="form-control"
                        value="{{ $employee->dni }}"
                        inputmode="numeric"
                        pattern="[0-9]{8}"
                        maxlength="8"
                        placeholder="XXXXXXXX"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Localidad</label>
                    <input type="text" name="locality" class="form-control"
                        value="{{ $employee->locality }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">WhatsApp</label>
                    <input
                        type="text"
                        name="whatsapp"
                        class="form-control"
                        value="{{ $employee->whatsapp }}"
                        inputmode="numeric"
                        pattern="[0-9]{9}"
                        maxlength="9"
                        placeholder="9XXXXXXXX"
                    >
                </div>

                {{-- LLAMADAS --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Número de llamadas</label>
                    <input
                        type="text"
                        name="call_phone"
                        class="form-control"
                        value="{{ $employee->call_phone }}"
                        inputmode="numeric"
                        pattern="[0-9]{9}"
                        maxlength="9"
                        placeholder="9XXXXXXXX"
                    >
                </div>

                 {{-- ROL DEL USUARIO --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Rol del usuario</label>
                    <select name="role_id" class="form-select" required 
                        @if(auth()->user()->role_id !== 1) disabled @endif>
                        <option value="">Seleccione un rol</option>
                        <option value="1" {{ $employee->role_id == 1 ? 'selected' : '' }}>Administrador</option>
                        <option value="3" {{ $employee->role_id == 3 ? 'selected' : '' }}>Empleado</option>
                    </select>
                    @if(auth()->user()->role_id !== 1)
                        <small class="text-muted">Solo los administradores pueden cambiar el rol</small>
                    @endif
                </div>

                {{-- CONTRASEÑA --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Nueva contraseña
                        <small class="text-muted">(opcional)</small>
                    </label>
                    <input type="password" name="password" class="form-control">
                    <small class="text-muted">
                        Si se modifica, esta será la nueva contraseña de acceso
                    </small>
                </div>

                {{-- DIRECCIÓN --}}
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Dirección</label>
                    <input type="text" name="address" class="form-control"
                        value="{{ $employee->address }}">
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

<script>
// Validación: solo números y longitud exacta
document.querySelectorAll(
    'input[name="dni"], input[name="ruc"], input[name="call_phone"], input[name="whatsapp"]'
).forEach(input => {

    input.addEventListener('input', function () {
        let max = 0;

        if (this.name === 'dni') max = 8;
        if (this.name === 'ruc') max = 11;
        if (this.name === 'call_phone') max = 9;
        if (this.name === 'whatsapp') max = 9;

        this.value = this.value
            .replace(/[^0-9]/g, '')
            .slice(0, max);
    });

});
</script>

@endsection