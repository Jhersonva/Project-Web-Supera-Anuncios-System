@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')

<div class="container mt-5">

    <a href="{{ route('admin.config.clients') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-4 text-center">Editar Cliente</h4>

    <div class="card p-4 shadow-sm">

        <form action="{{ route('admin.config.clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nombre completo</label>a
                <input type="text" class="form-control" name="full_name" value="{{ $client->full_name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ $client->email }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input type="text" class="form-control" name="phone" value="{{ $client->phone }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Localidad</label>
                <input type="text" class="form-control" name="locality" value="{{ $client->locality }}">
            </div>

            <button class="btn btn-primary w-100">Guardar cambios</button>
        </form>

    </div>
</div>

@endsection