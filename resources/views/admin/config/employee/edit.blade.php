@extends('layouts.app')

@section('title', 'Editar Empleado')

@section('content')

<style>
    .profile-card {
        border-radius: 20px;
        padding: 30px;
    }
    .profile-label {
        font-weight: 600;
        font-size: 0.9rem;
    }
</style>

<div class="container mt-4">

    {{-- VOLVER --}}
    <a href="{{ route('admin.config.employees') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold text-center mb-3">Editar Empleado</h4>

    <div class="card shadow-sm profile-card">

        <form action="{{ route('admin.config.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- IMAGEN DE PERFIL -->
            <div class="text-center mb-4">

                <label for="profile_image" class="position-relative d-inline-block" style="cursor:pointer;">

                    <div class="position-relative d-inline-block">

                        <img
                            id="profilePreview"
                            src="{{ $employee->profile_image
                                ? asset($employee->profile_image)
                                : asset('assets/img/profile-image/default-user.png') }}"
                            class="rounded-circle border border-2 border-primary"
                            style="width:120px; height:120px; object-fit:cover;"
                        >

                        {{-- ICONO EDITAR --}}
                        <span
                            class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2"
                            style="transform: translate(20%, 20%);">
                            <i class="fa-solid fa-camera"></i>
                        </span>

                    </div>

                    <input type="file"
                        name="profile_image"
                        id="profile_image"
                        class="d-none"
                        accept="image/*">
                </label>

            </div>


            <!-- GRID 2 COLUMNAS -->
            <div class="row g-3">

                {{-- NOMBRE --}}
                <div class="col-md-6">
                    <label class="profile-label">Nombre completo</label>
                    <input type="text" name="full_name" class="form-control"
                        value="{{ $employee->full_name }}" required>
                </div>

                {{-- EMAIL --}}
                <div class="col-md-6">
                    <label class="profile-label">
                        Correo <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email" class="form-control"
                        value="{{ $employee->email }}" required>
                </div>

                {{-- DNI --}}
                <div class="col-md-6">
                    <label class="profile-label">DNI</label>
                    <input type="text" name="dni" class="form-control"
                        value="{{ $employee->dni }}"
                        inputmode="numeric"
                        pattern="[0-9]{8}"
                        maxlength="8"
                        placeholder="XXXXXXXX"
                        required>
                </div>

                {{-- LOCALIDAD --}}
                <div class="col-md-6">
                    <label class="profile-label">Localidad</label>
                    <input type="text" name="locality" class="form-control"
                        value="{{ $employee->locality }}">
                </div>

                {{-- WHATSAPP --}}
                <div class="col-md-6">
                    <label class="profile-label">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control"
                        value="{{ $employee->whatsapp }}"
                        inputmode="numeric"
                        maxlength="9"
                        placeholder="9XXXXXXXX">
                </div>

                {{-- LLAMADAS --}}
                <div class="col-md-6">
                    <label class="profile-label">Teléfono para llamadas</label>
                    <input type="text" name="call_phone" class="form-control"
                        value="{{ $employee->call_phone }}"
                        inputmode="numeric"
                        maxlength="9"
                        placeholder="9XXXXXXXX">
                </div>

                {{-- ROL --}}
                <div class="col-md-6">
                    <label class="profile-label">Rol del usuario</label>
                    <select name="role_id" class="form-select" required
                        @if(auth()->user()->role_id !== 1) disabled @endif>
                        <option value="">Seleccione un rol</option>
                        <option value="1" {{ $employee->role_id == 1 ? 'selected' : '' }}>Administrador</option>
                        <option value="3" {{ $employee->role_id == 3 ? 'selected' : '' }}>Empleado</option>
                    </select>
                </div>

                {{-- CONTRASEÑA --}}
                <div class="col-md-6">
                    <label class="profile-label">
                        Nueva contraseña
                        <small class="text-muted">(opcional)</small>
                    </label>
                    <input type="password" name="password" class="form-control">
                </div>

                {{-- DIRECCIÓN (OCUPA 2 COLUMNAS) --}}
                <div class="col-md-12">
                    <label class="profile-label">Dirección</label>
                    <input type="text" name="address" class="form-control"
                        value="{{ $employee->address }}">
                </div>

            </div>

            <button class="btn btn-primary w-100 rounded-pill mt-4 py-2">
                Guardar cambios
            </button>

        </form>

    </div>
</div>

<script>
document.querySelectorAll(
    'input[name="dni"], input[name="call_phone"], input[name="whatsapp"]'
).forEach(input => {

    input.addEventListener('input', function () {
        let max = 0;

        if (this.name === 'dni') max = 8;
        if (this.name === 'call_phone') max = 9;
        if (this.name === 'whatsapp') max = 9;

        this.value = this.value.replace(/[^0-9]/g, '').slice(0, max);
    });

});

document.getElementById('profile_image').addEventListener('change', function (e) {

    const file = e.target.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Selecciona una imagen válida');
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('profilePreview').src = e.target.result;
    };

    reader.readAsDataURL(file);
});
</script>

@endsection