<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="icon" href="{{ system_logo() }}" type="image/png">
</head>
<body>

<a href="{{ route('home') }}" class="back-floating">
  <i class="fa-solid fa-arrow-left"></i>
</a>

<div class="auth-wrapper">
  <div class="auth-container">

    <h2 class="fw-bold mb-3 text-center">Bienvenido de nuevo</h2>

    <div class="text-center mb-3">
      {{--
      
      <img src="{{ asset('assets/icons/logo.jpg') }}" width="80" class="rounded-3 mb-2">
      <h6 class="fw-bold">VIVA ANUNCIOS!</h6>-->
      --}}
      <img src="{{ system_logo() }}" alt="{{ system_company_name() }}" width="90" class="rounded-3 mb-2">
    </div>

    <form id="loginForm" class="w-100">

      <div id="loginError" class="alert alert-danger d-none py-2 small"></div>

      <!-- Email -->
      <div class="input-group auth-input mb-3">
        <span class="input-group-text bg-white">
          <i class="fa-solid fa-envelope"></i>
        </span>
        <input type="email" id="loginEmail" class="form-control" placeholder="Correo electrónico" required>
      </div>

      <!-- Password -->
      <div class="input-group auth-input mb-3">
        <span class="input-group-text bg-white">
          <i class="fa-solid fa-lock"></i>
        </span>
        <input type="password" id="loginPass" class="form-control" placeholder="Contraseña" required>
        <span class="input-group-text bg-white toggle-pass"><i class="fa-solid fa-eye"></i></span>
      </div>

      <button class="btn btn-primary w-100 py-2 fw-bold">Iniciar sesión</button>

      <p class="mt-3 text-center">
        ¿No tienes una cuenta?
        <a href="{{ route('register') }}" class="fw-semibold">Crear Cuenta</a>
      </p>

    </form>

  </div>
</div>

<script src="{{ asset('js/auth.js') }}"></script>

@if(session('showPrivacy'))
<script>
    sessionStorage.setItem('showPrivacy', '1');
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    if (sessionStorage.getItem('showPrivacy') === '1') {
        sessionStorage.removeItem('showPrivacy');

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
                    <div style="margin-bottom:16px;">
                        {!! nl2br(e($policy->privacy_text)) !!}
                    </div>
                    <hr style="margin:16px 0">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Rechazar',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            reverseButtons: true,
            focusConfirm: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'privacy-modal',
                confirmButton: 'privacy-btn-confirm',
                cancelButton: 'privacy-btn-cancel'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('acceptForm').submit();
            } else {
                document.getElementById('rejectForm').submit();
            }
        });
    }
});
</script>

<form id="acceptForm" method="POST" action="{{ route('privacy-policy.accept') }}">
    @csrf
</form>

<form id="rejectForm" method="POST" action="{{ route('privacy-policy.reject') }}">
    @csrf
</form>


</body>
</html>
