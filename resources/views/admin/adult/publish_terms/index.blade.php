@extends('layouts.app')

@section('title', 'Términos para publicar contenido adulto')

@section('content')

<div class="container mt-5 mb-5">

    {{-- Volver --}}
    <a href="{{ route('admin.config.privacy-policy.indexGestion') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    {{-- Título --}}
    <h4 class="fw-bold mb-3 text-center">
        Términos para publicar contenido adulto
    </h4>

    <p class="text-secondary text-center mb-4">
        Configura las condiciones que deben aceptar los usuarios para poder publicar contenido adulto.
    </p>

    <div class="row justify-content-center">
        <div class="col-md-8">

            @foreach($terms as $term)
            <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius:16px;">

                <form method="POST"
                      action="{{ route('adult.publish_terms.update', $term) }}"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- ICONO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Icono
                        </label>

                        <input type="file"
                               name="icon"
                               class="form-control"
                               onchange="previewIcon(this)">

                        @if($term->icon)
                            <img src="{{ asset($term->icon) }}"
                                 class="mt-3 d-block"
                                 style="max-height:120px;"
                                 id="current-icon-{{ $term->id }}">
                        @endif

                        <img class="mt-3 d-none"
                             style="max-height:120px;"
                             id="preview-icon-{{ $term->id }}">
                    </div>

                    {{-- TÍTULO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Título
                        </label>

                        <input type="text"
                               name="title"
                               class="form-control"
                               value="{{ old('title', $term->title) }}"
                               required>
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Descripción
                        </label>

                        <textarea name="description"
                                  rows="4"
                                  class="form-control"
                                  required>{{ old('description', $term->description) }}</textarea>
                    </div>

                    {{-- BOTÓN --}}
                    <button class="btn btn-primary w-100 fw-semibold py-2">
                        Actualizar Término
                    </button>

                </form>

            </div>
            @endforeach

        </div>
    </div>

</div>

{{-- PREVISUALIZACIÓN DE IMAGEN --}}
<script>
function previewIcon(input) {
    const file = input.files[0];
    if (!file) return;

    const container = input.closest('.mb-3');
    const preview = container.querySelector('img[id^="preview-icon"]');
    const current = container.querySelector('img[id^="current-icon"]');

    preview.src = URL.createObjectURL(file);
    preview.classList.remove('d-none');

    if (current) {
        current.classList.add('d-none');
    }
}
</script>

@endsection
