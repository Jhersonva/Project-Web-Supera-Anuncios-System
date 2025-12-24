@extends('layouts.app')

@section('title', 'Gestión de Reclamos')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container mt-5 mb-5">

    {{-- VOLVER --}}
    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold text-center mb-2">Gestión de Reclamos</h4>
    <p class="text-muted text-center mb-4">
        Reclamos y quejas enviados por los usuarios.
    </p>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

        {{-- ICONO Y TÍTULO --}}
        <div class="d-flex align-items-center mb-4">
            <div class="bg-secondary text-white p-3 rounded-circle me-3"
                 style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-book-open"></i>
            </div>

            <div>
                <h5 class="fw-bold m-0">Reclamos Registrados</h5>
                <small class="text-muted">Listado de reclamos y quejas</small>
            </div>
        </div>

        {{-- BUSCADOR 
        <form method="GET" class="mb-4">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   class="form-control"
                   placeholder="Buscar por nombre, correo o asunto">
        </form>--}}

        @if($complaints->count() == 0)
            <div class="alert alert-warning text-center">
                No hay reclamos registrados.
            </div>
        @endif

        {{-- ========================= --}}
        {{--   VISTA ESCRITORIO TABLA --}}
        {{-- ========================= --}}
        <div class="table-responsive desktop-table">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Tipo</th>
                        <th>Asunto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($complaints as $complaint)
                        <tr>
                            <td class="fw-semibold">{{ $complaint->full_name }}</td>
                            <td>{{ $complaint->email }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst($complaint->complaint_type) }}
                                </span>
                            </td>
                            <td>{{ $complaint->subject }}</td>
                            <td>
                                @if($complaint->status === 'pendiente')
                                    <span class="badge bg-warning">Pendiente</span>
                                @elseif($complaint->status === 'atendido')
                                    <span class="badge bg-primary">Atendido</span>
                                @else
                                    <span class="badge bg-success">Cerrado</span>
                                @endif
                            </td>
                            <td>{{ $complaint->created_at->format('d/m/Y') }}</td>
                            <td>

                                <a href="{{ route('admin.config.complaints.show', $complaint) }}"
                                   class="btn btn-sm btn-primary mb-1">
                                    Ver
                                </a>

                                <form action="{{ route('admin.config.complaints.destroy', $complaint) }}"
                                      method="POST"
                                      class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn btn-sm btn-danger btn-delete">
                                        Eliminar
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ======================= --}}
        {{--   VISTA MÓVIL CARDS   --}}
        {{-- ======================= --}}
        <div class="mobile-card">
            @foreach ($complaints as $complaint)
                <div class="card mb-3 shadow-sm border-0" style="border-radius:14px;">
                    <div class="card-body">

                        <h6 class="fw-bold mb-2">{{ $complaint->full_name }}</h6>

                        <div class="small mb-3">
                            <div><strong>Email:</strong> {{ $complaint->email }}</div>
                            <div><strong>Tipo:</strong> {{ ucfirst($complaint->complaint_type) }}</div>
                            <div><strong>Asunto:</strong> {{ $complaint->subject }}</div>
                            <div><strong>Fecha:</strong> {{ $complaint->created_at->format('d/m/Y') }}</div>
                            <div class="mt-2">
                                <strong>Estado:</strong>
                                @if($complaint->status === 'pendiente')
                                    <span class="badge bg-warning">Pendiente</span>
                                @elseif($complaint->status === 'atendido')
                                    <span class="badge bg-primary">Atendido</span>
                                @else
                                    <span class="badge bg-success">Cerrado</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2">

                            <a href="{{ route('admin.config.complaints.show', $complaint) }}"
                               class="btn btn-primary btn-sm w-100">
                                Ver
                            </a>

                            <form action="{{ route('admin.config.complaints.destroy', $complaint) }}"
                                  method="POST" class="w-100 delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="btn btn-danger btn-sm w-100 btn-delete">
                                    Eliminar
                                </button>
                            </form>

                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- PAGINACIÓN --}}
        <div class="mt-3">
            {{ $complaints->links() }}
        </div>

    </div>
</div>

{{-- CONFIRMACIÓN ELIMINAR --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const form = btn.closest('.delete-form');
            Swal.fire({
                title: '¿Eliminar reclamo?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<style>
@media (max-width: 768px) {
    .desktop-table { display: none !important; }
    .mobile-card { display: block !important; }
}
@media (min-width: 769px) {
    .mobile-card { display: none !important; }
}
</style>

@endsection
