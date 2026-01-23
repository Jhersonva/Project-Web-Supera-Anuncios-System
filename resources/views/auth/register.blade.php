<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crear Cuenta</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="icon" href="{{ system_logo() }}" type="image/png">
</head>
<body>

<a href="{{ route('login') }}" class="back-floating">
  <i class="fa-solid fa-arrow-left"></i>
</a>

<div class="auth-wrapper">
  <div class="auth-container">

    <h2 class="fw-bold mb-2 text-center">Crear cuenta</h2>

    <p class="text-center text-muted small mb-4">
      Únete a nuestra comunidad para comprar y vender artículos
    </p>

    <div class="text-center mb-3">
      {{--
      <img src="{{ asset('assets/icons/logo.jpg') }}" width="80" class="rounded-3 mb-2">
      <h6 class="fw-bold">VIVA ANUNCIOS!</h6>
      --}}
      <img src="{{ system_logo() }}" alt="{{ system_company_name() }}" width="90" class="rounded-3 mb-2">
    </div>

    <form id="registerForm" class="w-100" method="POST" action="{{ route('auth.register') }}">
    @csrf
      <div id="registerError" class="alert alert-danger d-none py-2 small"></div>
      <div id="registerSuccess" class="alert alert-success d-none py-2 small"></div>

      <label class="form-label small fw-semibold">Tipo de cuenta</label>
      <div class="mb-3">
          <select id="accountType" name="account_type" class="form-select" required>
              <option value="">Selecciona un tipo</option>
              <option value="person">Persona Natural</option>
              <option value="business">Empresa / Negocio</option>
          </select>
      </div>

      <!-- Nombres -->
      <div id="personFields" class="d-none">
          <label class="form-label small fw-semibold">Nombres Completos</label>
          <div class="input-group auth-input mb-3">
              <span class="input-group-text bg-white">
                  <i class="fa-solid fa-user"></i>
              </span>
              <input
                  type="text"
                  name="full_name"
                  class="form-control"
                  placeholder="Ej. Juan Carlos Pérez"
              >
          </div>

          <label class="form-label small fw-semibold">DNI</label>
            <div class="input-group auth-input mb-3">
                <span class="input-group-text bg-white">
                    <i class="fa-solid fa-id-card"></i>
                </span>
                <input
                    type="text"
                    name="dni"
                    class="form-control"
                    inputmode="numeric"
                    pattern="[0-9]{8}"
                    maxlength="8"
                    placeholder="Ingrese su DNI"
                >
            </div>

      </div>

      <div id="businessFields" class="d-none">
          <label class="form-label small fw-semibold">Razón Social</label>
          <div class="input-group auth-input mb-3">
              <span class="input-group-text bg-white">
                  <i class="fa-solid fa-building"></i>
              </span>
              <input
                  type="text"
                  name="company_reason"
                  class="form-control"
                  placeholder="Ej. Comercial ABC S.A.C."
              >
          </div>

          <label class="form-label small fw-semibold">RUC</label>
          <div class="input-group auth-input mb-3">
                <span class="input-group-text bg-white">
                    <i class="fa-solid fa-file-invoice"></i>
                </span>
                <input
                    type="text"
                    name="ruc"
                    class="form-control"
                    required
                    inputmode="numeric"
                    pattern="[0-9]{11}"
                    maxlength="11"
                    placeholder="Ingrese su RUC"
                >
            </div>
      </div>

      <!-- Celular -->
      <label class="form-label small fw-semibold">Número de Celular</label>
      <div class="input-group auth-input mb-3">
            <span class="input-group-text bg-white">
                <i class="fa-solid fa-phone"></i>
            </span>
            <input
                type="text"
                id="regPhone"
                name="call_phone"
                class="form-control"
                required
                inputmode="numeric"
                pattern="[0-9]{9}"
                maxlength="9"
                placeholder="9XXXXXXXX"
            >
        </div>

      <!-- Localidad -->
      <label class="form-label small fw-semibold">Localidad</label>
      <div class="input-group auth-input mb-3">
        <span class="input-group-text bg-white"><i class="fa-solid fa-map-location-dot"></i></span>
        <input type="text" id="regLocalidad" name="locality" class="form-control" placeholder="Ciudad / Distrito" required>
      </div>

      <!-- Correo -->
      <label class="form-label small fw-semibold">Correo electrónico</label>
      <div class="input-group auth-input mb-1">
        <span class="input-group-text bg-white"><i class="fa-solid fa-envelope"></i></span>
        <input type="email" id="regEmail" name="email" class="form-control" placeholder="nombre@ejemplo.com" required>
      </div>

      <!-- Contraseña -->
      <label class="form-label small fw-semibold mt-3">Contraseña</label>
      <div class="input-group auth-input mb-1">
        <span class="input-group-text bg-white"><i class="fa-solid fa-lock"></i></span>
        <input type="password" id="regPass" name="password" class="form-control" placeholder="Crea tu contraseña" required>
        <span class="input-group-text bg-white toggle-pass"><i class="fa-solid fa-eye"></i></span>
      </div>

      <form>
        <div class="form-check mt-4">
            <input
            class="form-check-input"
            type="checkbox"
            id="acceptTerms"
            name="accept_terms"
            value="1"
            required
            >

            <label class="form-check-label small" for="acceptTerms">
            Acepto los
            <a href="javascript:void(0)" id="openTerms" class="fw-semibold">
                términos y condiciones
            </a>
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mt-4">
            Registrarse
        </button>
      </form>

      <p class="mt-3 text-center">
        ¿Ya tienes una cuenta?
        <a href="{{ route('login') }}" class="fw-semibold">Inicia sesión</a>
      </p>

    </form>

  </div>
</div>

<script src="{{ asset('js/auth.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

// Validacion de campos dni, ruc y llamadas
document.querySelectorAll('input[name="dni"], input[name="ruc"], input[name="call_phone"]').forEach(input => {
    input.addEventListener('input', function () {
        let max = 0;
        if (this.name === 'dni') max = 8;
        if (this.name === 'ruc') max = 11;
        if (this.name === 'call_phone') max = 9;

        this.value = this.value.replace(/[^0-9]/g, '').slice(0, max);
    });
});

document.getElementById('accountType').addEventListener('change', function () {

    const person = document.getElementById('personFields');
    const business = document.getElementById('businessFields');

    person.classList.add('d-none');
    business.classList.add('d-none');

    if (this.value === 'person') {
        person.classList.remove('d-none');
    }

    if (this.value === 'business') {
        business.classList.remove('d-none');
    }
});

document.addEventListener('DOMContentLoaded', function () {

    const checkbox = document.getElementById('acceptTerms');
    const openTerms = document.getElementById('openTerms');

    function showTermsModal() {
        Swal.fire({
            title: '<span style="font-size:22px;font-weight:600;">Términos y Condiciones</span>',
            width: 700,
            padding: '1.5rem',
            backdrop: 'rgba(0,0,0,0.65)',
            html: `
                <div style="
                    text-align:left;
                    max-height:420px;
                    overflow-y:auto;
                    padding-right:10px;
                    line-height:1.6;
                    font-size:14px;
                    color:#333;
                ">
                    {!! nl2br(e($policy->privacy_text)) !!}
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Rechazar',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                checkbox.checked = true;
            } else {
                checkbox.checked = false;
            }
        });
    }

    openTerms.addEventListener('click', showTermsModal);

});

document.getElementById('registerForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    // Validación rápida frontend
    if (!document.getElementById('acceptTerms').checked) {
        Swal.fire({
            icon: 'warning',
            title: 'Acepta los términos',
            text: 'Debes aceptar los términos y condiciones.',
            confirmButtonColor: '#0d6efd'
        });
        return;
    }

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async response => {
        const data = await response.json();

        if (!response.ok) {
            throw data;
        }

        // REGISTRO EXITOSO
        Swal.fire({
            icon: 'success',
            title: 'Cuenta creada',
            text: 'Tu cuenta fue creada correctamente.',
            confirmButtonColor: '#0d6efd'
        }).then(() => {
            window.location.href = data.redirect ?? '/';
        });

    })
    .catch(error => {

        // ERRORES DE VALIDACIÓN
        if (error.errors) {

            const messages = Object.values(error.errors)
                .flat()
                .join('<br>');

            Swal.fire({
                icon: 'error',
                title: 'Revisa los datos',
                html: messages,
                confirmButtonColor: '#dc3545'
            });

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message ?? 'Error inesperado',
                confirmButtonColor: '#dc3545'
            });
        }
    });
});
</script>

</body>
</html>
