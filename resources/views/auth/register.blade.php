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
      <img src="{{ asset('assets/icons/logo.jpg') }}" width="80" class="rounded-3 mb-2">
      <h6 class="fw-bold">VIVA ANUNCIOS!</h6>
    </div>

    <form id="registerForm" class="w-100" method="POST" action="{{ route('auth.register') }}">
    @csrf
      <div id="registerError" class="alert alert-danger d-none py-2 small"></div>
      <div id="registerSuccess" class="alert alert-success d-none py-2 small"></div>

      <!-- Nombres -->
      <label class="form-label small fw-semibold">Nombres Completos</label>
      <div class="input-group auth-input mb-3">
        <span class="input-group-text bg-white"><i class="fa-solid fa-user"></i></span>
        <input type="text" id="regName" name="full_name" class="form-control" placeholder="Introduce tus nombres completos" required>
      </div>

      <!-- DNI -->
      <label class="form-label small fw-semibold">DNI</label>
      <div class="input-group auth-input mb-3">
        <span class="input-group-text bg-white"><i class="fa-solid fa-id-card"></i></span>
        <input type="text" id="regDni" name="dni" class="form-control" placeholder="Documento de identidad" required maxlength="8">
      </div>

      <!-- Celular -->
      <label class="form-label small fw-semibold">Número de Celular</label>
      <div class="input-group auth-input mb-3">
        <span class="input-group-text bg-white"><i class="fa-solid fa-phone"></i></span>
        <input type="text" id="regPhone" name="phone" class="form-control" placeholder="9XXXXXXXX" required maxlength="9">
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

      <button class="btn btn-primary w-100 py-2 fw-bold mt-4">Registrarse</button>

      <p class="mt-3 text-center">
        ¿Ya tienes una cuenta?
        <a href="{{ route('login') }}" class="fw-semibold">Inicia sesión</a>
      </p>

    </form>

  </div>
</div>

<script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
