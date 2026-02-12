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
    @php
        $lockEdit = in_array($ad->status, ['publicado', 'pendiente']);
    @endphp

    <form id="adForm" action="{{ route('my-ads.updateAd', $ad->id) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="return_to" value="{{ url()->previous() }}">
        @csrf

        <input type="hidden" name="category_id" value="{{ $ad->ad_categories_id }}">
        <input type="hidden" name="subcategory_id" value="{{ $ad->ad_subcategories_id }}">
        <input type="hidden" name="days_active" value="{{ $ad->days_active }}">

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

            <label class="fw-semibold mt-2">Distrito</label>
            <input
                type="text"
                name="district"
                class="form-control"
                placeholder="Ej: San Juan de Miraflores"
                value="{{ $ad->district }}"
            >

            <label class="fw-semibold mt-2">Provincia</label>
            <input
                type="text"
                name="province"
                class="form-control"
                placeholder="Ej: Lima"
                value="{{ $ad->province  }}"
            >

            <label class="fw-semibold">Departamento</label>
            <input
                type="text"
                name="department"
                class="form-control"
                placeholder="Ej: Lima"
                value="{{ $ad->department }}"
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
                placeholder="Ej: 999888777"
                required
                inputmode="numeric"
                pattern="[0-9]{9}"
                maxlength="9"
                value="{{ old('whatsapp', $ad->whatsapp ?? $user->whatsapp ?? '') }}"
            >

            <label class="fw-semibold mt-2">Contacto vía Llamada</label>
            <input
                type="text"
                name="call_phone"
                class="form-control"
                placeholder="Ej: 983777666"
                required
                inputmode="numeric"
                pattern="[0-9]{9}"
                maxlength="9"
                value="{{ old('call_phone', $ad->call_phone ?? $user->call_phone ?? '') }}"
            >

        </div>

        {{-- MONTO --}}
        <div class="field-card {{ isset($ad) || $errors->has('amount') ? '' : 'd-none' }}" id="amountContainer">

            <label class="fw-semibold mb-2">Monto / Precio / Sueldo *</label>

            <div class="row g-3 align-items-start">

                <!-- MONEDA + MONTO -->
                <div class="col-12 col-md-8">

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
        <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="costContainer">
            {{-- DÍAS PUBLICACIÓN / COSTOS --}}

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

            <small class="text-muted">Indica cuántos días deseas que tu anuncio esté activo.</small>
            <br>

            <label class="fw-semibold mt-2">Costo por día</label>
            <input type="text" id="pricePerDay" class="form-control mb-2" value="S/. {{ $subcategories->firstWhere('id', $ad->ad_subcategories_id)->price ?? 0 }}" readonly>

            <label class="fw-semibold mt-2">Costo total</label>
            <input type="text" id="totalCost" class="form-control mb-2" value="S/. 0.00" readonly>

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

        {{-- ANUNCIO VERIFICADO --}}
        @php
            $showVerified = isset($ad) && $ad->ad_categories_id != 1;
        @endphp

        <div class="field-card {{ $showVerified ? '' : 'd-none' }}" id="verifiedContainer">

            <label class="fw-semibold">¿Deseas verificar tu anuncio?</label>

            <div class="form-check form-switch mb-2">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="verification_requested"
                    name="verification_requested"
                    value="1"
                    {{ isset($ad) && $ad->verification_requested ? 'checked' : '' }}
                >
                <label class="form-check-label" for="verification_requested">
                    Marcar anuncio como verificado
                </label>
            </div>

            <p class="text-muted small mb-2">
                Al activar esta opción, autorizas que este anuncio sea revisado y pueda
                mostrarse como <strong>ANUNCIO VERIFICADO</strong>.
            </p>

            <button type="button"
                    id="confirmVerifiedBtn"
                    class="btn btn-outline-danger btn-sm w-100">
                <i class="fa-solid fa-shield-check"></i>
                Confirmar verificación del anuncio
            </button>

            <small class="text-muted d-block mt-2">
                Disponible solo para Inmuebles y Vehículos / Maquinarias
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
                <span class="fw-bold text-success">
                    S/. {{ number_format($virtualWallet, 2) }}
                </span>
            </div>

            @if(isset($summaryTotalCost) && $summaryTotalCost > $virtualWallet)
                        <small class="text-danger d-block mt-2 fw-semibold">
                            ⚠ Saldo insuficiente para publicar este anuncio
                        </small>
                    @else
                        <small class="text-muted d-block mt-2">
                            El costo se calcula según los días y las opciones seleccionadas.
                        </small>
                    @endif
        </div>

        {{-- IMÁGENES DEL ANUNCIO (EDIT) --}}
        <div class="field-card" id="imagesContainer">

            <label class="fw-semibold mb-2">Imágenes del anuncio</label>

            <button
                type="button"
                class="btn btn-outline-primary mt-2 mb-3"
                id="openCropperBtn"
                disabled
            >
                🖼️ Cuadrar imagen
            </button>

            <!-- IMÁGENES EXISTENTES -->
            @if(isset($ad) && $ad->images->count())
                <div class="d-flex flex-wrap gap-2 mb-3" id="existingImagesWrapper">

                    @foreach($ad->images as $image)
                        <div class="position-relative image-wrapper"
                            data-image-id="{{ $image->id }}">

                            @php
                                $crop = is_array($image->crop_data) ? $image->crop_data : null;
                            @endphp

                            <div class="draft-crop-box">
                                <img
                                    src="{{ asset($image->image) }}"
                                    @if($crop)
                                        style="
                                            transform:
                                                scale({{ 120 / $crop['width'] }})
                                                translate(-{{ $crop['x'] }}px, -{{ $crop['y'] }}px);
                                        "
                                    @else
                                        style="width:100%;height:100%;object-fit:cover;"
                                    @endif
                                >
                            </div>

                            @if($image->is_main)
                                <span class="badge bg-primary position-absolute top-0 start-0">
                                Principal
                                </span>
                            @endif

                            <button type="button"
                                class="delete-img-btn"
                                onclick="markImageForRemoval({{ $image->id }}, this)">
                            ×
                            </button>

                        </div>
                    @endforeach

                </div>
            @endif

            {{-- PREVIEW DE NUEVAS IMÁGENES (UNA SOLA VEZ) --}}
            <label class="fw-semibold mb-2">Preview de las Imágenes</label>
            <div id="newImagesPreview" class="d-flex flex-wrap gap-2 mt-3"></div>

            <hr>

            <label class="fw-semibold mt-3">Agregar o reemplazar imágenes</label>

            <input
                type="file"
                name="images[]"
                id="ownImagesInput"
                class="form-control"
                accept="image/*"
            >

            <small class="text-muted d-block">
                Máximo 5 imágenes. Si subes nuevas, se agregarán al anuncio.
            </small>
        </div>

        <input type="hidden" name="crop_data" id="crop_data">
        <input type="hidden" name="remove_images" id="remove_images">

        {{-- COMPROBANTE DE PAGO --}}
        @php
            $receiptUser = $ad->user;
        @endphp

        <div class="field-card" id="receiptContainer">

            <h5 class="fw-bold mb-3">Datos del Comprobante de Pago</h5>

            {{-- Tipo de comprobante --}}
            <label class="fw-semibold mb-2">Tipo de comprobante</label>
            <select class="form-select" name="receipt_type" id="receipt_type">
                <option value="boleta" {{ $ad->receipt_type === 'boleta' ? 'selected' : '' }}>
                    Boleta
                </option>
                <option value="factura" {{ $ad->receipt_type === 'factura' ? 'selected' : '' }}>
                    Factura
                </option>
                <option value="nota_venta" {{ $ad->receipt_type === 'nota_venta' ? 'selected' : '' }}>
                    Nota de Venta
                </option>
            </select>

            {{-- BOLETA --}}
            <div id="boletaFields" class="mt-3 d-none">
                <label class="fw-semibold">DNI</label>
                <input type="text" name="dni" class="form-control" maxlength="8"
                    value="{{ $ad->dni }}">

                <label class="fw-semibold mt-2">Nombre Completo</label>
                <input type="text" name="boleta_full_name" id="boleta_full_name"
                    class="form-control"
                    value="{{ $ad->full_name }}">
            </div>

            {{-- FACTURA --}}
            <div id="facturaFields" class="mt-3 d-none">
                <label class="fw-semibold">RUC</label>
                <input type="text" name="ruc" class="form-control" maxlength="11"
                    value="{{ $ad->ruc }}">

                <label class="fw-semibold mt-2">Razón Social</label>
                <input type="text" name="company_name" class="form-control"
                    value="{{ $ad->company_name }}">

                <label class="fw-semibold mt-2">Dirección</label>
                <input type="text" name="address" class="form-control"
                    value="{{ $ad->address }}">
            </div>

            {{-- NOTA DE VENTA --}}
            <div id="notaVentaFields" class="mt-3 d-none">
                <label class="fw-semibold mt-2">Nombre Completo</label>
                <input type="text" name="nota_full_name" id="nota_full_name"
                    class="form-control"
                    value="{{ $ad->full_name }}">
            </div>

        </div>

        <!-- BOTÓN -->
        <button type="button" class="btn btn-danger w-100 py-2 fw-semibold mt-3" id="submitBtn">
            Guardar Cambios
        </button>

    </form>

</div>

<!-- MODAL CROPPER EDIT -->
<div class="modal fade" id="cropperModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Ajustar imagen</h5>
        <!-- Se eliminó la X -->
      </div>

      <div class="modal-body">

        <div class="alert alert-light border rounded-3 mb-3 small">

            <div class="fw-semibold mb-1">
                📌 Ajusta la imagen para tu anuncio
            </div>

            <ul class="mb-0 ps-3">
                <li>El área dentro del marco es lo que se mostrará en el anuncio.</li>
                <li>Puedes mover la imagen para centrar lo más importante.</li>
                <li>El tamaño visible tiene proporción horizontal optimizada para las tarjetas.</li>
                <li>Todo lo que quede fuera del marco no será visible.</li>
            </ul>
        </div>

        <div class="d-flex gap-2 mb-3 flex-wrap" id="thumbsContainer"></div>

        <div class="cropper-wrapper">
          <img id="cropImagePreview">
        </div>

        <div class="text-center text-muted small mt-2">
            Vista previa con proporción real del anuncio (700 x 380)
        </div>

      </div>

      <div class="modal-footer">
        <!-- Se eliminó Cancelar -->
        <button class="btn btn-primary" id="confirmCrop">
          Confirmar
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/cropperjs/dist/cropper.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

@php
$existingImages = $ad->images->map(function ($img) {
    return [
        'id'       => $img->id,
        'src'      => asset($img->image),
        'cropData' => $img->crop_data,
        'is_main'  => $img->is_main,
    ];
})->values();
@endphp

<script>
const previewContainer = document.getElementById('newImagesPreview');
const fileInput = document.getElementById('ownImagesInput');

let cropper = null;
let imagesState = [];
let currentIndex = null;
let tempCropBuffer = {};

const modalEl = document.getElementById('cropperModal');
const modal   = new bootstrap.Modal(modalEl);

const openCropperBtn = document.getElementById('openCropperBtn');
const cropImg = document.getElementById('cropImagePreview');

function updateCropperButtonState() {
    const active = imagesState.filter(i => !i.deleted).length;
    openCropperBtn.disabled = active === 0;
}

// OCULTAR CARGAR IMÁGENES EXISTENTES AL ESTADO
document.addEventListener('DOMContentLoaded', () => {
    if (openCropperBtn) {
        openCropperBtn.style.display = 'none';
    }
});

fileInput.addEventListener('click', function (e) {

    const activeCount = imagesState.filter(i => !i.deleted).length;

    if (activeCount >= MAX_IMAGES) {

        e.preventDefault();
        e.stopPropagation();

        Swal.fire({
            icon: 'warning',
            title: 'Límite alcanzado',
            text: `Solo puedes subir ${MAX_IMAGES} imágenes`
        });

        return false;
    }
});

fileInput.addEventListener('change', e => {

    const file = e.target.files[0];
    if (!file) return;

    const activeCount = imagesState.filter(i => !i.deleted).length;

    if (activeCount >= 5) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite alcanzado',
            text: 'Solo puedes subir 5 imágenes'
        });
        fileInput.value = '';
        return;
    }

    const newImage = {
        uid: crypto.randomUUID(),
        file: file,
        src: null,
        cropData: null,
        deleted: false,
        isExisting: false
    };

    imagesState.push(newImage);

    fileInput.value = '';

    // ABRIR MODAL SOLO CON ESTA IMAGEN
    currentIndex = imagesState.length - 1;

    const reader = new FileReader();

    reader.onload = () => {

        modal.show();

        setTimeout(() => {
            initCropper(reader.result, currentIndex);
        }, 200);

    };

    reader.readAsDataURL(file);
});

function renderNewImagesPreview() {

    previewContainer.innerHTML = '';

    imagesState.forEach((img, index) => {

        if (img.deleted || img.isExisting) return;

        const reader = new FileReader();

        reader.onload = e => {

            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative';
            wrapper.style.width = '120px';
            wrapper.style.height = '120px';

            const image = document.createElement('img');
            image.src = e.target.result;
            image.className = 'rounded border';
            image.style.width = '100%';
            image.style.height = '100%';
            image.style.objectFit = 'cover';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = '×';
            btn.className = 'delete-img-btn';

            btn.onclick = () => {
                img.deleted = true;
                syncFileInputFromState();
                renderNewImagesPreview();
                updateCropperButtonState();
            };

            wrapper.appendChild(image);
            wrapper.appendChild(btn);
            previewContainer.appendChild(wrapper);
        };

        reader.readAsDataURL(img.file);
    });
}

function syncFileInputFromState() {

    const dataTransfer = new DataTransfer();

    imagesState
        .filter(img => !img.deleted && img.file) 
        .forEach(img => {
            dataTransfer.items.add(img.file);
        });

    fileInput.files = dataTransfer.files;
}

/* ABRIR MODAL */
openCropperBtn.addEventListener('click', () => {

    const thumbs = document.getElementById('thumbsContainer');
    thumbs.innerHTML = '';

    imagesState.forEach((img, index) => {
        if (img.deleted) return;

        const t = document.createElement('img');
        t.className = 'crop-thumb';
        if (img.file) {
            const reader = new FileReader();
            reader.onload = e => t.src = e.target.result;
            reader.readAsDataURL(img.file);
        } else {
            t.src = img.src;
        }


        t.onclick = () => {
            saveTempCrop();   
            loadImage(index);
        };

        thumbs.appendChild(t);
    });

    currentIndex = imagesState.findIndex(i => !i.deleted);
    modal.show();
});

modalEl.addEventListener('shown.bs.modal', () => {
    loadImage(currentIndex);
});

//modalEl.addEventListener('hide.bs.modal', saveCurrentCrop);
modalEl.addEventListener('hide.bs.modal', () => {

    // descartar todo lo temporal
    tempCropBuffer = {};

    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
});


/* CARGAR IMAGEN */
function loadImage(index) {
    currentIndex = index;
    const img = imagesState[index];

    if (img.file) {
        const reader = new FileReader();
        reader.onload = e => initCropper(e.target.result, index);
        reader.readAsDataURL(img.file);
    } else {
        initCropper(img.src, index);
    }
}

function initCropper(src, index) {

    cropImg.src = src;
    if (cropper) cropper.destroy();

    cropper = new Cropper(cropImg, {
        aspectRatio: 700 / 380,
        viewMode: 1,
        dragMode: 'move',
        autoCrop: true,
        autoCropArea: 1,
        cropBoxMovable: false,
        cropBoxResizable: false,
        zoomable: true,
        movable: true,

        ready() {
            const saved = imagesState[index];

            // prioridad: buffer temporal
            if (tempCropBuffer[index]) {
                cropper.setData(tempCropBuffer[index]);
            }
            // si no hay buffer, usar crop definitivo
            else if (saved?.cropData) {
                cropper.setData(saved.cropData);
            }
        }
    });
}

function saveTempCrop() {
    if (!cropper || currentIndex === null) return;

    tempCropBuffer[currentIndex] = cropper.getData(true);
}

function saveCurrentCrop() {
    if (!cropper || currentIndex === null) return;
    imagesState[currentIndex].cropData = cropper.getData(true);
}

/* CONFIRMAR ENCUADRE */
document.getElementById('confirmCrop').addEventListener('click', () => {

    if (!cropper || currentIndex === null) return;

    // guardar crop directo
    imagesState[currentIndex].cropData = cropper.getData(true);

    cropper.destroy();
    cropper = null;

    modal.hide();

    syncFileInputFromState();
    renderNewImagesPreview();

    Swal.fire({
        icon: 'success',
        title: 'Imagen ajustada',
        timer: 800,
        showConfirmButton: false
    });
});

const FORM_MODE = 'edit';
const existingImagesFromServer = @json($existingImages);

document.addEventListener('DOMContentLoaded', () => {

    if (FORM_MODE !== 'edit') return;

    if (existingImagesFromServer.length) {
        existingImagesFromServer.forEach(img => {
            imagesState.push({
                uid: 'db-' + img.id,
                id: img.id,
                file: null,
                src: img.src,
                cropData: img.cropData ?? null,
                canvasData: null,
                cropBoxData: null,
                deleted: false,
                isExisting: true
            });
        });

        updateCropperButtonState();
    }
});

/* Validación de campos WhatsApp y llamadas */
document.querySelectorAll('input[name="whatsapp"], input[name="call_phone"]').forEach(input => {
    input.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9);
    });
});

// PRECIOS DE ETIQUETAS
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

function markImageForRemoval(imageId, btn) {

    const wrapper = btn.closest('.image-wrapper');

    // imágenes visibles actuales (no eliminadas)
    const remainingImages =
        document.querySelectorAll('.image-wrapper:not(.removed)').length;

    // si es la última, no permitir
    if (remainingImages <= 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Acción no permitida',
            text: 'El anuncio debe tener al menos una imagen.',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    // marcar visualmente como eliminada
    wrapper.classList.add('removed');
    wrapper.style.opacity = '0.4';
    btn.style.display = 'none';

    // actualizar hidden input
    const input = document.getElementById('remove_images');
    let removed = input.value ? input.value.split(',') : [];

    removed.push(imageId);
    input.value = removed.join(',');
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

    const amountValueContainer   = document.getElementById('amountValueContainer');
    const amountInput            = document.getElementById('amountInput');
    const amountVisibleCheckbox  = document.getElementById('amountVisibleCheckbox');
    const amountVisibleInput     = document.getElementById('amountVisibleInput');
    const amountTextSelect       = document.getElementById('amountTextSelect');
    const amountTextInput        = document.getElementById('amountTextInput');

    function toggleAmount(visible) {

        if (visible) {
            amountInput.classList.remove('d-none');
            amountInput.disabled = false;
            amountInput.required = true;

            amountTextSelect.classList.add('d-none');

            amountVisibleInput.value = 1;
            amountTextInput.value = '';
        } else {
            //amountInput.classList.add('d-none');
            amountInput.disabled = true;
            amountInput.required = false;
            amountInput.value = '';

            amountTextSelect.classList.remove('d-none');

            amountVisibleInput.value = 0;
            amountTextInput.value = amountTextSelect.value || 'No especificado';
        }
    }

    // ESTADO INICIAL (EDIT)
    toggleAmount(amountVisibleCheckbox.checked);

    // SWITCH
    amountVisibleCheckbox.addEventListener('change', function () {
        toggleAmount(this.checked);
        updatePreview?.();
    });

    // TEXTO CUANDO ESTÁ OCULTO
    amountTextSelect.addEventListener('change', function () {
        if (!amountVisibleCheckbox.checked) {
            amountTextInput.value = this.value || 'No especificado';
            updatePreview?.();
        }
    });

});

// LÓGICA DE ANUNCIO VERIFICADO SEGÚN CATEGORÍA Y SUBCATEGORÍA
const verifiedContainer = document.getElementById('verifiedContainer');
const verifiedInput     = document.getElementById('verification_requested');

function updateVerifiedVisibility(categoryId, subcategoryId) {

    if (!verifiedContainer || !verifiedInput) return;

    const allowedCategory = categoryId === '2' || categoryId === '3';
    const hasSubcategory  = subcategoryId && subcategoryId !== '';

    if (allowedCategory && hasSubcategory) {
        verifiedContainer.classList.remove('d-none');
    } else {
        verifiedContainer.classList.add('d-none');
        verifiedInput.checked = false;
    }
}

// Inicializar estado
document.addEventListener('DOMContentLoaded', () => {

    const categorySelect    = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');

    if (categorySelect && subcategorySelect) {
        updateVerifiedVisibility(
            categorySelect.value,
            subcategorySelect.value
        );
    }
});

// Actualizar visibilidad al cambiar categoría o subcategoría
document.addEventListener('DOMContentLoaded', () => {

    const requestInput = document.getElementById('verification_requested');
    const confirmBtn   = document.getElementById('confirmVerifiedBtn');

    if (!requestInput || !confirmBtn) return;

    function syncButtonWithCheckbox() {
        if (requestInput.checked) {
            confirmBtn.classList.remove('btn-outline-danger');
            confirmBtn.classList.add('btn-success');
            confirmBtn.innerHTML = `
                <i class="fa-solid fa-check"></i>
                Verificación solicitada
            `;
        } else {
            confirmBtn.classList.remove('btn-success');
            confirmBtn.classList.add('btn-outline-danger');
            confirmBtn.innerHTML = `
                <i class="fa-solid fa-shield-check"></i>
                Confirmar verificación del anuncio
            `;
        }
    }

    confirmBtn.addEventListener('click', () => {
        requestInput.checked = true;
        syncButtonWithCheckbox();
    });

    requestInput.addEventListener('change', syncButtonWithCheckbox);

    syncButtonWithCheckbox(); 
});

// LÓGICA DE CÁLCULO DE COSTOS SEGUN LAS ETIQUETAS
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

// LÓGICA DE COMPROBANTE DE PAGO
const receiptType     = document.getElementById("receipt_type");
const boletaFields    = document.getElementById("boletaFields");
const facturaFields   = document.getElementById("facturaFields");
const notaVentaFields = document.getElementById("notaVentaFields");
const receiptPreview  = document.getElementById("receiptPreview");

document.addEventListener("DOMContentLoaded", () => {

    function applyReceiptType(type) {

        boletaFields.classList.add("d-none");
        facturaFields.classList.add("d-none");
        notaVentaFields.classList.add("d-none");

        if (type === "boleta") boletaFields.classList.remove("d-none");
        if (type === "factura") facturaFields.classList.remove("d-none");
        if (type === "nota_venta") notaVentaFields.classList.remove("d-none");

        updateReceiptPreview();
    }

    receiptType.addEventListener("change", () => {
        applyReceiptType(receiptType.value);
    });

    applyReceiptType(receiptType.value);
});

document.addEventListener("input", (e) => {
    if (
        e.target.name === "dni" ||
        e.target.name === "boleta_full_name" ||
        e.target.name === "nota_full_name" ||
        e.target.name === "ruc" ||
        e.target.name === "company_name" ||
        e.target.name === "address"
    ) {
        updateReceiptPreview();
    }
});

// Script de no editar campos con status de publicado
document.addEventListener('DOMContentLoaded', () => {

    const STATUS = @json($ad->status);

    /* =========================
       SI ESTÁ PUBLICADO
       ========================= */
    if (STATUS === 'publicado') {

        const editableFields = [
            'district',
            'province',
            'department',
            'contact_location',
            'whatsapp',
            'call_phone'
        ];

        const form = document.getElementById('adForm');
        if (!form) return;

        form.querySelectorAll('input, select, textarea').forEach(el => {

            if (['_token', '_method', 'return_to'].includes(el.name)) return;

            if (editableFields.includes(el.name)) {
                el.disabled = false;
            } else {
                el.disabled = true;
                el.classList.add('disabled');
            }
        });

        // ASEGURAR QUE EL BOTÓN SIEMPRE ESTÉ ACTIVO
        document.getElementById('submitBtn')?.removeAttribute('disabled');
        return;
    }

    /* =========================
       SI ESTÁ PENDIENTE
       ========================= */
    if (STATUS === 'pendiente') {

        document.getElementById('categorySelect')?.setAttribute('disabled', true);
        document.getElementById('subcategorySelect')?.setAttribute('disabled', true);
        document.getElementById('days_active')?.setAttribute('disabled', true);

        [
            'urgent_publication',
            'featured_publication',
            'premiere_publication_switch',
            'semi_new_publication',
            'new_publication',
            'available_publication',
            'top_publication'
        ].forEach(id => {
            document.getElementById(id)?.setAttribute('disabled', true);
        });

    }

});

document.addEventListener('DOMContentLoaded', () => {

    const submitBtn = document.getElementById('submitBtn');
    const form      = document.getElementById('adForm');

    if (!submitBtn || !form) return;

    submitBtn.addEventListener('click', function () {

        if (this.disabled) return;

        // SINCRONIZAR FILES FINAL
        const dt = new DataTransfer();

        imagesState
            .filter(img => !img.deleted && img.file)
            .forEach(img => {
                dt.items.add(img.file);
            });

        fileInput.files = dt.files;

        // SERIALIZAR CROPS
        const cropPayload = imagesState
            .filter(img => !img.deleted)
            .map(img => ({
                id: img.id ?? null,
                uid: img.uid,
                cropData: img.cropData
            }));

        document.getElementById('crop_data').value =
            JSON.stringify(cropPayload);

        // DEBUG (puedes quitar luego)
        console.log("Archivos a enviar:", fileInput.files.length);

        // BLOQUEAR BOTÓN
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

        form.submit();
    });
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

/* CONTENEDOR CROPPER */
.cropper-wrapper {
    width: 100%;
    height: 430px;
    background: #2b2b2b;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cropper-wrapper img {
    max-width: 100%;
    max-height: 100%;
}

/* THUMBS */
.crop-thumb {
    width: 70px;
    height: 70px;
    object-fit: cover;
    cursor: pointer;
    border-radius: 6px;
    border: 2px solid #ddd;
}

.crop-thumb:hover {
    border-color: #0d6efd;
}

/* GRID EDIT */
.image-wrapper {
    position: relative;
}

.draft-crop-box {
    width: 120px;
    height: 120px;
    overflow: hidden;
    border-radius: 6px;
}

.draft-crop-box img {
    transform-origin: top left;
}

/* BOTÓN X */
.delete-img-btn {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #dc3545;
    color: #fff;
    border: none;
    font-size: 18px;
    cursor: pointer;
    z-index: 10;
}

/* MOBILE */
@media (max-width: 576px) {
    .modal-body {
        padding: 0.5rem;
    }

    .cropper-wrapper {
        height: 78vh;
    }

    .crop-thumb {
        width: 56px;
        height: 56px;
    }
}

.image-wrapper {
    position: relative;
}

.delete-img-btn {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #dc3545;
    color: #fff;
    border: none;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.delete-img-btn:hover {
    background: #bb2d3b;
}

</style>

@endsection
