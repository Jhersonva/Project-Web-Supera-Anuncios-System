@extends('layouts.app')

@section('title', 'Administrar Categorías')

@section('content')

<div class="container mt-5 mb-5">

    {{-- IZQUIERDA: BOTÓN VOLVER --}}
    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Administración de Categorías</h4>
    <p class="text-secondary text-center mb-4">
        Aquí puedes ver todas las categorías, sub-categorías y los campos que pertenecen a cada una.
    </p>

    @foreach ($categories as $category)
        <div class="card shadow-sm mb-4 border-0" style="border-radius: 16px;">
            <div class="card-body">

                <!-- CATEGORÍA -->
                <div class="d-flex align-items-center mb-2">
                    <i class="fa-solid {{ $category->icon }} text-danger me-3" style="font-size: 1.8rem;"></i>
                    <h5 class="fw-bold m-0">{{ $category->name }}</h5>
                </div>

                <hr>

                <!-- SUBCATEGORÍAS -->
                @foreach ($category->subcategories as $sub)
                    <div class="mb-3">

                        <div class="d-flex justify-content-between">
                            <h6 class="fw-semibold mb-1">{{ $sub->name }}</h6>
                            <span class="badge text-bg-danger">S/. {{ number_format($sub->price, 2) }}</span>
                        </div>

                        <!-- CAMPOS -->
                        <ul class="list-group ms-3 mb-3">
                            @forelse ($sub->fields as $field)
                                <li class="list-group-item py-1">
                                    <i class="fa-solid fa-circle text-danger small me-2"></i>
                                    {{ $field->name }}
                                </li>
                            @empty
                                <li class="list-group-item py-1 text-muted">
                                    No hay campos asignados
                                </li>
                            @endforelse
                        </ul>

                    </div>
                @endforeach

            </div>
        </div>
    @endforeach

</div>

@endsection
