@extends('layouts.app')

@section('title', 'Editar Anuncio')

@section('content')

<style>
    .field-card{
        border-radius: 14px;
        padding: 18px;
        background: #fff;
        border: 1px solid #eee;
        margin-bottom: 15px;
    }
    .img-thumb {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #ddd;
    }
    .delete-img-btn {
        position: absolute;
        top: -6px;
        right: -6px;
        background: red;
        color: #fff;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        font-size: 12px;
        border: none;
    }
</style>

<div class="container mt-4 mb-5">

    {{-- VOLVER --}}
    <a href="{{ url()->previous() }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    <h3 class="fw-bold mb-3 text-center">Editar Anuncio</h3>
    <p class="text-secondary text-center mb-4">
        Modifica la información de tu anuncio y guarda los cambios.
    </p>

    <!-- FORMULARIO -->
    <form action="{{ route('my-ads.updateAd', $ad->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="return_to" value="{{ url()->previous() }}">
        @csrf

        {{-- CATEGORÍA --}}
        <div class="field-card">
            <label class="fw-semibold mb-2">Categoría</label>
            <select id="categorySelect" name="category_id" class="form-select" disabled>
                <option value="">-- Selecciona --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $ad->ad_categories_id == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- SUBCATEGORÍA --}}
        <div id="subcatContainer" class="field-card">
            <label class="fw-semibold mb-2">Subcategoría</label>
            <select id="subcategorySelect" name="subcategory_id" class="form-select" disabled>
                @foreach($subcategories as $sub)
                    <option value="{{ $sub->id }}" {{ $ad->ad_subcategories_id == $sub->id ? 'selected' : '' }}>
                        {{ $sub->name }} 
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Título --}}
        <div class="field-card" id="titleContainer">
            <label class="fw-semibold">Título del Anuncio</label>
            <input type="text" class="form-control" name="title" value="{{ $ad->title }}" disabled>
        </div>

        {{-- Descripción --}}
        <div class="field-card" id="descriptionContainer">
            <label class="fw-semibold">Descripción</label>
            <textarea name="description" class="form-control" rows="4" disabled>{{ $ad->description }}</textarea>
        </div>

        {{-- LISTA DE CAMPOS DINÁMICOS --}}
        <div id="fieldsContainer">
            @foreach($fields as $field)
            @php
                $existing = $ad->fields_values->firstWhere('fields_subcategory_ads_id', $field->id);
            @endphp
            <div class="field-card">
                <label class="fw-semibold">{{ $field->name }}</label>
                <input type="text" class="form-control" name="dynamic[{{ $field->id }}]"
                       value="{{ $existing ? $existing->value : '' }}" disabled>
            </div>
            @endforeach
        </div>

        {{-- UBICACIÓN DEL ANUNCIO --}}
        <div class="field-card" id="locationAdContainer">

            <label class="fw-semibold">Departamento</label>
            <input
                type="text"
                name="department"
                class="form-control"
                placeholder="Ej: Lima"
                value="{{ $ad->department }}"
                disabled
            >

            <label class="fw-semibold mt-2">Provincia</label>
            <input
                type="text"
                name="province"
                class="form-control"
                placeholder="Ej: Lima"
                value="{{ $ad->province  }}"
                disabled
            >

            <label class="fw-semibold mt-2">Distrito</label>
            <input
                type="text"
                name="district"
                class="form-control"
                placeholder="Ej: San Juan de Miraflores"
                value="{{ $ad->district }}"
                disabled
            >
        </div>

        {{-- Dirección --}}
        <div class="field-card" id="contactLocationContainer">
            <label class="fw-semibold">Dirección</label>
            <input type="text" name="contact_location" class="form-control"
                   value="{{ $ad->contact_location }}">
        </div>

        {{-- DATOS DE CONTACTO --}}
        <div class="field-card">

            <label class="fw-semibold">Contacto vía WhatsApp</label>
            <input
                type="text"
                name="whatsapp"
                class="form-control"
                value="{{ old('whatsapp', $ad->user?->whatsapp) }}"
                placeholder="Ej: +51 999 888 777"
            >

            <label class="fw-semibold mt-2">Contacto vía Llamada</label>
            <input
                type="text"
                name="call_phone"
                class="form-control"
                value="{{ old('call_phone', $ad->user?->call_phone) }}"
                placeholder="Ej: 01 555 4444"
            >

        </div>

        {{-- MONTO --}}
        <div class="field-card" id="amountContainer">

            <div class="d-flex justify-content-between align-items-start gap-3">
                <div style="flex:1">

                    <label class="fw-semibold">Monto / Precio / Sueldo *</label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="amount"
                        id="amountInput"
                        class="form-control"
                        value="{{ $ad->amount_visible ? $ad->amount : '' }}"
                        disabled
                    >

                    {{-- SELECT TEXTO --}}
                    <select id="amountTextSelect" class="form-select mt-2" disabled>
                        <option value="">Selecciona texto...</option>
                        <option value="Sueldo a tratar" {{ $ad->amount_text == 'Sueldo a tratar' ? 'selected' : '' }}>
                            Sueldo a tratar
                        </option>
                        <option value="Sueldo por comisiones" {{ $ad->amount_text == 'Sueldo por comisiones' ? 'selected' : '' }}>
                            Sueldo por comisiones
                        </option>
                        <option value="No especificado" {{ $ad->amount_text == 'No especificado' ? 'selected' : '' }}>
                            No especificado
                        </option>
                    </select>

                    <small class="text-muted">
                        Si ocultas el monto, se mostrará el texto seleccionado.
                    </small>

                </div>

                <div style="min-width:170px; display:flex; align-items:center; justify-content:center;">
                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="amountVisibleCheckbox"
                            {{ $ad->amount_visible ? 'checked' : '' }}
                            disabled
                        >
                        <label class="form-check-label">Mostrar monto</label>
                    </div>
                </div>
            </div>

            <input type="hidden" name="amount_visible" id="amountVisibleInput" value="{{ $ad->amount_visible }}">
            <input type="hidden" name="amount_text" id="amountTextInput" value="{{ $ad->amount_text }}">

        </div>

        {{-- COSTOS --}}
        <div class="field-card" id="costContainer">
            <label class="fw-semibold">Días de publicación *</label>
            <input type="number" min="1" name="days_active" id="days_active" class="form-control"
                   value="{{ $ad->days_active }}" disabled>

            <small class="text-muted">Indica cuántos días deseas que tu anuncio esté activo.</small>
            <br>

            <label class="fw-semibold mt-2">Costo por día</label>
            <input type="text" id="pricePerDay" class="form-control mb-2" value="S/. {{ $subcategories->firstWhere('id', $ad->ad_subcategories_id)->price ?? 0 }}" disabled>

            <label class="fw-semibold mt-2">Costo total</label>
            <input type="text" id="totalCost" class="form-control mb-2" value="S/. 0.00" disabled>

            <label class="fw-semibold mt-2">Fecha de expiración</label>
            <input type="text" id="expiresAt" class="form-control"
                   value="{{ $ad->expires_at }}" disabled>
        </div>

        {{-- PUBLICACIÓN URGENTE --}}
        <div class="field-card d-none" id="urgentContainer">
            <label class="fw-semibold">¿Publicación urgente?</label>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="urgent_publication"
                    name="urgent_publication"
                    value="1"
                    {{ $ad->urgent_publication ? 'checked' : '' }}
                    disabled
                >
                <label class="form-check-label" for="urgent_publication">
                    Activar publicación como urgente
                </label>
            </div>

            <small class="text-danger fw-bold">
                Precio por publicación urgente: S/. {{ number_format($urgentPrice, 2) }}
            </small>
        </div>

        {{-- PUBLICACIÓN DESTACADA --}}
        <div class="field-card d-none" id="featuredContainer">
            <label class="fw-semibold">¿Publicación destacada?</label>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="featured_publication"
                    name="featured_publication"
                    value="1"
                    {{ $ad->featured_publication ? 'checked' : '' }}
                    disabled
                >
                <label class="form-check-label">
                    Activar publicación como destacada
                </label>
            </div>

            <small class="text-danger fw-bold">
                Precio por publicación destacada: S/. {{ number_format($featuredPrice, 2) }}
            </small>
        </div>

        {{-- PUBLICACIÓN EN ESTRENO --}}
        <div class="field-card d-none" id="premiereContainer">
            <label class="fw-semibold">¿Publicación en estreno?</label>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="premiere_publication_switch"
                    {{ $ad->premiere_publication ? 'checked' : '' }}
                    disabled
                >

                <input
                    type="hidden"
                    name="premiere_publication"
                    id="premiere_publication"
                    value="{{ $ad->premiere_publication ? 1 : 0 }}"
                >

                <label class="form-check-label">
                    Activar publicación como estreno
                </label>
            </div>

            <small class="text-danger fw-bold">
                Precio de publicación estreno: S/. {{ number_format($premierePrice, 2) }}
            </small>
        </div>

        {{-- PUBLICACIÓN SEMINUEVO --}}
        <div class="field-card d-none" id="semiNewContainer">
            <label class="fw-semibold">¿Publicación seminuevo?</label>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="semi_new_publication"
                    name="semi_new_publication"
                    value="1"
                    {{ $ad->semi_new_publication ? 'checked' : '' }}
                    disabled
                >
                <label class="form-check-label">
                    Activar publicación como seminuevo
                </label>
            </div>

            <small class="text-danger fw-bold">
                Precio publicación seminuevo: S/. {{ number_format($semiNewPrice, 2) }}
            </small>
        </div>

        {{-- PUBLICACIÓN NUEVO --}}
        <div class="field-card d-none" id="newContainer">
            <label class="fw-semibold">¿Publicación nueva?</label>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="new_publication"
                    name="new_publication"
                    value="1"
                    {{ $ad->new_publication ? 'checked' : '' }}
                    disabled
                >
                <label class="form-check-label">
                    Activar publicación como nuevo
                </label>
            </div>

            <small class="text-danger fw-bold">
                Precio publicación nuevo: S/. {{ number_format($newPrice, 2) }}
            </small>
        </div>

        {{-- PUBLICACIÓN DISPONIBLE --}}
        <div class="field-card d-none" id="availableContainer">
            <label class="fw-semibold">¿Publicación disponible?</label>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="available_publication"
                    name="available_publication"
                    value="1"
                    {{ $ad->available_publication ? 'checked' : '' }}
                    disabled
                >
                <label class="form-check-label">
                    Activar publicación como disponible
                </label>
            </div>

            <small class="text-danger fw-bold">
                Precio publicación disponible: S/. {{ number_format($availablePrice, 2) }}
            </small>
        </div>

        {{-- ETIQUETAS  TOP  --}}
        <div class="field-card d-none" id="topContainer">
            <label class="fw-semibold">¿Publicación TOP?</label>

            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="top_publication"
                    name="top_publication"
                    value="1"
                    {{ $ad->top_publication ? 'checked' : '' }}
                    disabled
                >
                <label class="form-check-label">
                    Activar publicación como TOP
                </label>
            </div>

            <small class="text-danger fw-bold">
                Precio publicación TOP: S/. {{ number_format($topPrice, 2) }}
            </small>
        </div>

        {{-- RESUMEN --}}
        <div class="field-card" id="summaryContainer">
            <h5 class="fw-bold mb-3">Resumen de Pago</h5>

            <div class="d-flex justify-content-between">
                <span class="fw-semibold">Costo total:</span>
                <span id="summaryTotalCost" class="fw-bold text-danger">S/. 0.00</span>
            </div>
        </div>

        {{-- IMÁGENES ACTUALES - EMPLEOS--}}
        @if($isEmployment)

            <div class="field-card">
                <label class="fw-semibold">Imagen actual</label>

                @if($ad->images->first())
                    <img src="{{ asset($ad->images->first()->image) }}" class="img-thumb">
                @endif
            </div>

            {{-- AGREGAR NUEVAS IMÁGENES --}}
            @if(auth()->user()->role_id === 1)
            <div class="field-card">
                <label class="fw-semibold">Elegir imagen</label>

                <button type="button"
                        class="btn btn-outline-info open-images-modal">
                    Elegir imágenes
                </button>

                <div id="selectedPreview" class="d-none mt-2">
                    <div id="selectedPreviewListEmployment"></div>
                </div>

                <input type="hidden" name="selected_subcategory_image_employment" id="selected_subcategory_image_employment">
            </div>

            @if($isEmployment)
                <input type="hidden" name="remove_images" id="remove_images">
            @endif

            @endif
        @endif


        @if(!$isEmployment)

        {{-- IMÁGENES ACTUALES - OTROS --}}
        <div class="field-card">
            <label class="fw-semibold">Imágenes actuales</label>

            <div class="d-flex flex-wrap gap-3">
                @foreach ($ad->images as $img)
                    <div class="position-relative image-wrapper">
                        <img src="{{ asset($img->image) }}" class="img-thumb">

                        <button type="button"
                            class="delete-img-btn"
                            onclick="markImageForRemoval({{ $img->id }}, this)">
                            ×
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- NUEVAS DESDE GALERÍA --}}
        <div class="field-card">
            <label class="fw-semibold">Agregar imágenes</label>

            <button type="button"
                    class="btn btn-outline-info open-images-modal">
                Elegir imágenes
            </button>

            <div id="selectedPreviewListGeneral" class="d-flex flex-wrap gap-2 mt-2"></div>

            <input type="hidden" name="selected_subcategory_image_general" id="selected_subcategory_image_general">
        </div>

        <input type="hidden" name="remove_images" id="remove_images">

        @endif


        <!-- BOTÓN -->
        <button class="btn btn-danger w-100 py-2 fw-semibold mt-3" id="submitBtn">
            Guardar Cambios
        </button>

    </form>

<div class="modal fade" id="modalSubcategoryImages" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Elegir imagen de referencia
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="modalImagesGrid" class="image-grid">
                    <small class="text-muted">Cargando imágenes...</small>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button class="btn btn-dark" id="confirmImage">
                    Usar imagen
                </button>
            </div>

        </div>
    </div>
</div>

</div>

<script>
const currentSubcategory = "{{ $ad->ad_subcategories_id }}";
const selectedImageIds  = "{{ $ad->selected_subcategory_image }}";
let referenceImages = [];
let MAX_IMAGES = 5;
let imagesToDelete = [];
const currentSubcategoryId = "{{ $ad->ad_subcategories_id }}";

const removeInput = document.getElementById('remove_images');
if (removeInput) {
    removeInput.value = '';
}


function markImageForRemoval(id, btn) {

    btn.closest('.image-wrapper').style.opacity = 0.4;

    if (!imagesToDelete.includes(id)) {
        imagesToDelete.push(id);
    }

    document.getElementById('remove_images').value =
        JSON.stringify(imagesToDelete);
}

// LÓGICA PARA SINCRONIZAR SWITCH DE PUBLICACIÓN
document.getElementById('premiere_publication_switch')
    ?.addEventListener('change', function () {
        document.getElementById('premiere_publication').value = this.checked ? 1 : 0;
    });

// LÓGICA DE CÁLCULO DE COSTOS SEGUN LAS ETIQUETAS
function recalculateEditTotal() {

    const days = parseInt(document.getElementById("days_active")?.value || 0);

    if (!days || days <= 0) {
        document.getElementById("totalCost").value = "";
        document.getElementById("summaryTotalCost").textContent = "S/. 0.00";
        return;
    }

    let total = subcatPrice * days;

    if (document.getElementById("urgent_publication")?.checked) {
        total += urgentPrice;
    }

    if (document.getElementById("featured_publication")?.checked) {
        total += featuredPrice;
    }

    if (document.getElementById("premiere_publication_switch")?.checked) {
        total += premierePrice;
    }

    if (document.getElementById("semi_new_publication")?.checked) {
        total += semiNewPrice;
    }

    if (document.getElementById("new_publication")?.checked) {
        total += newPrice;
    }

    if (document.getElementById("available_publication")?.checked) {
        total += availablePrice;
    }

    if (document.getElementById("top_publication")?.checked) {
        total += topPrice;
    }

    document.getElementById("totalCost").value = `S/. ${total.toFixed(2)}`;
    document.getElementById("summaryTotalCost").textContent = `S/. ${total.toFixed(2)}`;
}

// LÓGICA DE ETIQUETAS SEGÚN CATEGORÍA
document.addEventListener('DOMContentLoaded', () => {

    //const openBtn = document.getElementById('openImagesModalGeneral');
    const confirmBtn = document.getElementById('confirmImage');
    const imagesGrid = document.getElementById('modalImagesGrid');

    const previewEmployment = document.getElementById('selectedPreviewListEmployment');
    const previewGeneral = document.getElementById('selectedPreviewListGeneral');
    const previewBox = document.getElementById('selectedPreview');

    const inputEmployment = document.getElementById('selected_subcategory_image_employment');
    const inputGeneral = document.getElementById('selected_subcategory_image_general');
    const removeInput = document.getElementById('remove_images');

    const isEmployment = {{ $isEmployment ? 'true' : 'false' }};

    let selectedImages = [];

    const modal = new bootstrap.Modal(
        document.getElementById('modalSubcategoryImages')
    );

    // =========================
    // ABRIR MODAL
    // =========================
    document.querySelectorAll('.open-images-modal').forEach(btn => {

    btn.addEventListener('click', () => {

        selectedImages = [];
        imagesGrid.innerHTML = 'Cargando...';

        fetch(`/advertising/subcategories/{{ $ad->ad_subcategories_id }}/images`)
            .then(r => r.json())
            .then(images => {

                imagesGrid.innerHTML = '';

                images.forEach(img => {

                    const card = document.createElement('div');
                    card.className = 'image-card';
                    card.innerHTML = `<img src="/${img.image}">`;

                    card.onclick = () => {

                        if (isEmployment) {
                            selectedImages = [img];

                            document
                                .querySelectorAll('#modalImagesGrid .image-card')
                                .forEach(c => c.classList.remove('selected'));

                            card.classList.add('selected');
                        }
                        else {
                            if (selectedImages.find(i => i.id === img.id)) return;
                            selectedImages.push(img);
                            card.classList.add('selected');

                        }
                    };

                    imagesGrid.appendChild(card);
                });
            });

        modal.show();
    });

});

    // =========================
    // CONFIRMAR
    // =========================
    confirmBtn?.addEventListener('click', () => {

        if (!selectedImages.length) return;

        const ids = selectedImages.map(i => i.id).join(',');

        // LIMPIAR PREVIEW
        previewEmployment && (previewEmployment.innerHTML = '');
        previewGeneral && (previewGeneral.innerHTML = '');

        selectedImages.forEach(img => {

            const html = `
                <div class="image-card border">
                    <img src="/${img.image}">
                </div>
            `;

            if (isEmployment && previewEmployment) {
                previewEmployment.innerHTML = html;
            }

            if (!isEmployment && previewGeneral) {
                previewGeneral.insertAdjacentHTML('beforeend', html);
            }
        });

        previewBox?.classList.remove('d-none');

        // INPUTS
        if (isEmployment && inputEmployment) {
            inputEmployment.value = ids;
            removeInput.value = 'all'; 
        }

        if (!isEmployment && inputGeneral) {
            inputGeneral.value = ids; 
        }

        modal.hide();
    });

});


// LÓGICA DE MONTO VISIBLE
document.addEventListener('DOMContentLoaded', function () {

    const amountInput = document.getElementById('amountInput');
    const amountVisibleCheckbox = document.getElementById('amountVisibleCheckbox');
    const amountVisibleInput = document.getElementById('amountVisibleInput');
    const amountTextSelect = document.getElementById('amountTextSelect');
    const amountTextInput = document.getElementById('amountTextInput');

    function toggleAmount(visible) {
        if (visible) {
            amountInput.disabled = false;
            amountInput.required = true;
            amountTextSelect.classList.add('d-none');

            amountVisibleInput.value = 1;
            amountTextInput.value = '';
        } else {
            amountInput.disabled = true;
            amountInput.required = false;
            amountInput.value = '';

            amountTextSelect.classList.remove('d-none');
            amountTextInput.value = amountTextSelect.value || 'No especificado';
        }
    }

    // estado inicial desde BD
    toggleAmount(amountVisibleCheckbox.checked);

    amountVisibleCheckbox.addEventListener('change', function () {
        toggleAmount(this.checked);
    });

    amountTextSelect.addEventListener('change', function () {
        if (!amountVisibleCheckbox.checked) {
            amountTextInput.value = this.value;
        }
    });

});

document.addEventListener("DOMContentLoaded", () => {

    const daysInput = document.getElementById("days_active");

    const checkboxes = [
        "urgent_publication",
        "featured_publication",
        "premiere_publication_switch",
        "semi_new_publication",
        "new_publication",
        "available_publication",
        "top_publication"
    ];

    // Precios desde backend
    const prices = {
        base: parseFloat("{{ $subcategories->firstWhere('id', $ad->ad_subcategories_id)->price ?? 0 }}"),
        urgent: parseFloat("{{ $urgentPrice }}"),
        featured: parseFloat("{{ $featuredPrice }}"),
        premiere: parseFloat("{{ $premierePrice }}"),
        semiNew: parseFloat("{{ $semiNewPrice }}"),
        new: parseFloat("{{ $newPrice }}"),
        available: parseFloat("{{ $availablePrice }}"),
        top: parseFloat("{{ $topPrice }}"),
    };

    function recalculateEditTotal() {

        const days = parseInt(daysInput.value) || 1;
        let total = prices.base * days;

        if (document.getElementById("urgent_publication")?.checked) total += prices.urgent;
        if (document.getElementById("featured_publication")?.checked) total += prices.featured;
        if (document.getElementById("premiere_publication_switch")?.checked) total += prices.premiere;
        if (document.getElementById("semi_new_publication")?.checked) total += prices.semiNew;
        if (document.getElementById("new_publication")?.checked) total += prices.new;
        if (document.getElementById("available_publication")?.checked) total += prices.available;
        if (document.getElementById("top_publication")?.checked) total += prices.top;

        document.getElementById("totalCost").value = `S/. ${total.toFixed(2)}`;
        document.getElementById("summaryTotalCost").textContent = `S/. ${total.toFixed(2)}`;
    }

    // EVENTOS
    daysInput.addEventListener("input", recalculateEditTotal);

    checkboxes.forEach(id => {
        document.getElementById(id)?.addEventListener("change", recalculateEditTotal);
    });

    // sincronizar estreno
    document.getElementById("premiere_publication_switch")
        ?.addEventListener("change", function () {
            document.getElementById("premiere_publication").value = this.checked ? 1 : 0;
        });

    recalculateEditTotal();
});

/*
document.addEventListener("DOMContentLoaded", () => {

    const imagesContainer  = document.getElementById('imagesContainer');
    const imagesGrid       = document.getElementById('modalImagesGrid');
    const previewBox       = document.getElementById('selectedPreview');
    const previewList      = document.getElementById('selectedPreviewList');
    const openImagesBtn    = document.getElementById('openImagesModal');
    const confirmBtn       = document.getElementById('confirmImage');

    let tempSelectedImages = [];

    if (!currentSubcategory || !imagesContainer) return;

    imagesContainer.classList.remove('d-none');

    const modal = new bootstrap.Modal(
        document.getElementById('modalSubcategoryImages')
    );

    // ABRIR MODAL (MISMO COMPORTAMIENTO QUE CREATE)
    openImagesBtn?.addEventListener('click', () => {

        imagesGrid.innerHTML = `<small class="text-muted">Cargando imágenes...</small>`;
        tempSelectedImages = [];

        fetch(`/advertising/subcategories/${currentSubcategory}/images`)
            .then(r => r.json())
            .then(images => {

                imagesGrid.innerHTML = '';

                images.forEach(img => {

                    const card = document.createElement('div');
                    card.className = 'image-card';
                    card.innerHTML = `<img src="/${img.image}">`;

                    card.addEventListener('click', () => {

                        if (tempSelectedImages.find(i => i.id === img.id)) return;

                        tempSelectedImages.push(img);

                        const preview = document.createElement('div');
                        preview.className = 'image-card position-relative';
                        preview.innerHTML = `
                            <img src="/${img.image}">
                            <button class="delete-img-btn">×</button>
                        `;

                        preview.querySelector('button').onclick = () => {
                            preview.remove();
                            tempSelectedImages =
                                tempSelectedImages.filter(i => i.id !== img.id);
                        };

                        previewList.appendChild(preview);
                    });

                    imagesGrid.appendChild(card);
                });
            });

        modal.show();
    });

    // CONFIRMAR
    confirmBtn?.addEventListener('click', () => {

        if (!tempSelectedImages.length) return;

        selectedInput.value = tempSelectedImages[0].id;

        // limpiar eliminaciones previas
        document.getElementById('remove_images').value = '';

        previewList.innerHTML = `
            <div class="image-card border border-dark" style="max-width:120px">
                <img src="/${tempSelectedImages[0].image}">
            </div>
        `;

        previewBox.classList.remove('d-none');
        modal.hide();
    });

});
*/
function deleteImage(id){
    if(!confirm("¿Eliminar esta imagen?")) return;

    fetch(`/advertising/my-ads/delete-image/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(() => location.reload());
}

@if (session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: '¡Éxito!',
    text: "{{ session('success') }}",
    confirmButtonColor: '#3085d6'
});
</script>
@endif

@if (session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: "{{ session('error') }}",
    confirmButtonColor: '#d33'
});
</script>
@endif

</script>

<style>
/* =========================
   GRID DE IMÁGENES DEL MODAL
   ========================= */
.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 12px;
}

.image-grid .image-card {
    width: 100%;
    height: 140px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color .2s ease, transform .15s ease;
    background: #f8f9fa;
}

/* Hover */
.image-grid .image-card:hover {
    border-color: #0d6efd; /* azul */
    transform: scale(1.02);
}

/* ✅ Seleccionada */
.image-grid .image-card.selected {
    border-color: #198754; /* verde bootstrap */
    box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.25);
}

/* Imagen */
.image-grid .image-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}


/* =========================
   PREVIEW SELECCIONADO
   ========================= */
#selectedPreviewListEmployment,
#selectedPreviewListGeneral {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

#selectedPreviewListEmployment .image-card,
#selectedPreviewListGeneral .image-card {
    width: 120px;
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #198754; /* verde fijo */
    background: #f8f9fa;
}

#selectedPreviewListEmployment img,
#selectedPreviewListGeneral img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

</style>

@endsection
