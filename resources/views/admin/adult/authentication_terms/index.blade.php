@extends('layouts.app')

@section('title', 'Políticas de Privacidad')

@section('content')

<div class="container mt-5 mb-5">

    <a href="{{ route('admin.config.privacy-policy.indexGestion') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Políticas de Privacidad</h4>
    <p class="text-secondary text-center mb-4">
        Configura el contenido legal visible para los usuarios del sistema.
    </p>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

                <form action="{{ route('admin.adult.authentication_terms.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- TEXTO DE POLÍTICAS --}}
                    <div class="mb-4">
                        <label for="privacy_text" class="form-label fw-semibold">
                            Texto de Políticas de Privacidad
                        </label>

                        <textarea
                            name="privacy_text"
                            id="privacy_text"
                            class="form-control"
                            rows="8"
                            placeholder="Escriba aquí el texto legal de las políticas..."
                            required
                        >{{ old('privacy_text', $policy->privacy_text ?? '') }}</textarea>

                        @error('privacy_text')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- OPCIONES --}}
                    <div class="row g-4 mb-4">

                        {{-- ACTIVO --}}
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    {{ $policy?->is_active ? 'checked' : '' }}
                                >
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Políticas activas
                                </label>
                            </div>
                            <small class="text-muted">
                                Visible para los usuarios.
                            </small>
                        </div>

                    </div>

                    {{-- BOTÓN --}}
                    <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                        Actualizar Políticas
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

@endsection         