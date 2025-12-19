@extends('layouts.app')

@section('title', 'Libro de Reclamaciones')

@section('content')
<div class="container mt-5 mb-5">

    <h4 class="fw-bold text-center mb-3">Libro de Reclamaciones</h4>

    <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius:16px;">
        <p><strong>Razón Social:</strong> {{ $settings->business_name }}</p>
        <p><strong>RUC:</strong> {{ $settings->ruc }}</p>
        <p><strong>Dirección:</strong> {{ $settings->address }}</p>

        <hr>

        <div class="small text-muted">
            {!! nl2br(e($settings->legal_text)) !!}
        </div>
    </div>

    @auth
        {{-- FORMULARIO --}}
        <div class="card shadow-sm border-0 p-4" style="border-radius:16px;">
            <h5 class="fw-bold mb-3">Registrar reclamo / queja</h5>

            <form method="POST" action="{{ url('/advertising/complaints') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="complaint_type" class="form-select" required>
                        <option value="reclamo">Reclamo</option>
                        <option value="queja">Queja</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Asunto</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>

                <button class="btn btn-danger w-100">
                    Enviar reclamo
                </button>
            </form>
        </div>
    @else
        <div class="alert alert-warning text-center">
            Debes <a href="{{ route('login') }}">iniciar sesión</a> para enviar un reclamo.
        </div>
    @endauth

</div>
@endsection
