@extends('layouts.app')

@section('title', 'Gestión de Reclamos')

@section('content')

<div class="container mt-5 mb-5">

    {{-- VOLVER --}}
    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Gestión de Reclamos</h4>
    <p class="text-secondary text-center mb-4">
        Reclamos y quejas enviados por los usuarios.
    </p>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 16px;">

        {{-- BUSCADOR --}}
        <form method="GET" class="mb-4">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   class="form-control"
                   placeholder="Buscar por nombre, correo o asunto">
        </form>

        @if ($complaints->isEmpty())
            <p class="text-center text-muted">No hay reclamos registrados.</p>
        @else

        {{-- TABLA DESKTOP --}}
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

        {{-- PAGINACIÓN --}}
        <div class="mt-3">
            {{ $complaints->links() }}
        </div>

        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {

            const form = this.closest('.delete-form');

            Swal.fire({
                title: '¿Eliminar reclamo?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
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
    .desktop-table { display: none; }
}
</style>

@endsection
