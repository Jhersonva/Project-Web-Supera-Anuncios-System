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
            <label class="fw-semibold">Monto / Precio / Sueldo *</label>
            <input type="number" step="0.01" min="0" name="amount" class="form-control"
                   value="{{ $ad->amount }}" required>
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
        <div class="field-card" id="urgentContainer">
            <label class="fw-semibold">¿Publicación urgente?</label>

            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="urgent_publication"
                       name="urgent_publication" value="1"
                       {{ $ad->urgent_publication ? 'checked' : '' }}>
                <label class="form-check-label" for="urgent_publication">
                    Activar publicación urgente
                </label>
            </div>

            <small class="text-danger fw-bold">
                Precio urgente: S/. {{ number_format($urgentPrice, 2) }}
            </small>
        </div>

        {{-- RESUMEN --}}
        <div class="field-card" id="summaryContainer">
            <h5 class="fw-bold mb-3">Resumen de Pago</h5>

            <div class="d-flex justify-content-between">
                <span class="fw-semibold">Costo total:</span>
                <span id="summaryTotalCost" class="fw-bold text-danger">S/. 0.00</span>
            </div>

            <div class="d-flex justify-content-between mt-2">
                <span class="fw-semibold">Tu saldo:</span>
                <span class="fw-bold text-success">S/. {{ number_format(auth()->user()->virtual_wallet, 2) }}</span>
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

document.addEventListener("DOMContentLoaded", function () {

    const daysInput = document.getElementById("days_active");
    const pricePerDay = parseFloat("{{ $subcategories->firstWhere('id', $ad->ad_subcategories_id)->price ?? 0 }}");
    const urgentPrice = parseFloat("{{ $urgentPrice }}");

    const urgentCheckbox = document.getElementById("urgent_publication");

    const totalCostInput = document.getElementById("totalCost");
    const summaryTotalCost = document.getElementById("summaryTotalCost");
    const expiresAt = document.getElementById("expiresAt");


    function calcular() {

        let days = parseInt(daysInput.value) || 1;
        let total = pricePerDay * days;

        if (urgentCheckbox.checked) {
            total += urgentPrice;
        }

        // actualizar costo total
        totalCostInput.value = `S/. ${total.toFixed(2)}`;
        summaryTotalCost.textContent = `S/. ${total.toFixed(2)}`;

        // calcular expiración
        let now = new Date();
        now.setDate(now.getDate() + days);

        const yyyy = now.getFullYear();
        const mm = (now.getMonth() + 1).toString().padStart(2, '0');
        const dd = now.getDate().toString().padStart(2, '0');

        expiresAt.value = `${yyyy}-${mm}-${dd}`;
    }

    // eventos
    daysInput.addEventListener("input", calcular);
    urgentCheckbox.addEventListener("change", calcular);

    // ejecutar al cargar
    calcular();
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
</script>

@endsection
