@extends('layouts.app')

@section('title', 'Editar Cliente')

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

    <a href="{{ route('admin.config.clients') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold text-center mb-3">Editar Cliente</h4>

    <div class="card shadow-sm profile-card">

        <form action="{{ route('admin.config.clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- IMAGEN DE PERFIL -->
            <div class="text-center mb-4">

                <label for="profile_image" class="position-relative d-inline-block" style="cursor:pointer;">

                    <div class="position-relative d-inline-block">

                        <img
                            src="{{ $client->profile_image
                                ? asset($client->profile_image)
                                : asset('assets/img/profile-image/default-user.png') }}"
                            class="rounded-circle border border-2 border-danger"
                            style="width:120px; height:120px; object-fit:cover;"
                        >

                        {{-- INSIGNIA VERIFICADO --}}
                        @if($client->is_verified)
                            <img
                                src="{{ asset('assets/img/verified-icon/verified.png') }}"
                                alt="Usuario verificado"
                                title="Usuario verificado"
                                class="position-absolute top-0 end-0"
                                style="
                                    width:52px;
                                    height:52px;
                                    transform: translate(20%, -20%);
                                ">
                        @endif

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

                @if($client->account_type === 'person')
                    <div class="col-md-6">
                        <label class="profile-label">Nombre completo</label>
                        <input type="text" class="form-control" name="full_name"
                            value="{{ old('full_name', $client->full_name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="profile-label">DNI</label>
                        <input
                            type="text"
                            class="form-control"
                            name="dni"
                            value="{{ old('dni', $client->dni) }}"
                            inputmode="numeric"
                            pattern="[0-9]{8}"
                            maxlength="8"
                            placeholder="XXXXXXXX"
                            title="El DNI debe contener exactamente 8 números"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="profile-label">Fecha de nacimiento</label>
                        <input
                            type="date"
                            class="form-control"
                            name="birthdate"
                            value="{{ old('birthdate', optional($client->birthdate)->format('Y-m-d')) }}"
                        >
                    </div>
                @endif

                @if($client->account_type === 'business')
                    <div class="col-md-6">
                        <label class="profile-label">Razón social</label>
                        <input type="text" class="form-control" name="company_reason"
                            value="{{ old('company_reason', $client->company_reason) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="profile-label">RUC</label>
                        <input
                            type="text"
                            class="form-control"
                            name="ruc"
                            value="{{ old('ruc', $client->ruc) }}"
                            inputmode="numeric"
                            pattern="[0-9]{11}"
                            maxlength="11"
                            placeholder="XXXXXXXXXXX"
                            title="El RUC debe contener exactamente 11 números"
                            required
                        >
                    </div>
                @endif

                <div class="col-md-6">
                    <label class="profile-label">Correo</label>
                    <input type="email" class="form-control" name="email"
                        value="{{ old('email', $client->email) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Localidad</label>
                    <input type="text" class="form-control" name="locality"
                        value="{{ old('locality', $client->locality) }}">
                </div>

                <div class="col-md-6">
                    <label class="profile-label">WhatsApp</label>
                    <input
                        type="text"
                        class="form-control"
                        name="whatsapp"
                        value="{{ old('whatsapp', $client->whatsapp) }}"
                        inputmode="numeric"
                        pattern="[0-9]{9}"
                        maxlength="9"
                        placeholder="9XXXXXXXX"
                        title="El número de WhatsApp debe tener 9 dígitos"
                    >
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Teléfono para llamadas</label>
                    <input
                        type="text"
                        class="form-control"
                        name="call_phone"
                        value="{{ old('call_phone', $client->call_phone) }}"
                        inputmode="numeric"
                        pattern="[0-9]{9}"
                        maxlength="9"
                        placeholder="9XXXXXXXX"
                        title="El número de llamadas debe tener 9 dígitos"
                    >
                </div>

                <div class="col-md-6">
                    <label class="profile-label">Dirección</label>
                    <input type="text" class="form-control" name="address"
                        value="{{ old('address', $client->address) }}">
                </div>

            </div>

            <hr class="my-4">

            <!-- SOLO LECTURA -->
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="profile-label fw-bold">Billetera virtual</label>
                    <input type="text" class="form-control"
                        value="S/. {{ number_format($client->virtual_wallet, 2) }}" disabled>
                </div>

                <div class="col-md-6">
                    <label class="profile-label fw-bold">Estado</label>
                    <input type="text" class="form-control"
                        value="{{ $client->is_active ? 'Activo' : 'Inactivo' }}" disabled>
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

<style>
.verified-badge {
    width: 32px;
    height: 32px;
    position: absolute;
    top: 0;
    right: 0;
    transform: translate(20%, -20%);
}

.edit-avatar {
    position: absolute;
    bottom: 0;
    right: 0;
    transform: translate(20%, 20%);
}
</style>
@endsection
