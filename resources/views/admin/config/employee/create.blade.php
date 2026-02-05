@extends('layouts.app')

@section('title', 'Crear Empleado')

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

<div class="container mt-4 mb-5">

    {{-- VOLVER --}}
    <a href="{{ route('admin.config.employees') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold text-center mb-3">Registrar Empleado</h4>
    <p class="text-muted text-center mb-4">
        Complete los datos del empleado con su rol asignado
    </p>

    <div class="card shadow-sm profile-card">

        <form action="{{ route('admin.config.employees.store') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            <!-- IMAGEN DE PERFIL -->
            <div class="text-center mb-4">

                <label for="profile_image"
                       class="position-relative d-inline-block"
                       style="cursor:pointer;">

                    <div class="position-relative d-inline-block">

                        <img
                            id="profilePreview"
                            src="{{ asset('assets/img/profile-image/default-user.png') }}"
                            class="rounded-circle border border-2 border-primary"
                            style="width:120px;height:120px;object-fit:cover;"
                        >

                        {{-- ICONO CAMARA --}}
                        <span
                            class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2"
                            style="transform: translate(20%, 20%);">
                            <i class="fa-solid fa-camera"></i>
                        </span>

                    </div>

                    <input
                        type="file"
                        name="profile_image"
                        id="profile_image"
                        class="d-none"
                        accept="image/*"
                    >
                </label>

            </div>

            <!-- GRID 2 COLUMNAS -->
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="profile-label">Nombre completo</label>
                    <input type="text"
                           name="full_name"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Correo de acceso</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="profile-label">DNI</label>
                    <input type="text"
                           name="dni"
                           class="form-control"
                           inputmode="numeric"
                           maxlength="8"
                           placeholder="XXXXXXXX"
                           required>
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Localidad</label>
                    <input type="text"
                           name="locality"
                           class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="profile-label">WhatsApp</label>
                    <input type="text"
                           name="whatsapp"
                           class="form-control"
                           inputmode="numeric"
                           maxlength="9"
                           placeholder="9XXXXXXXX">
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Teléfono para llamadas</label>
                    <input type="text"
                           name="call_phone"
                           class="form-control"
                           inputmode="numeric"
                           maxlength="9"
                           placeholder="9XXXXXXXX">
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Rol del usuario</label>
                    <select name="role_id" class="form-select" required>
                        <option value="">Seleccione un rol</option>
                        <option value="1">Administrador</option>
                        <option value="3">Empleado</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Contraseña</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-12">
                    <label class="profile-label">Dirección</label>
                    <input type="text"
                           name="address"
                           class="form-control">
                </div>

            </div>

            <button class="btn btn-primary w-100 rounded-pill mt-4 py-2">
                <i class="fa-solid fa-check me-2"></i>
                Registrar Empleado
            </button>

        </form>

    </div>
</div>

<script>
/* ==========================
   PREVIEW IMAGEN PERFIL
========================== */
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

/* ==========================
   SOLO NUMEROS
========================== */
document.querySelectorAll(
    'input[name="dni"], input[name="call_phone"], input[name="whatsapp"]'
).forEach(input => {

    input.addEventListener('input', function () {

        let max = 9;
        if (this.name === 'dni') max = 8;

        this.value = this.value
            .replace(/[^0-9]/g, '')
            .slice(0, max);
    });

});
</script>

@endsection
