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

                <form action="{{ route('admin.config.label-price.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="key" value="urgent_publication_price">
                    <input type="number" step="0.01" name="price"
                        class="form-control mb-2"
                        value="{{ $urgentPrice }}">
                    <button class="btn btn-danger btn-sm w-100">Actualizar</button>
                </form>
            </div>

            <!-- CARD 2 -->
            <div class="card p-3 shadow-sm" style="width: 260px; border-radius: 12px;">
                <h6 class="fw-bold mb-3">Publicación Destacada</h6>

                <form action="{{ route('admin.config.label-price.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="key" value="featured_publication_price">
                    <input type="number" step="0.01" name="price" 
                        class="form-control mb-2"
                        value="{{ $featuredPrice }}">
                    <button class="btn btn-danger btn-sm w-100">Actualizar</button>
                </form>
            </div>

            <!-- CARD 3 -->
            <div class="card p-3 shadow-sm" style="width: 260px; border-radius: 12px;">
                <h6 class="fw-bold mb-3">Publicación Estreno</h6>

                <form action="{{ route('admin.config.label-price.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="key" value="premiere_publication_price">
                    <input type="number" step="0.01" name="price" 
                        class="form-control mb-2"
                        value="{{ $premierePrice }}">
                    <button class="btn btn-danger btn-sm w-100">Actualizar</button>
                </form>
            </div>

            <!-- CARD 4 -->
            <div class="card p-3 shadow-sm" style="width:260px; border-radius:12px;">
                <h6 class="fw-bold mb-3">Etiqueta Seminuevo</h6>

                <form action="{{ route('admin.config.label-price.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="key" value="semi_new_publication_price">
                    <input type="number" step="0.01" name="price" 
                        class="form-control mb-2"
                        value="{{ $semiNewPrice }}">
                    <button class="btn btn-danger btn-sm w-100">Actualizar</button>
                </form>
            </div>

            <!-- CARD 5 -->
            <div class="card p-3 shadow-sm" style="width:260px; border-radius:12px;">
                <h6 class="fw-bold mb-3">Etiqueta Nuevo</h6>

                <form action="{{ route('admin.config.label-price.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="key" value="new_publication_price">
                    <input type="number" step="0.01" name="price" 
                        class="form-control mb-2"
                        value="{{ $newPrice }}">
                    <button class="btn btn-danger btn-sm w-100">Actualizar</button>
                </form>
            </div>

            <!-- CARD 6-->
            <div class="card p-3 shadow-sm" style="width:260px; border-radius:12px;">
                <h6 class="fw-bold mb-3">Etiqueta Disponible</h6>

                <form action="{{ route('admin.config.label-price.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="key" value="available_publication_price">
                    <input type="number" step="0.01" name="price" 
                        class="form-control mb-2"
                        value="{{ $availablePrice }}">
                    <button class="btn btn-danger btn-sm w-100">Actualizar</button>
                </form>
            </div>

            <!-- CARD 7-->
            <div class="card p-3 shadow-sm" style="width:260px; border-radius:12px;">
                <h6 class="fw-bold mb-3">Etiqueta Top</h6>

                <form action="{{ route('admin.config.label-price.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="key" value="top_publication_price">
                    <input type="number" step="0.01" name="price" 
                        class="form-control mb-2"
                        value="{{ $topPrice }}">
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
                        <button class="btn btn-sm btn-outline-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditCategory"
                            data-id="{{ $category->id }}"
                            data-name="{{ $category->name }}"
                            data-icon="{{ $category->icon }}"
                            data-is_urgent="{{ $category->is_urgent }}"
                            data-is_premiere="{{ $category->is_premiere }}"
                            data-is_featured="{{ $category->is_featured }}"
                            data-is_semi_new="{{ $category->is_semi_new }}"
                            data-is_new="{{ $category->is_new }}"
                            data-is_available="{{ $category->is_available }}"
                            data-is_top="{{ $category->is_top }}"
                        >
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
                                <button class="btn btn-sm btn-outline-warning"
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

                                <!-- GALERÍA -->
                                <button class="btn btn-sm btn-outline-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalImages"
                                    data-sub-id="{{ $sub->id }}"
                                    data-sub-name="{{ $sub->name }}">
                                    <i class="fa-solid fa-images"></i> Imágenes
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
                                        <button class="btn btn-sm btn-outline-warning"
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

<!-- MODAL IMÁGENES SUBCATEGORÍA -->
<div class="modal fade" id="modalImages" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <!-- HEADER -->
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold">
                    Gestión de imágenes · <span id="subName"></span>
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.config.subcategory.images.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="ad_subcategory_id" id="subId">

                    <div class="row g-4">

                        <!-- IZQUIERDA -->
                        <div class="col-lg-4">

                            <div class="section-title">Subir imágenes</div>

                            <label class="upload-zone w-100">
                                <i class="fa-solid fa-cloud-arrow-up mb-2"></i>
                                <div class="fw-semibold">Seleccionar imágenes</div>
                                <small class="text-muted">JPG, PNG · múltiples</small>

                                <input type="file"
                                       class="d-none"
                                       name="images[]"
                                       id="imageInput"
                                       multiple
                                       accept="image/*">
                            </label>

                            <div class="section-title mt-4">Vista previa</div>
                            <div class="image-counter" id="previewCount">0 imágenes</div>

                            <div id="previewImages" class="image-grid image-scroll">
                                <span class="text-muted small">
                                    Sin imágenes seleccionadas
                                </span>
                            </div>

                        </div>

                        <!-- DERECHA -->
                        <div class="col-lg-8">

                            <div class="section-title">Imágenes registradas</div>
                            <div class="image-counter" id="existingCount">0 imágenes</div>

                            <div id="existingImages" class="image-grid image-scroll">
                                <span class="text-muted small">
                                    Cargando imágenes...
                                </span>
                            </div>

                        </div>

                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-top">
                    <button class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-dark px-4">
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- MODALES --}}
@include('admin.config.categories.modals')


<script>   

// Gestión de Imágenes de Subcategorías
document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('imageInput');
    const preview = document.getElementById('previewImages');
    const existing = document.getElementById('existingImages');

    const previewCount = document.getElementById('previewCount');
    const existingCount = document.getElementById('existingCount');

    let currentSubId = null;

    // MODAL OPEN
    $('#modalImages').on('show.bs.modal', function (e) {

        const btn = e.relatedTarget;
        currentSubId = btn.dataset.subId;

        document.getElementById('subId').value = currentSubId;
        document.getElementById('subName').innerText = btn.dataset.subName;

        preview.innerHTML = `<span class="text-muted small">Sin imágenes seleccionadas</span>`;
        previewCount.innerText = `0 imágenes`;

        loadImages();
    });

    // LOAD IMAGES
    function loadImages() {

        existing.innerHTML = `<span class="text-muted small">Cargando imágenes...</span>`;
        existingCount.innerText = `0 imágenes`;

        fetch(`subcategory/${currentSubId}/images`)
            .then(r => r.json())
            .then(images => {

                existing.innerHTML = '';

                if (!images.length) {
                    existing.innerHTML = `
                        <span class="text-muted small">
                            No hay imágenes registradas
                        </span>`;
                    existingCount.innerText = `0 imágenes`;
                    return;
                }

                images.forEach(img => {
                    existing.innerHTML += `
                        <div class="image-card" data-id="${img.id}">
                            <img src="/${img.image}">
                            <button type="button" class="image-delete" title="Eliminar">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>`;
                });

                existingCount.innerText = `${images.length} imágenes`;
            });
    }

    // DELETE IMAGE (DELEGATION)
    existing.addEventListener('click', e => {

        const btn = e.target.closest('.image-delete');
        if (!btn) return;

        const card = btn.closest('.image-card');
        const imageId = card.dataset.id;

        Swal.fire({
            title: '¿Eliminar imagen?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {

            if (!result.isConfirmed) return;

            fetch(`/admin/config/subcategory-images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => {
                if (!res.ok) throw new Error();

                card.remove();

                const total = existing.querySelectorAll('.image-card').length;
                existingCount.innerText = `${total} imágenes`;

                if (!total) {
                    existing.innerHTML = `
                        <span class="text-muted small">
                            No hay imágenes registradas
                        </span>`;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Imagen eliminada',
                    timer: 1200,
                    showConfirmButton: false
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo eliminar la imagen'
                });
            });

        });

    });

    // ===============================
    // PREVIEW
    // ===============================
    input.addEventListener('change', () => {

        preview.innerHTML = '';

        if (!input.files.length) {
            preview.innerHTML = `
                <span class="text-muted small">
                    Sin imágenes seleccionadas
                </span>`;
            previewCount.innerText = `0 imágenes`;
            return;
        }

        [...input.files].forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML += `
                    <div class="image-card">
                        <img src="${e.target.result}">
                    </div>`;
            };
            reader.readAsDataURL(file);
        });

        previewCount.innerText = `${input.files.length} imágenes`;
    });

});

document.addEventListener('DOMContentLoaded', function() {

    // Cuando se abre el modal de subcategoría
    $('#modalAddSubcategory').on('show.bs.modal', function (e) {
        const btn = $(e.relatedTarget);
        const categoryId = btn.data('category-id');

        $('#addSubcategoryCategoryId').val(categoryId);
    });

    // Log del submit del formulario
    const form = document.querySelector('#modalImages form');

    form.addEventListener('submit', e => {
        if (!input.files.length) {
            e.preventDefault();
        }
    });

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
    $('#modalEditCategory').on('show.bs.modal', function (e) {
        const btn = $(e.relatedTarget);

        $('#editCategoryId').val(btn.data('id'));
        $('#editCategoryName').val(btn.data('name'));
        $('#editCategoryIcon').val(btn.data('icon'));

        const flags = [
            'is_urgent',
            'is_premiere',
            'is_featured',
            'is_semi_new',
            'is_new',
            'is_available',
            'is_top'
        ];

        flags.forEach(flag => {
            $('#edit_' + flag).prop('checked', btn.data(flag) == 1);
        });

        $('#formEditCategory').attr(
            'action',
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

<style>
/* GRID */
.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 16px;
}

.image-grid > span {
    grid-column: 1 / -1;
    text-align: center;
    align-self: center;
}

/* SCROLL CONTAINER */
.image-scroll {
    height: 360px; 
    max-height: 360px; 
    overflow-y: auto;
    padding-right: 6px;
}

/* SCROLL BAR (sutil) */
.image-scroll::-webkit-scrollbar {
    width: 6px;
}
.image-scroll::-webkit-scrollbar-thumb {
    background: #ced4da;
    border-radius: 4px;
}

/* CARD */
.image-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* IMAGE */
.image-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* DROPZONE */
.upload-zone {
    border: 2px dashed #ced4da;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    background: #f8f9fa;
    cursor: pointer;
    transition: .2s;
}

.upload-zone:hover {
    background: #eef2f7;
    border-color: #0d6efd;
}

.upload-zone i {
    font-size: 32px;
    color: #6c757d;
}

/* TITLES */
.section-title {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: 6px;
}

/* COUNTER */
.image-counter {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 8px;
}

/* DELETE BUTTON */
.image-delete {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(220, 53, 69, 0.95);
    color: #fff;
    border: none;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: .2s ease;
}

.image-card {
    position: relative;
}

.image-card:hover .image-delete {
    opacity: 1;
}

.image-delete:hover {
    background: #bb2d3b;
}
</style>


@endsection