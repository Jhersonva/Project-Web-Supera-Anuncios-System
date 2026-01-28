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
            <select id="categorySelect" name="category_id" class="form-select">
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
            <select id="subcategorySelect" name="subcategory_id" class="form-select">
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
            <input type="text" class="form-control" name="title" value="{{ $ad->title }}">
        </div>

        {{-- Descripción --}}
        <div class="field-card" id="descriptionContainer">
            <label class="fw-semibold">Descripción</label>
            <textarea name="description" class="form-control" rows="4">{{ $ad->description }}</textarea>
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
                       value="{{ $existing ? $existing->value : '' }}">
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
            >

            <label class="fw-semibold mt-2">Provincia</label>
            <input
                type="text"
                name="province"
                class="form-control"
                placeholder="Ej: Lima"
                value="{{ $ad->province  }}"
            >

            <label class="fw-semibold mt-2">Distrito</label>
            <input
                type="text"
                name="district"
                class="form-control"
                placeholder="Ej: San Juan de Miraflores"
                value="{{ $ad->district }}"
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
                value="{{ old('whatsapp', $ad->whatsapp ?? $user->whatsapp ?? '') }}"
                placeholder="Ej: +51 999888777"
            >

            <label class="fw-semibold mt-2">Contacto vía Llamada</label>
            <input
                type="text"
                name="call_phone"
                class="form-control"
                value="{{ old('call_phone', $ad->call_phone ?? $user->call_phone ?? '') }}"
                placeholder="Ej: 983777666"
                inputmode="numeric"
                pattern="[0-9]{9}"
                maxlength="9"
            >

        </div>

        {{-- MONTO --}}
        <div class="field-card {{ isset($ad) || $errors->has('amount') ? '' : 'd-none' }}" id="amountContainer">

            <label class="fw-semibold mb-2">Monto / Precio / Sueldo *</label>

            <div class="row g-3 align-items-start">

                <!-- MONEDA + MONTO -->
                <div class="col-12 col-md-8">

                    <div
                        id="amountValueContainer"
                        class="{{ old('amount_visible', $ad->amount_visible ?? 1) ? '' : 'd-none' }}"
                    >
                        <div class="input-group">

                            {{-- MONEDA --}}
                            <select
                                name="amount_currency"
                                id="amountCurrency"
                                class="form-select"
                                style="max-width: 130px"
                            >
                                <option value="PEN"
                                    {{ old('amount_currency', $ad->amount_currency ?? 'PEN') === 'PEN' ? 'selected' : '' }}>
                                    S/ PEN
                                </option>

                                <option value="USD"
                                    {{ old('amount_currency', $ad->amount_currency ?? '') === 'USD' ? 'selected' : '' }}>
                                    $ USD
                                </option>
                            </select>

                            {{-- MONTO --}}
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="amount"
                                id="amountInput"
                                class="form-control @error('amount') is-invalid @enderror"
                                value="{{ old('amount', $ad->amount_visible ? $ad->amount : '') }}"
                            >
                        </div>

                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TEXTO CUANDO EL MONTO ESTÁ OCULTO --}}
                    <select
                        id="amountTextSelect"
                        class="form-select mt-2 {{ old('amount_visible', $ad->amount_visible ?? 1) ? 'd-none' : '' }}"
                    >
                        <option value="">Selecciona texto por defecto...</option>

                        @foreach([
                            'Sueldo a tratar',
                            'Monto a tratar',
                            'Sueldo por comisiones',
                            'No especificado'
                        ] as $text)
                            <option
                                value="{{ $text }}"
                                {{ old('amount_text', $ad->amount_text ?? '') === $text ? 'selected' : '' }}
                            >
                                ({{ $text }})
                            </option>
                        @endforeach
                    </select>

                    <small class="text-muted d-block mt-1">
                        Si ocultas el monto, el público verá el texto seleccionado o "No especificado".
                    </small>
                </div>

                {{-- SWITCH --}}
                <div class="col-12 col-md-4 d-flex align-items-center">
                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="amountVisibleCheckbox"
                            {{ old('amount_visible', $ad->amount_visible ?? 1) ? 'checked' : '' }}
                        >
                        <label class="form-check-label ms-2">
                            Mostrar monto
                        </label>
                    </div>
                </div>

            </div>

            {{-- HIDDEN --}}
            <input
                type="hidden"
                name="amount_visible"
                id="amountVisibleInput"
                value="{{ old('amount_visible', $ad->amount_visible ?? 1) }}"
            >

            <input
                type="hidden"
                name="amount_text"
                id="amountTextInput"
                value="{{ old('amount_text', $ad->amount_text ?? '') }}"
            >
        </div>


        {{-- COSTOS --}}
        <div class="field-card" id="costContainer">
            {{-- DÍAS PUBLICACIÓN / COSTOS --}}
            <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="costContainer">

                <label class="fw-semibold">
                    Días de publicación *
                </label>

                <div class="form-text text-primary d-flex align-items-center gap-1">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Solo se permiten publicaciones de 2 días en adelante</span>
                </div>

                <input
                    type="number"
                    min="2"
                    step="1"
                    name="days_active"
                    id="days_active"
                    class="form-control"
                    value="{{ old('days_active', $ad->days_active ?? 2) }}"
                    data-min="2"
                    required
                >

            </div>

            <small class="text-muted">Indica cuántos días deseas que tu anuncio esté activo.</small>
            <br>

            <label class="fw-semibold mt-2">Costo por día</label>
            <input type="text" id="pricePerDay" class="form-control mb-2" value="S/. {{ $subcategories->firstWhere('id', $ad->ad_subcategories_id)->price ?? 0 }}">

            <label class="fw-semibold mt-2">Costo total</label>
            <input type="text" id="totalCost" class="form-control mb-2" value="S/. 0.00">

            <label class="fw-semibold mt-2">Fecha de expiración</label>
            <input type="text" id="expiresAt" class="form-control"
                   value="{{ $ad->expires_at }}">
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

        {{-- IMÁGENES DEL ANUNCIO (EDIT) --}}
        <div class="field-card" id="imagesContainer">

            <label class="fw-semibold mb-2">Imágenes del anuncio</label>

            @if($ad->images->count())
                <div class="d-flex flex-wrap gap-2 mb-3">

                    @foreach($ad->images as $image)
                        <div class="position-relative image-wrapper">

                            <img
                                src="{{ asset($image->image) }}"
                                class="rounded border"
                                style="width:120px;height:120px;object-fit:cover;"
                            >

                            @if($image->is_main)
                                <span class="badge bg-primary position-absolute top-0 start-0">
                                    Principal
                                </span>
                            @endif

                            <button
                                type="button"
                                class="delete-img-btn"
                                onclick="markImageForRemoval({{ $image->id }}, this)">
                                ×
                            </button>

                        </div>
                    @endforeach

                </div>
            @endif

            {{-- PREVIEW DE NUEVAS IMÁGENES (UNA SOLA VEZ) --}}
            <div id="newImagesPreview" class="d-flex flex-wrap gap-2 mt-3"></div>

            <hr>

            <label class="fw-semibold mt-3">Agregar o reemplazar imágenes</label>

            <input
                type="file"
                name="images[]"
                id="newImagesInput"
                class="form-control"
                accept="image/*"
                multiple
            >

            <small class="text-muted d-block">
                Máximo 5 imágenes en total.
            </small>
        </div>

        <input type="hidden" name="remove_images" id="remove_images">


        <!-- BOTÓN -->
        <button class="btn btn-danger w-100 py-2 fw-semibold mt-3" id="submitBtn">
            Guardar Cambios
        </button>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    const wrapper = btn.closest('.image-wrapper');
    wrapper.classList.add('removed');
    wrapper.style.opacity = '0.4';

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
});


// LÓGICA DE MONTO VISIBLE
document.addEventListener('DOMContentLoaded', () => {

    const amountContainer        = document.getElementById('amountContainer');
    if (!amountContainer) return;

    const amountInput            = document.getElementById('amountInput');
    const amountVisibleCheckbox  = document.getElementById('amountVisibleCheckbox');
    const amountVisibleInput     = document.getElementById('amountVisibleInput');
    const amountTextSelect       = document.getElementById('amountTextSelect');
    const amountTextInput        = document.getElementById('amountTextInput');

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

            amountVisibleInput.value = 0;
            amountTextInput.value = amountTextSelect.value || 'No especificado';
        }
    }

    // Estado inicial (EDIT)
    toggleAmount(amountVisibleCheckbox.checked);

    // Switch mostrar / ocultar
    amountVisibleCheckbox.addEventListener('change', function () {
        toggleAmount(this.checked);
        updatePreview?.();
    });

    // Cambio de texto
    amountTextSelect.addEventListener('change', function () {
        if (!amountVisibleCheckbox.checked) {
            amountTextInput.value = this.value || 'No especificado';
            updatePreview?.();
        }
    });

});

document.addEventListener("DOMContentLoaded", () => {

    const daysInput = document.getElementById("days_active");

    if (daysInput) {

        daysInput.addEventListener("input", () => {
            const value = parseInt(daysInput.value, 10);

            if (value >= 2) {
                calculateDatesAndCosts?.();
                recalculateEditTotal?.();
            }
        });

        daysInput.addEventListener("blur", () => {
            const value = parseInt(daysInput.value, 10);

            if (!value || value < 2) {
                daysInput.value = 2;
            }

            calculateDatesAndCosts?.(true);
            recalculateEditTotal?.();
        });
    }

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

document.addEventListener("DOMContentLoaded", () => {

    const subcategory = @json(
        $subcategories->firstWhere('id', $ad->ad_subcategories_id)
    );

    if (!subcategory) return;

    const tagMap = {
        is_urgent:    'urgentContainer',
        is_featured:  'featuredContainer',
        is_premiere:  'premiereContainer',
        is_semi_new:  'semiNewContainer',
        is_new:       'newContainer',
        is_available: 'availableContainer',
        is_top:       'topContainer',
    };

    // ocultar todos primero
    Object.values(tagMap).forEach(id => {
        document.getElementById(id)?.classList.add('d-none');
    });

    // mostrar SOLO los permitidos por la subcategoría
    Object.entries(tagMap).forEach(([flag, containerId]) => {
        if (subcategory[flag]) {
            document.getElementById(containerId)
                ?.classList.remove('d-none');
        }
    });

});

let newImages = [];

const input = document.getElementById('newImagesInput');
const preview = document.getElementById('newImagesPreview');

input.addEventListener('change', function () {

    const files = Array.from(this.files);

    // contar imágenes actuales visibles
    const currentCount =
        document.querySelectorAll('.image-wrapper:not(.removed)').length;

    if (currentCount + newImages.length + files.length > MAX_IMAGES) {
        alert(`Máximo ${MAX_IMAGES} imágenes en total`);
        input.value = '';
        return;
    }

    files.forEach(file => {
        newImages.push(file);
    });

    renderNewImages();
    input.value = '';
});

function renderNewImages() {

    preview.innerHTML = '';

    newImages.forEach((file, index) => {

        const reader = new FileReader();

        reader.onload = e => {
            const div = document.createElement('div');
            div.classList.add('image-wrapper');

            div.innerHTML = `
                <img src="${e.target.result}"
                     class="rounded border"
                     style="width:120px;height:120px;object-fit:cover;">
                <button type="button"
                    class="delete-img-btn"
                    onclick="removeNewImage(${index})">×</button>
            `;

            preview.appendChild(div);
        };

        reader.readAsDataURL(file);
    });
}

function removeNewImage(index) {
    newImages.splice(index, 1);
    renderNewImages();
}

/* Reemplazar archivos reales antes de enviar */
document.querySelector('form').addEventListener('submit', function (e) {

    if (newImages.length === 0) return;

    const dt = new DataTransfer();
    newImages.forEach(file => dt.items.add(file));
    input.files = dt.files;
});

</script>

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
    border-color: #0d6efd;
    transform: scale(1.02);
}

/* Seleccionada */
.image-grid .image-card.selected {
    border-color: #198754; 
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
    border: 2px solid #198754; 
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
