@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')

<style>
    .profile-icon {
        font-size: 60px;
        color: #0d6efd;
    }
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

    <h4 class="fw-bold text-center mb-3">Mi Perfil</h4>

    <div class="card shadow-sm profile-card">


        <form action="{{ route('profile.update') }}"method="POST"enctype="multipart/form-data">
            @csrf

            <!-- Ícono centrado -->
            <div class="text-center mb-4">

                <label for="profile_image" class="position-relative d-inline-block" style="cursor:pointer;">
                    <img
                        id="profilePreview"
                        src="{{ $user->profile_image 
                            ? asset($user->profile_image) 
                            : asset('assets/img/profile-image/default-user.png') }}"
                        class="rounded-circle border border-2 border-danger"
                        style="width:120px; height:120px; object-fit:cover;"
                    >

                    <!-- Icono editar -->
                    <span class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2">
                        <i class="fa-solid fa-camera"></i>
                    </span>
                </label>

                <input type="file"
                    name="profile_image"
                    id="profile_image"
                    class="d-none"
                    accept="image/*">
            </div>

            <!-- GRID RESPONSVIO 2 COLUMNAS -->
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="profile-label">Nombre completo</label>
                    <input type="text" class="form-control" name="full_name"
                        value="{{ old('full_name', $user->full_name) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Correo</label>
                    <input type="email" class="form-control" name="email"
                        value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="profile-label">DNI</label>
                    <input type="text" class="form-control" name="dni"
                        value="{{ old('dni', $user->dni) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Nueva contraseña (opcional)</label>
                    <input type="password" class="form-control" name="password" placeholder="••••••">
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Teléfono</label>
                    <input type="text" class="form-control" name="phone"
                        value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Localidad</label>
                    <input type="text" class="form-control" name="locality"
                        value="{{ old('locality', $user->locality) }}">
                </div>

                <div class="col-md-6">
                    <label class="profile-label">WhatsApp</label>
                    <input type="text" class="form-control" name="whatsapp"
                        value="{{ old('whatsapp', $user->whatsapp) }}">
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Teléfono para llamadas</label>
                    <input type="text" class="form-control" name="call_phone"
                        value="{{ old('call_phone', $user->call_phone) }}">
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Correo de contacto</label>
                    <input type="email" class="form-control" name="contact_email"
                        value="{{ old('contact_email', $user->contact_email) }}">
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Dirección</label>
                    <input type="text" class="form-control" name="address"
                        value="{{ old('address', $user->address) }}">
                </div>

            </div>

            <hr class="my-4">

            <!-- DATOS SOLO LECTURA -->
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="profile-label fw-bold">Billetera virtual</label>
                    <input type="text" class="form-control"
                        value="S/. {{ number_format($user->virtual_wallet, 2) }}" disabled>
                </div>

                <div class="col-md-6">
                    <label class="profile-label fw-bold">Estado</label>
                    <input type="text" class="form-control"
                        value="{{ $user->is_active ? 'Activo' : 'Inactivo' }}" disabled>
                </div>

            </div>

            <button class="btn btn-primary w-100 rounded-pill mt-4 py-2">
                Guardar cambios
            </button>

        </form>
    </div>
</div>

<script>
document.getElementById('profile_image').addEventListener('change', function (e) {

    const file = e.target.files[0];
    if (!file) return;

    // Validar que sea imagen
    if (!file.type.startsWith('image/')) {
        alert('Selecciona una imagen válida');
        return;
    }

    const reader = new FileReader();

    reader.onload = function (event) {
        document.getElementById('profilePreview').src = event.target.result;
    };

    reader.readAsDataURL(file);
});
</script>
@endsection
