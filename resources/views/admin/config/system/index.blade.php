@extends('layouts.app')

@section('title', 'Gestión de Configuración del Sistema')

@section('content')

<div class="container mt-5 mb-5">

    <h4 class="fw-bold mb-3 text-center">Configuración del Sistema</h4>
    <p class="text-secondary text-center mb-4">
        Aquí podrás administrar el nombre, descripción y logo de tu sistema.
    </p>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

                <form action="{{ route('admin.config.system.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Nombre de la Empresa --}}
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Nombre de la Empresa</label>
                        <input type="text" name="company_name" id="company_name" class="form-control" 
                               value="{{ old('company_name', $settings->company_name) }}" 
                               placeholder="Ingrese el nombre de la empresa">
                        @error('company_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Descripción de la Empresa --}}
                    <div class="mb-3">
                        <label for="company_description" class="form-label">Descripción de la Empresa</label>
                        <textarea name="company_description" id="company_description" class="form-control" rows="4"
                                  placeholder="Ingrese la descripción de la empresa">{{ old('company_description', $settings->company_description) }}</textarea>
                        @error('company_description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Logo de la Empresa --}}
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo de la Empresa</label>
                        <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                        @if($settings->logo)
                            <div class="mt-3">
                                <label class="form-label">Logo Actual</label>
                                <div>
                                    <img src="{{ asset($settings->logo) }}" alt="Logo actual" class="img-fluid" style="max-height: 150px;">
                                </div>
                            </div>
                        @endif
                        @error('logo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Botón de Actualización --}}
                    <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">Actualizar Configuración</button>
                </form>

            </div>
        </div>
    </div>

</div>

@endsection
