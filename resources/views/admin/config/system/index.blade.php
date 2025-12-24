@extends('layouts.app')

@section('title', 'Gestión de Configuración del Sistema')

@section('content')

<div class="container mt-5 mb-5">

    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Configuración del Sistema</h4>
    <p class="text-secondary text-center mb-4">
        Aquí podrás administrar el nombre, descripción y logo de tu sistema.
    </p>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

                <form action="{{ route('admin.config.system.update') }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Empresa</label>
                        <input type="text" name="company_name" class="form-control"
                            value="{{ old('company_name', $settings->company_name) }}">
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="company_description"
                                class="form-control"
                                rows="4">{{ old('company_description', $settings->company_description) }}</textarea>
                    </div>

                    {{-- Logo --}}
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control">

                        @if($settings->logo)
                            <img src="{{ asset($settings->logo) }}"
                                class="mt-3"
                                style="max-height:150px">
                        @endif
                    </div>

                    <button class="btn btn-primary w-100">
                        Actualizar Configuración
                    </button>
                </form>

                <hr class="my-4">

                <h5 class="fw-bold mb-3">Redes Sociales</h5>

                <form action="{{ route('admin.config.system.social.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="row g-3 mb-4">
                    @csrf

                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control" placeholder="Facebook" required>
                    </div>

                    <div class="col-md-5">
                        <input type="url" name="url" class="form-control"
                            placeholder="https://facebook.com/empresa" required>
                    </div>

                    <div class="col-md-2">
                        <input type="file" name="icon" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-success w-100">
                            <i class="fa-solid fa-plus"></i> Agregar
                        </button>
                    </div>
                </form>


                <div class="row">
                @forelse($socials as $social)
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                @if($social->icon)
                                    <img src="{{ asset($social->icon) }}" width="28">
                                @endif
                                <strong>{{ $social->name }}</strong>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ $social->url }}"
                                target="_blank"
                                class="btn btn-sm btn-primary">
                                    Ver
                                </a>

                                <form action="{{ route('admin.config.system.social.destroy', $social) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">No hay redes sociales registradas</p>
                @endforelse
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
