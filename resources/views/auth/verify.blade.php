@extends('layouts.app')

@section('content')

<div class="container d-flex justify-content-center align-items-center" style="min-height:70vh">

    <div class="card shadow-lg border-0 text-center p-4" style="max-width:520px;width:100%;border-radius:14px">

        @if($status === 'success')

            <div class="mb-3">
                <div class="bg-success text-white d-inline-flex align-items-center justify-content-center rounded-circle"
                     style="width:70px;height:70px;font-size:28px">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>

            <h3 class="fw-bold mb-2">Cuenta verificada</h3>

            <p class="text-muted mb-4">
                Tu cuenta ha sido verificada correctamente.
                Serás redirigido a tu panel en
                <strong id="countdown">5</strong> segundos.
            </p>

            <a href="{{ route('home') }}" class="btn btn-primary px-4">
                Ir ahora al panel
            </a>

        @elseif($status === 'expired')

            <div class="mb-3">
                <div class="bg-warning text-white d-inline-flex align-items-center justify-content-center rounded-circle"
                     style="width:70px;height:70px;font-size:28px">
                    <i class="fa-solid fa-hourglass-end"></i>
                </div>
            </div>

            <h3 class="fw-bold mb-2">Enlace expirado</h3>

            <p class="text-muted mb-4">
                El enlace de verificación ha expirado.
                Puedes solicitar uno nuevo desde tu cuenta.
            </p>

            <a href="{{ route('login') }}" class="btn btn-outline-primary px-4">
                Ir a iniciar sesión
            </a>

        @else

            <div class="mb-3">
                <div class="bg-danger text-white d-inline-flex align-items-center justify-content-center rounded-circle"
                     style="width:70px;height:70px;font-size:28px">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>

            <h3 class="fw-bold mb-2">Enlace inválido</h3>

            <p class="text-muted mb-4">
                Este enlace no es válido o ya fue utilizado.
                Si el problema continúa, contacta con soporte.
            </p>

            <a href="{{ route('home') }}" class="btn btn-outline-primary px-4">
                Volver al inicio
            </a>

        @endif

    </div>

</div>

@if($status === 'success')
<script>
let seconds = 5;
let countdown = document.getElementById('countdown');

let interval = setInterval(() => {
    seconds--;
    countdown.innerText = seconds;

    if(seconds <= 0){
        clearInterval(interval);
        window.location.href = "{{ route('home') }}";
    }
}, 1000);
</script>
@endif

<style>
.success-icon{
animation: pop 0.4s ease;
}

@keyframes pop{
0%{transform:scale(0.5)}
100%{transform:scale(1)}
}
</style>

@endsection