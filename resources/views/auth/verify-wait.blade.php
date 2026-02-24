@extends('layouts.app')

@section('content')

<div class="container d-flex justify-content-center align-items-center" style="min-height:75vh">

    <div class="card shadow-lg border-0 text-center p-5" style="max-width:520px;width:100%;border-radius:14px">

        <div class="mb-4">
            <div class="bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle"
                 style="width:80px;height:80px;font-size:30px">
                <i class="fa-solid fa-envelope"></i>
            </div>
        </div>

        <h3 class="fw-bold mb-3">Revisa tu correo</h3>

        <p class="text-muted mb-4">
            Te hemos enviado un enlace de verificación para activar tu cuenta.
            <br><br>
            Debes confirmar tu correo antes de comenzar a publicar anuncios.
        </p>

        <div class="alert alert-light border small">
            <strong>SuperaAnuncios</strong> te permite publicar,
            vender y promocionar tus productos de forma rápida y segura.
        </div>

        <p class="text-muted small mt-3">
            Una vez verificado tu correo podrás acceder a tu panel y
            empezar a publicar tus anuncios.
        </p>

        <a href="{{ route('login') }}" class="btn btn-primary mt-3 px-4">
            Ir a iniciar sesión
        </a>

    </div>

</div>

@endsection