@extends('layouts.app')

@section('title', 'Detalle del Reclamo')

@section('content')

<div class="container mt-5 mb-5">

    <a href="{{ route('admin.config.complaints.index') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>

    <div class="card shadow-sm border-0 mt-3 p-4" style="border-radius:16px;">

        <h5 class="fw-bold mb-3">Detalle del Reclamo</h5>

        <div class="row g-3">
            <div class="col-md-6">
                <strong>Nombre:</strong>
                <p>{{ $complaint->full_name }}</p>
            </div>

            <div class="col-md-6">
                <strong>Email:</strong>
                <p>{{ $complaint->email }}</p>
            </div>

            <div class="col-md-6">
                <strong>Tipo:</strong>
                <p>{{ ucfirst($complaint->complaint_type) }}</p>
            </div>

            <div class="col-md-6">
                <strong>Estado:</strong>
                <p>
                    <span class="badge bg-{{ $complaint->status === 'pendiente' ? 'warning' : ($complaint->status === 'atendido' ? 'primary' : 'success') }}">
                        {{ ucfirst($complaint->status) }}
                    </span>
                </p>
            </div>

            <div class="col-12">
                <strong>Asunto:</strong>
                <p>{{ $complaint->subject }}</p>
            </div>

            <div class="col-12">
                <strong>Descripción:</strong>
                <p>{{ $complaint->description }}</p>
            </div>

            @if($complaint->request)
            <div class="col-12">
                <strong>Solicitud:</strong>
                <p>{{ $complaint->request }}</p>
            </div>
            @endif
        </div>

        {{-- RESPUESTA ADMIN --}}
        <hr>

        <form method="POST"
              action="{{ route('admin.config.complaints.update', $complaint) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                    <option value="pendiente" @selected($complaint->status === 'pendiente')>Pendiente</option>
                    <option value="atendido" @selected($complaint->status === 'atendido')>Atendido</option>
                    <option value="cerrado" @selected($complaint->status === 'cerrado')>Cerrado</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Respuesta</label>
                <textarea name="response" rows="4" class="form-control">{{ $complaint->response }}</textarea>
            </div>

            <button class="btn btn-primary w-100">
                Guardar respuesta
            </button>
        </form>

    </div>
</div>

@endsection
