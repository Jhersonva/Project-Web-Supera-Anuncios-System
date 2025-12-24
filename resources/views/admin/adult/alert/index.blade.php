@extends('layouts.app')

@section('title', 'Gestión de Alertas')

@section('content')

<div class="container mt-5 mb-5">

    <a href="{{ route('admin.config.privacy-policy.indexGestion') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Alertas del Sistema</h4>

    <p class="text-secondary text-center mb-4">
        Administra las alertas visibles para los usuarios.
    </p>

    <div class="row justify-content-center">
        <div class="col-md-8">

            @foreach($alerts as $alert)
            <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius:16px;">

                <form method="POST"
                      action="{{ route('alerts.update', $alert) }}"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- LOGO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Logo</label>

                        <input type="file"
                               name="logo"
                               class="form-control"
                               onchange="previewIcon(this)">

                        @if($alert->logo)
                            <img src="{{ asset($alert->logo) }}"
                                 class="mt-3 d-block"
                                 style="max-height:120px;"
                                 id="current-icon-{{ $alert->id }}">
                        @endif

                        <img class="mt-3 d-none"
                             style="max-height:120px;"
                             id="preview-icon-{{ $alert->id }}">
                    </div>

                    {{-- TÍTULO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               value="{{ old('title', $alert->title) }}"
                               required>
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="description"
                                  rows="4"
                                  class="form-control"
                                  required>{{ old('description', $alert->description) }}</textarea>
                    </div>

                    {{-- ESTADO --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ $alert->is_active ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ !$alert->is_active ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100 fw-semibold py-2">
                        Actualizar Alerta
                    </button>

                </form>

            </div>
            @endforeach

        </div>
    </div>

</div>

<script>
function previewIcon(input) {
    const file = input.files[0];
    if (!file) return;

    const wrapper = input.closest('.mb-3');
    const preview = wrapper.querySelector('[id^="preview-icon"]');
    const current = wrapper.querySelector('[id^="current-icon"]');

    preview.src = URL.createObjectURL(file);
    preview.classList.remove('d-none');

    if (current) {
        current.classList.add('d-none');
    }
}
</script>

@endsection
