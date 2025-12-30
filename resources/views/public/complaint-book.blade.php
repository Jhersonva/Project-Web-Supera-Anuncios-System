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

        {{-- FORMULARIO --}}
        <div class="card shadow-sm border-0 p-4" style="border-radius:16px;">
            <h5 class="fw-bold mb-3">Registrar reclamo / queja</h5>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Corrige los siguientes errores:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('complaints.store') }}" id="complaint-form">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" class="form-control @error('full_name') is-invalid @enderror" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="complaint_type"class="form-select @error('complaint_type') is-invalid @enderror"required>
                        <option value="">Seleccione</option>
                        <option value="reclamo" {{ old('complaint_type') == 'reclamo' ? 'selected' : '' }}>
                            Reclamo
                        </option>
                        <option value="queja" {{ old('complaint_type') == 'queja' ? 'selected' : '' }}>
                            Queja
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Asunto</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        ¿Cuánto es {{ $a }} + {{ $b }}?
                    </label>
                    <input type="number" name="captcha" value="{{ old('captcha') }}" class="form-control @error('captcha') is-invalid @enderror" required>
                    @error('captcha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                

                <button class="btn btn-danger w-100">
                    Enviar reclamo
                </button>
            </form>
        </div>
</div>
@endsection
