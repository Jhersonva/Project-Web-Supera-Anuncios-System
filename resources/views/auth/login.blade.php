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
</head>
<body>

<a href="{{ route('home') }}" class="back-floating">
  <i class="fa-solid fa-arrow-left"></i>
</a>

<div class="auth-wrapper">
  <div class="auth-container">

    <h2 class="fw-bold mb-3 text-center">Bienvenido de nuevo</h2>

    <div class="text-center mb-3">
      <img src="{{ asset('assets/icons/logo.jpg') }}" width="80" class="rounded-3 mb-2">
      <h6 class="fw-bold">VIVA ANUNCIOS!</h6>
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
</body>
</html>
