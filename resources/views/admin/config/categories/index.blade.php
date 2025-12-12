@extends('layouts.app')

@section('title', 'Administrar Categorías')

@section('content')

<div class="container mt-5 mb-5">

    {{-- BOTÓN VOLVER --}}
    <a href="{{ route('admin.config') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h4 class="fw-bold mb-3 text-center">Administración de Categorías</h4>

    <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
        <!-- Botón Nueva Categoría -->
        <button class="btn btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#modalAddCategory">
            <i class="fa-solid fa-plus"></i> Nueva Categoría
        </button>
        <!-- Botón Configuración de Precios -->
        <button class="btn btn-dark"
            data-bs-toggle="collapse"
            data-bs-target="#priceCards">
            <i class="fa-solid fa-gear me-1"></i>
            Configuración de Precios
        </button>
    </div>

    <div id="priceCards" class="collapse">

        <div class="d-flex justify-content-center gap-4 mb-4 flex-wrap">

            <!-- CARD 1 -->
            <div class="card p-3 shadow-sm" style="width: 260px; border-radius: 12px;">
                <h6 class="fw-bold mb-3">Publicación Urgente</h6>

                <form action="{{ route('admin.config.urgent-price.update') }}" method="POST">
                    @csrf
                    <input type="number" step="0.01" name="urgent_price"
                        class="form-control mb-2" value="{{ $urgentPrice }}">
                    <button class="btn btn-danger btn-sm w-100">Actualizar</button>
                </form>
            </div>

            <!-- CARD 2 -->
            <div class="card p-3 shadow-sm" style="width: 260px; border-radius: 12px;">
                <h6 class="fw-bold mb-3">Publicación Destacada</h6>

                <form action="{{ route('admin.config.featured-price.update') }}" method="POST">
                    @csrf
                    <input type="number" step="0.01" name="featured_price"
                        class="form-control mb-2" value="{{ $featuredPrice }}">
                    <button class="btn btn-danger btn-sm w-100">Actualizar</button>
                </form>
            </div>

            <!-- CARD 3 -->
            <div class="card p-3 shadow-sm" style="width: 260px; border-radius: 12px;">
                <h6 class="fw-bold mb-3">Publicación Estreno</h6>

                <form action="{{ route('admin.config.premiere-price.update') }}" method="POST">
                    @csrf
                    <input type="number" step="0.01" name="premiere_price"
                        class="form-control mb-2" value="{{ $premierePrice }}">
                    <button class="btn btn-danger btn-sm w-100">Actualizar</button>
                </form>
            </div>

        </div>

    </div>

    @foreach ($categories as $category)
        <div class="card shadow-sm mb-4 border-0" style="border-radius: 16px;">
            <div class="card-body">

                <!-- CATEGORÍA -->
                <div class="d-flex justify-content-between align-items-center mb-2">

                    <div class="d-flex align-items-center">
                        <i class="fa-solid {{ $category->icon }} text-danger me-3" style="font-size: 1.8rem;"></i>
                        <h5 class="fw-bold m-0">{{ $category->name }}</h5>
                    </div>

                    <div>
                        <button class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditCategory"
                                data-id="{{ $category->id }}"
                                data-name="{{ $category->name }}"
                                data-icon="{{ $category->icon }}">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <button class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDeleteCategory"
                                data-id="{{ $category->id }}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="text-end mb-2">
                    <button class="btn btn-sm btn-dark"
                            data-bs-toggle="modal"
                            data-bs-target="#modalAddSubcategory"
                            data-category-id="{{ $category->id }}">
                        <i class="fa-solid fa-plus"></i> Subcategoría
                    </button>
                </div>

                <hr>

                <!-- SUBCATEGORÍAS -->
                @foreach ($category->subcategories as $sub)
                    <div class="mb-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <h6 class="fw-semibold mb-0">{{ $sub->name }}</h6>
                                <small class="text-muted">S/. {{ number_format($sub->price, 2) }}</small>
                            </div>

                            <div>
                                <button class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditSubcategory"
                                        data-id="{{ $sub->id }}"
                                        data-name="{{ $sub->name }}"
                                        data-price="{{ $sub->price }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDeleteSubcategory"
                                        data-id="{{ $sub->id }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- BOTÓN AGREGAR CAMPO -->
                        <div class="mt-2 text-end">
                            <button class="btn btn-sm btn-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAddField"
                                    data-sub-id="{{ $sub->id }}">
                                <i class="fa-solid fa-plus"></i> Campo
                            </button>
                        </div>

                        <!-- CAMPOS -->
                        <ul class="list-group ms-3 mt-2 mb-3">
                            @forelse ($sub->fields as $field)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <i class="fa-solid fa-circle text-danger small me-2"></i>
                                        {{ $field->name }}
                                    </div>

                                    <div>
                                        <button class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditField"
                                                data-id="{{ $field->id }}"
                                                data-name="{{ $field->name }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDeleteField"
                                                data-id="{{ $field->id }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
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

{{-- MODALES --}}
@include('admin.config.categories.modals')


<script>   
document.addEventListener('DOMContentLoaded', function() {

    // Cuando se abre el modal de subcategoría
    $('#modalAddSubcategory').on('show.bs.modal', function (e) {
        const btn = $(e.relatedTarget);
        const categoryId = btn.data('category-id');

        $('#addSubcategoryCategoryId').val(categoryId);
    });

    // Log del submit del formulario
    const form = document.querySelector('#modalAddSubcategory form');

    if (form) {
        form.addEventListener('submit', function() {
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {

    // Cuando se abre el modal de agregar Campo
    $('#modalAddField').on('show.bs.modal', function (e) {
        const btn = $(e.relatedTarget);
        const subId = btn.data('sub-id');

        $('#addFieldSubId').val(subId);

    });


    // Log al hacer submit del modal de campo
    $("#modalAddField form").on("submit", function(e) {
    });
});

document.addEventListener("DOMContentLoaded", () => {

    // Editar Categoría
    $('#modalEditCategory').on('show.bs.modal', function(e){
        const btn = $(e.relatedTarget);
        $('#editCategoryId').val(btn.data('id'));
        $('#editCategoryName').val(btn.data('name'));
        $('#editCategoryIcon').val(btn.data('icon'));

       $('#formEditCategory').attr('action',
            `/admin/config/categorias/update/${btn.data('id')}`
        );
    });

    // Eliminar Categoría
    $('#modalDeleteCategory').on('show.bs.modal', function(e){
        const id = $(e.relatedTarget).data('id');
        $('#formDeleteCategory').attr('action', `/admin/config/categorias/delete/${id}`);
    });

    // Editar Subcategoría
    $('#modalEditSubcategory').on('show.bs.modal', function(e){
        const btn = $(e.relatedTarget);
        $('#editSubId').val(btn.data('id'));
        $('#editSubName').val(btn.data('name'));
        $('#editSubPrice').val(btn.data('price'));

        $('#formEditSubcategory').attr('action',
            `/admin/config/sub/update/${btn.data('id')}`
        );
    });

    // Eliminar Subcategoría
    $('#modalDeleteSubcategory').on('show.bs.modal', function(e){
        const id = $(e.relatedTarget).data('id');
        $('#formDeleteSubcategory').attr('action', `/admin/config/sub/delete/${id}`);
    });

    // Editar Campo
    $('#modalEditField').on('show.bs.modal', function(e){
        const btn = $(e.relatedTarget);
        $('#editFieldId').val(btn.data('id'));
        $('#editFieldName').val(btn.data('name'));

        $('#formEditField').attr('action',
            `/admin/config/field/update/${btn.data('id')}`
        );
    });

    // Eliminar Campo
    $('#modalDeleteField').on('show.bs.modal', function(e){
        const id = $(e.relatedTarget).data('id');
        $('#formDeleteField').attr('action', `/admin/config/field/delete/${id}`);
    });

});
</script>


@endsection