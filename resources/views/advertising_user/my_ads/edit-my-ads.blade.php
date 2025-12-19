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
            <select id="categorySelect" name="category_id" class="form-select" required>
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
            <select id="subcategorySelect" name="subcategory_id" class="form-select" required>
                @foreach($subcategories as $sub)
                    <option value="{{ $sub->id }}" {{ $ad->ad_subcategories_id == $sub->id ? 'selected' : '' }}>
                        {{ $sub->name }} (S/. {{ $sub->price }})
                    </option>
                @endforeach
            </select>
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

        {{-- Ubicación --}}
        <div class="field-card" id="contactLocationContainer">
            <label class="fw-semibold">Ubicación de contacto</label>
            <input type="text" name="contact_location" class="form-control"
                   value="{{ $ad->contact_location }}">
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
                    >

                    <small class="text-muted">
                        Si marcas "Ocultar monto", el público verá "No especificado".
                    </small>
                </div>

                <div style="min-width:170px; display:flex; align-items:center; justify-content:center;">
                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="amountVisibleCheckbox"
                            {{ $ad->amount_visible ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="amountVisibleCheckbox">
                            Mostrar monto
                        </label>
                    </div>
                </div>
            </div>

            {{-- input oculto para backend --}}
            <input
                type="hidden"
                name="amount_visible"
                id="amountVisibleInput"
                value="{{ $ad->amount_visible }}"
            >
        </div>

        {{-- COSTOS --}}
        <div class="field-card" id="costContainer">
            <label class="fw-semibold">Días de publicación *</label>
            <input type="number" min="1" name="days_active" id="days_active" class="form-control"
                   value="{{ $ad->days_active }}" required>

            <small class="text-muted">Indica cuántos días deseas que tu anuncio esté activo.</small>
            <br>

            <label class="fw-semibold mt-2">Costo por día</label>
            <input type="text" id="pricePerDay" class="form-control mb-2" value="S/. {{ $subcategories->firstWhere('id', $ad->ad_subcategories_id)->price ?? 0 }}" readonly>

            <label class="fw-semibold mt-2">Costo total</label>
            <input type="text" id="totalCost" class="form-control mb-2" readonly value="S/. 0.00">

            <label class="fw-semibold mt-2">Fecha de expiración</label>
            <input type="text" id="expiresAt" class="form-control"
                   value="{{ $ad->expires_at }}" readonly>
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

        {{-- IMÁGENES ACTUALES --}}
        <div class="field-card">
            <label class="fw-semibold">Imágenes actuales</label>
            <div class="d-flex flex-wrap gap-3 mt-2">
                @foreach ($ad->images as $img)
                <div class="position-relative">
                    <img src="{{ asset($img->image) }}" class="img-thumb">

                    <button type="button" class="delete-img-btn"
                        onclick="markImageForRemoval({{ $img->id }}, this)">×</button>

                    <div class="form-check mt-1">
                        <input type="radio" name="main_image" value="{{ $img->id }}"
                            class="form-check-input" {{ $img->is_main ? 'checked' : '' }}>
                        <label class="form-check-label">Principal</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- AGREGAR NUEVAS IMÁGENES --}}
        <div class="field-card" id="imagesContainer">
            <label class="fw-semibold">Agregar nuevas imágenes (opcional)</label>
            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
        </div>

        <!-- BOTÓN -->
        <button class="btn btn-danger w-100 py-2 fw-semibold mt-3" id="submitBtn">
            Guardar Cambios
        </button>

        <input type="hidden" name="remove_images" id="remove_images">

    </form>

</div>

<script>
let imagesToDelete = [];

function markImageForRemoval(id, btn) {
    // Ocultar visualmente la imagen
    const container = btn.parentElement;
    container.style.opacity = '0.4';
    container.style.filter = 'grayscale(1)';

    // Marcar para eliminación
    if (!imagesToDelete.includes(id)) {
        imagesToDelete.push(id);
    }

    // Actualizar el hidden input
    document.getElementById('remove_images').value = JSON.stringify(imagesToDelete);
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
document.addEventListener("DOMContentLoaded", () => {

    const tagMap = {
        is_urgent: 'urgentContainer',
        is_featured: 'featuredContainer',
        is_premiere: 'premiereContainer',
        is_semi_new: 'semiNewContainer',
        is_new: 'newContainer',
        is_available: 'availableContainer',
        is_top: 'topContainer',
    };

    const categoryId = document.getElementById('categorySelect')?.value;

    if (!categoryId) return;

    fetch(`/advertising/my-ads/subcategories-with-category/${categoryId}`)
        .then(res => res.json())
        .then(data => {

            // MOSTRAR SOLO LAS ETIQUETAS PERMITIDAS
            Object.entries(tagMap).forEach(([flag, containerId]) => {

                if (data.category[flag]) {
                    const container = document.getElementById(containerId);
                    if (container) {
                        container.classList.remove('d-none');
                    }
                }
            });
        });
});


// LÓGICA DE MONTO VISIBLE
document.addEventListener('DOMContentLoaded', function () {

    const amountInput = document.getElementById('amountInput');
    const amountVisibleCheckbox = document.getElementById('amountVisibleCheckbox');
    const amountVisibleInput = document.getElementById('amountVisibleInput');

    if (!amountInput || !amountVisibleCheckbox || !amountVisibleInput) return;

    function applyAmountVisibility(visible) {

        if (visible) {
            amountInput.disabled = false;
            amountInput.required = true;
            amountVisibleInput.value = 1;

            if (amountInput.dataset.tmpVal) {
                amountInput.value = amountInput.dataset.tmpVal;
                delete amountInput.dataset.tmpVal;
            }

        } else {
            amountInput.dataset.tmpVal = amountInput.value;
            amountInput.value = '';
            amountInput.disabled = true;
            amountInput.required = false;
            amountInput.placeholder = 'No especificado';
            amountVisibleInput.value = 0;
        }
    }

    // inicializar según BD
    applyAmountVisibility(amountVisibleCheckbox.checked);

    // escuchar cambios
    amountVisibleCheckbox.addEventListener('change', function () {
        applyAmountVisibility(this.checked);
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

@endsection
