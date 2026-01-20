@extends('layouts.app')

@section('title', 'Crear Anuncio')

@section('content')

<style>
    .field-card{
        border-radius: 14px;
        padding: 18px;
        background: #fff;
        border: 1px solid #eee;
        margin-bottom: 15px;
    }
</style>

<div class="container mt-4 mb-5">

    {{-- VOLVER --}}
    <a href="{{ route('my-ads.index') }}" class="text-dark">
        <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>


    <h3 class="fw-bold mb-3 text-center">Crear Nuevo Anuncio</h3>
    <p class="text-secondary text-center mb-4">
        Completa la información para la publicación de tu anuncio.
    </p>


    <div class="row mt-4">

        <!-- FORMULARIO IZQUIERDA -->
        <div class="col-lg-8 col-md-7">
            <form id="adForm" action="{{ isset($ad) ? route('my-ads.updateDraft', $ad->id) : route('my-ads.storeAdRequest') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- CATEGORÍA --}}
                <div class="field-card">
                    <label class="fw-semibold mb-2">Selecciona una Categoría</label>
                    <select id="categorySelect" name="category_id" class="form-select">
                        <option value="">-- Selecciona --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $ad->ad_categories_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SUBCATEGORÍA --}}
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="subcatContainer">
                    <label class="fw-semibold mb-2">Selecciona una Subcategoría</label>
                    <select id="subcategorySelect" name="subcategory_id" class="form-select">
                        <option value="">-- Selecciona --</option>

                        @foreach($subcategories as $sub)
                            <option value="{{ $sub->id }}"
                                {{ old('subcategory_id', $ad->ad_subcategories_id ?? '') == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Título --}}
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="titleContainer">
                    <label class="fw-semibold">Título del Anuncio</label>

                    <input type="text"
                        class="form-control"
                        name="title"
                        value="{{ old('title', $ad->title ?? '') }}"
                        id="titleInput"
                        placeholder="Se busca Perforista / Ayudante de Cocina / Pintor"
                        minlength="3"
                        maxlength="70"
                        required>

                    <small class="text-muted">
                        <span id="charCount">0</span>/70 caracteres
                    </small>
                </div>

                {{-- Descripción --}}
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="descriptionContainer">
                    <label class="fw-semibold">Descripción</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Describe tu anuncio">
                        {{ old('description', $ad->description ?? '') }}
                    </textarea>
                </div>

                {{-- LISTA DE CAMPOS DINÁMICOS --}}
                <div id="fieldsContainer"></div>

                {{-- UBICACIÓN DEL ANUNCIO --}}
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="locationAdContainer">

                    <label class="fw-semibold mt-2">Distrito</label>
                    <input
                        type="text"
                        name="district"
                        class="form-control uppercase-input"
                        placeholder="Ej: San Juan de Miraflores"
                        value="{{ old('district', $ad->district ?? '') }}"
                    >

                    <label class="fw-semibold mt-2">Provincia</label>
                    <input
                        type="text"
                        name="province"
                        class="form-control uppercase-input"
                        placeholder="Ej: Lima"
                        value="{{ old('province', $ad->province ?? '') }}"
                    >

                    <label class="fw-semibold">Departamento</label>
                    <input
                        type="text"
                        name="department"
                        class="form-control uppercase-input"
                        placeholder="Ej: Lima"
                        value="{{ old('department', $ad->department ?? '') }}"
                    >

                </div>

                {{-- DIRECCIO DEL ANUNCIO --}}
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="contactLocationContainer">
                    <label class="fw-semibold">Dirección</label>
                    <input
                        type="text"
                        name="contact_location"
                        class="form-control"
                        placeholder="Ej: Av. Mantaro 123"
                        value="{{ old('contact_location', $ad->contact_location ?? '') }}"
                    >
                </div>

                {{-- DATOS DE CONTACTO DEL USUARIO --}}
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="contactDataContainer">

                    <label class="fw-semibold">Contacto vía WhatsApp</label>
                    <input
                        type="text"
                        name="whatsapp"
                        class="form-control"
                        placeholder="Ej: +51 999 888 777"
                        value="{{ old('whatsapp', $ad->whatsapp ?? $user->whatsapp ?? '') }}"
                    >

                    <label class="fw-semibold mt-2">Contacto vía Llamada</label>
                    <input
                        type="text"
                        name="call_phone"
                        class="form-control"
                        placeholder="Ej: 01 555 4444"
                        value="{{ old('call_phone', $ad->call_phone ?? $user->call_phone ?? '') }}"
                    >

                </div>

                <!-- MONTO -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="amountContainer">
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
                                value="{{ old('amount', $ad->amount ?? '') }}"
                                {{ isset($ad) && !$ad->amount_visible ? 'disabled' : '' }}
                            >

                            <select id="amountTextSelect" class="form-select mt-2 {{ isset($ad) && !$ad->amount_visible ? '' : 'd-none' }}">
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

                            <small class="text-muted">
                                Si marcas "Ocultar monto", el público verá el texto seleccionado o "No especificado".
                            </small>
                        </div>

                        <div style="min-width:170px; display:flex; align-items:center; justify-content:center;">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="amountVisibleCheckbox"
                                    {{ old('amount_visible', $ad->amount_visible ?? 1) ? 'checked' : '' }}
                                >
                                <label class="form-check-label">Mostrar monto</label>
                            </div>
                        </div>
                    </div>

                    <!-- hidden fields -->
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

                <!-- DIAS PUBLICACION / COSTOS -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="costContainer">

                    <label class="fw-semibold">Días de publicación *</label>
                    <input
                        type="number"
                        min="2"
                        step="1"
                        name="days_active"
                        id="days_active"
                        class="form-control"
                        value="{{ old('days_active', $ad->days_active ?? 2) }}"
                        required
                    >

                    <small class="text-muted">
                        Indica cuántos días deseas que tu anuncio esté activo.
                    </small>

                    <br>

                    <label class="fw-semibold">Costo por día</label>
                    <input
                        type="text"
                        id="pricePerDay"
                        class="form-control mb-2"
                        value="{{ isset($ad) ? 'S/. ' . number_format($subcategories->first()->price ?? 0, 2) : '' }}"
                        readonly
                    >

                    <label class="fw-semibold mt-2">Costo total</label>
                    <input
                        type="text"
                        id="totalCost"
                        class="form-control mb-2"
                        value="{{ isset($ad) && isset($subcategories->first()->price)
                            ? 'S/. ' . number_format($ad->days_active * $subcategories->first()->price, 2)
                            : '' }}"
                        readonly
                    >

                    <label class="fw-semibold mt-2">Fecha de expiración</label>
                    <input
                        type="text"
                        id="expiresAt"
                        class="form-control"
                        value="{{ isset($ad) && $ad->expires_at
                            ? $ad->expires_at->format('d/m/Y')
                            : '' }}"
                        readonly
                    >
                </div>

                <!-- PUBLICACIÓN URGENTE -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="urgentContainer">

                    <label class="fw-semibold">¿Publicación urgente?</label>

                    <div class="form-check form-switch">
                        <input class="form-check-input"
                            type="checkbox"
                            id="urgent_publication"
                            name="urgent_publication"
                            value="1"
                            {{ isset($ad) && $ad->urgent_publication ? 'checked' : '' }}>
                        <label class="form-check-label" for="urgent_publication">
                            Activar publicación como urgente
                        </label>
                    </div>

                    <small class="text-danger fw-bold">
                        Precio por publicación urgente: S/. {{ number_format($urgentPrice, 2) }}
                    </small>
                </div>

                <!-- PUBLICACIÓN DESTACADA -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="featuredContainer">

                    <label class="fw-semibold">¿Publicación destacada?</label>

                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="featured_publication"
                            name="featured_publication"
                            value="1"
                            {{ isset($ad) && $ad->featured_publication ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="featured_publication">
                            Activar publicación como destacada
                        </label>
                    </div>

                    <small class="text-danger fw-bold">
                        Precio por publicación destacada: S/. {{ number_format($featuredPrice, 2) }}
                    </small>
                </div>

                <!-- PUBLICACIÓN ESTRENO -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="premiereContainer">
                    <label class="fw-semibold">¿Publicación en estreno?</label>

                    <div class="form-check form-switch">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="premiere_publication_switch"
                        >

                        <input 
                            type="hidden" 
                            name="premiere_publication" 
                            id="premiere_publication" 
                            value="{{ isset($ad) && $ad->premiere_publication ? 1 : 0 }}"
                        >

                        <label class="form-check-label" for="premiere_publication">
                            Activar publicación como estreno
                        </label>
                    </div>

                    <small class="text-danger fw-bold mt-1 d-block">
                        Precio de publicación estreno: S/. {{ number_format($premierePrice, 2) }}
                    </small>
                </div>

                <!-- PUBLICACIÓN SEMI-NUEVO -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="semiNewContainer">
                    <label class="fw-semibold">¿Publicación seminuevo?</label>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                            id="semi_new_publication"
                            name="semi_new_publication"
                            value="1" {{ isset($ad) && $ad->semi_new_publication ? 'checked' : '' }}>
                        <label class="form-check-label" for="semi_new_publication">
                            Activar publicación como seminuevo
                        </label>
                    </div>

                    <small class="text-danger fw-bold mt-1 d-block">
                        Precio publicación seminuevo: S/. {{ number_format($semiNewPrice, 2) }}
                    </small>
                </div>

                <!-- PUBLICACIÓN NUEVA -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="newContainer">
                    <label class="fw-semibold">¿Publicación nueva?</label>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                            id="new_publication"
                            name="new_publication"
                            value="1" {{ isset($ad) && $ad->new_publication ? 'checked' : '' }}>
                        <label class="form-check-label" for="new_publication">
                            Activar publicación como nuevo
                        </label>
                    </div>

                    <small class="text-danger fw-bold mt-1 d-block">
                        Precio publicación nuevo: S/. {{ number_format($newPrice, 2) }}
                    </small>
                </div>

                <!-- PUBLICACIÓN DISPONIBLE -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="availableContainer">
                    <label class="fw-semibold">¿Publicación disponible?</label>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                            id="available_publication"
                            name="available_publication"
                            value="1"
                            {{ isset($ad) && $ad->available_publication ? 'checked' : '' }}>
                        <label class="form-check-label" for="available_publication">
                            Activar publicación como disponible
                        </label>
                    </div>

                    <small class="text-danger fw-bold mt-1 d-block">
                        Precio publicación disponible: S/. {{ number_format($availablePrice, 2) }}
                    </small>
                </div>

                <!-- PUBLICACIÓN TOP -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="topContainer">
                    <label class="fw-semibold">¿Publicación TOP?</label>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                            id="top_publication"
                            name="top_publication"
                            value="1" {{ isset($ad) && $ad->top_publication ? 'checked' : '' }}>
                        <label class="form-check-label" for="top_publication">
                             Activar publicación como TOP
                        </label>
                    </div>

                    <small class="text-danger fw-bold mt-1 d-block">
                        Precio publicación TOP: S/. {{ number_format($topPrice, 2) }}
                    </small>
                </div>

                <!-- ANUNCIO VERIFICADO -->
                @php
                    $showVerified = isset($ad) && $ad->ad_categories_id != 1;
                @endphp

                <div class="field-card {{ $showVerified ? '' : 'd-none' }}" id="verifiedContainer">

                    <label class="fw-semibold">¿Deseas verificar tu anuncio?</label>

                    {{-- SWITCH --}}
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

                    {{-- TEXTO DE AUTORIZACIÓN --}}
                    <p class="text-muted small mb-2">
                        Al activar esta opción, autorizas que este anuncio sea revisado y pueda
                        mostrarse como <strong>ANUNCIO VERIFICADO</strong>.
                    </p>

                    {{-- BOTÓN UX (NO backend) --}}
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

                {{-- RESUMEN DE COSTO Y SALDO --}}
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="summaryContainer">
                    <h5 class="fw-bold mb-3">Resumen de Pago</h5>

                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">Costo total:</span>

                        <span class="fw-bold text-danger" id="summaryTotalCost">
                            S/. {{ isset($summaryTotalCost) ? number_format($summaryTotalCost, 2) : '0.00' }}
                        </span>
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

                <!-- IMÁGENES DE GALERIA -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="imagesContainer">

                    <label class="fw-semibold mb-2">Imágenes del anuncio</label>

                    {{-- IMÁGENES PROPIAS EXISTENTES --}}
                    @if(isset($images) && $images->count())
                        <div class="d-flex flex-wrap gap-2 mb-3">

                            @foreach($images as $image)
                                <div class="position-relative">
                                    <img
                                        src="{{ asset($image->image) }}"
                                        class="rounded border"
                                        style="width:120px;height:120px;object-fit:cover;"
                                        alt="Imagen anuncio"
                                    >

                                    @if($image->is_main)
                                        <span class="badge bg-primary position-absolute top-0 start-0">
                                            Principal
                                        </span>
                                    @endif
                                </div>
                            @endforeach

                        </div>

                        <small class="text-muted d-block mb-3">
                            Estas son las imágenes que ya subiste para este anuncio.
                        </small>
                    @else
                        <small class="text-muted d-block mb-3">
                            Aún no has subido imágenes propias.
                        </small>
                    @endif

                    <hr>

                    {{-- SUBIR NUEVAS IMÁGENES --}}
                    <label class="fw-semibold mt-3">Agregar o reemplazar imágenes</label>

                    <input type="file"
                        name="images[]"
                        class="form-control"
                        id="ownImagesInput"
                        accept="image/*"
                        multiple>

                    <small class="text-muted d-block">
                        Máximo 5 imágenes. Si subes nuevas, se agregarán al anuncio.
                    </small>

                </div>

                <!-- COMPROBANTE -->
                <div class="field-card {{ isset($ad) ? '' : 'd-none' }}" id="receiptContainer">

                    <h5 class="fw-bold mb-3">Datos para Comprobante de Pago</h5>

                    <!-- Tipo de comprobante -->
                    <label class="fw-semibold mb-2">Tipo de comprobante</label>
                    <select class="form-select" name="receipt_type" id="receipt_type">
                        <option value="">-- Sin comprobante --</option>
                        <option value="boleta" {{ ($ad->receipt_type ?? '') === 'boleta' ? 'selected' : '' }}>
                            Boleta
                        </option>
                        <option value="factura" {{ ($ad->receipt_type ?? '') === 'factura' ? 'selected' : '' }}>
                            Factura
                        </option>
                        <option value="nota_venta" {{ ($ad->receipt_type ?? '') === 'nota_venta' ? 'selected' : '' }}>
                            Nota de Venta
                        </option>
                    </select>

                    <!-- BOLETA -->
                    <div id="boletaFields" class="mt-3 d-none">
                        <label class="fw-semibold">DNI</label>
                        <input type="text"
                            name="dni"
                            class="form-control"
                            maxlength="8"
                            value="{{ old('dni', $ad->dni ?? '') }}">

                        <label class="fw-semibold mt-2">Nombre Completo</label>
                        <input type="text"
                            name="boleta_full_name"
                            id="boleta_full_name"
                            class="form-control"
                            value="{{ old('boleta_full_name', $ad->full_name ?? '') }}">
                    </div>

                    <!-- FACTURA -->
                    <div id="facturaFields" class="mt-3 d-none">
                        <label class="fw-semibold">RUC</label>
                        <input type="text"
                            name="ruc"
                            class="form-control"
                            maxlength="11"
                            value="{{ old('ruc', $ad->ruc ?? '') }}">

                        <label class="fw-semibold mt-2">Razón Social</label>
                        <input type="text"
                            name="company_name"
                            class="form-control"
                            value="{{ old('company_name', $ad->company_name ?? '') }}">

                        <label class="fw-semibold mt-2">Dirección</label>
                        <input type="text"
                            name="address"
                            class="form-control"
                            value="{{ old('address', $ad->address ?? '') }}">
                    </div>

                    <!-- NOTA DE VENTA -->
                    <div id="notaVentaFields" class="mt-3 d-none">
                        <label class="fw-semibold mt-2">Nombre Completo</label>
                        <input type="text"
                            name="nota_full_name"
                            id="nota_full_name"
                            class="form-control"
                            value="{{ old('nota_full_name', $ad->full_name ?? '') }}">
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-2">Previsualización del Comprobante</h5>
                    <div class="p-3 border rounded bg-light" id="receiptPreview">
                        <small class="text-muted">Completa los datos para ver la previsualización.</small>
                    </div>

                    <input type="hidden" name="save_as_draft" id="save_as_draft" value="0">

                    @php
                        $hasEnoughBalance = $virtualWallet >= $summaryTotalCost;
                    @endphp

                    <button
                        type="submit"
                        class="btn btn-danger w-100"
                        id="submitAdBtn"
                        {{ isset($ad) && !$hasEnoughBalance ? 'disabled' : '' }}
                    >
                        Enviar Solicitud de Anuncio
                    </button>

                    @if(isset($ad) && !$hasEnoughBalance)
                        <small class="text-danger d-block mt-2 text-center">
                            Saldo insuficiente para publicar este anuncio
                        </small>
                    @endif

                </div>
            </form>
        </div>

        <!-- PREVIEW DERECHA -->
        <div class="col-lg-4 col-md-5 mb-4">
            <h4 class="fw-bold mb-3 text-center">Previsualización</h4>

            <div id="previewCard" class="p-2"
                style="position: sticky; top: 80px; z-index: 10;">
            </div>
        </div>

    </div>


<!--Modal Elegir de la Galeria de Imagenes-->
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
window.SERVICIOS_CATEGORY_ID = 4;
window.PRIVADOS_SUBCATEGORY_ID = 21;

window.ALERTS = @json($alertsPrepared);
</script>

<script>
    // Campos permitidos para la subcategoría (solo tags activos)
    const fieldsFromServer = @json($fields ?? []);

    // Datos del anuncio (para marcar checkboxes en borrador)
    const adDataFromServer = @json($ad ?? null);
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const categorySelect = document.getElementById('categorySelect');
    const subcatSelect = document.getElementById('subcategorySelect');
    const subcatContainer = document.getElementById('subcatContainer');
    const fieldsContainer = document.getElementById('fieldsContainer');

    const imagesContainer = document.getElementById('imagesContainer');

    const tagMap = {
        is_urgent:     { container: 'urgentContainer',     input: 'urgent_publication' },
        is_featured:   { container: 'featuredContainer',   input: 'featured_publication' },
        is_premiere:   { container: 'premiereContainer',   input: 'premiere_publication' },
        is_semi_new:   { container: 'semiNewContainer',    input: 'semi_new_publication' },
        is_new:        { container: 'newContainer',        input: 'new_publication' },
        is_available:  { container: 'availableContainer',  input: 'available_publication' },
        is_top:        { container: 'topContainer',        input: 'top_publication' }
    };

    function resetTags() {
        Object.values(tagMap).forEach(tag => {
            const c = document.getElementById(tag.container);
            const i = document.getElementById(tag.input);
            if (c) c.classList.add('d-none');
            if (i) i.checked = false;
        });
    }

    function showTagsForSubcategory(selectedSub) {
        resetTags();
        Object.entries(tagMap).forEach(([flag, tag]) => {
            if (selectedSub[flag]) {
                const container = document.getElementById(tag.container);
                if (container) container.classList.remove('d-none');

                if (adDataFromServer && adDataFromServer[tag.input]) {
                    const input = document.getElementById(tag.input);
                    if (input) input.checked = true;
                }
            }
        });
    }

    function generateDynamicFields(selectedSubId) {
        fieldsContainer.innerHTML = ''; // limpiar antes

        const allowedFields = fieldsFromServer.filter(f => f.ad_subcategories_id == selectedSubId);

        allowedFields.forEach(f => {
            const value = adDataFromServer?.values?.[f.id]?.value || ''; // <-- importante
            const div = document.createElement('div');
            div.className = 'field-card';
            div.innerHTML = `<label class="fw-semibold">${f.name}</label>`;

            let fieldHtml = '';
            switch(f.type) {
                case 'number':
                    fieldHtml = `<input type="number" class="form-control" name="dynamic[${f.id}]" value="${value}">`;
                    break;
                case 'textarea':
                    fieldHtml = `<textarea class="form-control" name="dynamic[${f.id}]" rows="3">${value}</textarea>`;
                    break;
                default:
                    fieldHtml = `<input type="text" class="form-control" name="dynamic[${f.id}]" value="${value}">`;
            }

            div.innerHTML += fieldHtml;
            fieldsContainer.appendChild(div);
        });
    }

    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        subcatSelect.innerHTML = '';
        subcatContainer.classList.add('d-none');
        resetTags();

        if (!categoryId) return;

        fetch(`/advertising/my-ads/subcategories-with-category/${categoryId}`)
            .then(res => res.json())
            .then(data => {
                let html = `<option value="">-- Selecciona --</option>`;
                data.subcategories.forEach(sub => {
                    html += `<option value="${sub.id}">${sub.name}</option>`;
                });
                subcatSelect.innerHTML = html;
                subcatContainer.classList.remove('d-none');

                subcatSelect.onchange = function() {
                    const subId = this.value;
                    if (!subId) return;
                    const selectedSub = data.subcategories.find(s => s.id == subId);
                    if (!selectedSub) return;

                    showTagsForSubcategory(selectedSub);
                    generateDynamicFields(subId);
                    imagesContainer?.classList.remove('d-none');
                };

                // Si hay borrador
                @if(isset($ad))
                    subcatSelect.value = "{{ $ad->ad_subcategories_id }}";
                    subcatSelect.dispatchEvent(new Event('change'));
                @endif
            });
    });

    // Para nuevos anuncios, si ya hay categoría seleccionada (ej. old input)
    if (categorySelect.value) categorySelect.dispatchEvent(new Event('change'));
});
    
document.addEventListener('DOMContentLoaded', () => {

    const subSelect = document.getElementById('subcategorySelect');

    if (subSelect) {
        subSelect.addEventListener('change', function () {
            if (!this.value) return;

            document.getElementById('titleContainer')?.classList.remove('d-none');
            document.getElementById('descriptionContainer')?.classList.remove('d-none');

            loadDynamicFields(this.value);
        });
    }

});

document.addEventListener('DOMContentLoaded', () => {

    const receiptType = document.getElementById('receipt_type');
    const boleta      = document.getElementById('boletaFields');
    const factura     = document.getElementById('facturaFields');
    const notaVenta   = document.getElementById('notaVentaFields');
    const preview     = document.getElementById('receiptPreview');

    function hideAll() {
        boleta?.classList.add('d-none');
        factura?.classList.add('d-none');
        notaVenta?.classList.add('d-none');
    }

    function updateReceipt(type) {

        hideAll();

        if (!type) {
            preview.innerHTML = `<small class="text-muted">Sin comprobante.</small>`;
            return;
        }

        if (type === 'boleta') {
            boleta.classList.remove('d-none');
            preview.innerHTML = `<strong>Boleta</strong>`;
        }

        if (type === 'factura') {
            factura.classList.remove('d-none');
            preview.innerHTML = `<strong>Factura</strong>`;
        }

        if (type === 'nota_venta') {
            notaVenta.classList.remove('d-none');
            preview.innerHTML = `<strong>Nota de Venta</strong>`;
        }
    }

    // Cambio manual
    receiptType?.addEventListener('change', function () {
        updateReceipt(this.value);
    });

    // CARGA AUTOMÁTICA EN BORRADOR
    if (receiptType?.value) {
        updateReceipt(receiptType.value);
    }
});

// script para enviar el formulario
document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('adForm');

    form.addEventListener('submit', function (e) {

        // obtener el total REAL desde el resumen
        const totalText = document
            .getElementById('summaryTotalCost')
            ?.textContent
            ?.replace('S/.', '')
            ?.trim();

        const finalPrice = parseFloat(totalText || 0);

        // SI NO PASA LA VALIDACIÓN → BLOQUEAMOS SUBMIT
        if (!checkBalanceBeforeSubmit(finalPrice)) {
            e.preventDefault();
            return false;
        }
    });
});

function checkBalanceBeforeSubmit(finalPrice) {

    const userBalance = {{ auth()->user()->virtual_wallet }};

    if (userBalance < finalPrice) {

        Swal.fire({
            icon: 'warning',
            title: 'Saldo insuficiente',
            html: `
                <p>Tu saldo actual es <strong>S/. ${userBalance.toFixed(2)}</strong></p>
                <p>El costo del anuncio es <strong>S/. ${finalPrice.toFixed(2)}</strong></p>
                <p class="text-danger fw-bold">Necesitas recargar saldo</p>
            `,
            showCancelButton: true,
            confirmButtonText: '💳 Ir a Recargar',
            cancelButtonText: '🗑️ Borrar anuncio',
            reverseButtons: true,
        }).then(result => {

            // IR A RECARGAR → GUARDAR COMO DRAFT
            if (result.isConfirmed) {
                document.getElementById('save_as_draft').value = 1;
                document.getElementById('adForm').submit();
            }

            // (el form nunca se envía)
        });

        return false;
    }

    return true;
}
    
/*Logica de cuando el usuario ingresa a la vista de create con saldo 0*/
document.addEventListener('DOMContentLoaded', function () {

    const virtualWallet = {{ (int) $virtualWallet }};

    if (virtualWallet <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Saldo insuficiente',
            html: `
                <p class="mb-1">No tienes saldo disponible para crear un anuncio.</p>
                <strong>Necesitas realizar una recarga.</strong>
            `,
            showCancelButton: true,
            confirmButtonText: '💳 Ir a Recargar',
            cancelButtonText: '👀 Solo ver',
            allowOutsideClick: false,
            allowEscapeKey: false,
            reverseButtons: true,
        }).then((result) => {

            if (result.isConfirmed) {
                window.location.href = "{{ route('recharges.index') }}";
            }

            if (result.dismiss === Swal.DismissReason.cancel) {
                disableAdForm();
            }
        });
    }

    function disableAdForm() {

        // Mensaje visual
        const info = document.createElement('div');
        info.className = 'alert alert-info mt-3';
        info.innerHTML = `
            <i class="fa-solid fa-circle-info me-1"></i>
            Estás en modo solo visual. Recarga saldo para publicar anuncios.
        `;

        form.prepend(info);
    }
});

/*Que muestre el alert al querer crear un servicio privado*/
document.addEventListener('DOMContentLoaded', () => {

    const categorySelect    = document.getElementById('categorySelect');
    const subcategorySelect = document.getElementById('subcategorySelect');

    if (!categorySelect || !subcategorySelect) return;

    subcategorySelect.addEventListener('change', () => {

        const categoryId    = parseInt(categorySelect.value);
        const subcategoryId = parseInt(subcategorySelect.value);

        if (
            categoryId === window.SERVICIOS_CATEGORY_ID &&
            subcategoryId === window.PRIVADOS_SUBCATEGORY_ID
        ) {
            showAdultServicesAlert();
        }

    });

});

//Title personalizado para cada categoria
document.addEventListener("DOMContentLoaded", () => {

    const titleInput = document.getElementById("titleInput");
    const categorySelect = document.getElementById("categorySelect");

    const titlePlaceholders = {
        "EMPLEOS": "Ej: Se busca Ayudante de Cocina / Perforista / Chofer A1",
        "INMUEBLES": "Ej: Departamento en alquiler 2 habitaciones en Miraflores",
        "VEHICULOS, MOTOS, MAQUINARIAS, EQUIPOS Y OTROS": "Ej: Toyota Corolla 2018 automático",
        "SERVICIOS": "Ej: Servicio de gasfitería y electricidad a domicilio"
    };

    categorySelect.addEventListener("change", function () {

        const selectedOption = this.options[this.selectedIndex];
        const categoryName = selectedOption.text.trim().toUpperCase();

        if (titlePlaceholders[categoryName]) {
            titleInput.placeholder = titlePlaceholders[categoryName];
        } else {
            titleInput.placeholder = "Escribe un título para tu anuncio";
        }

        titleInput.value = "";
    });

});

function buildTermsHtml(terms) {
    return terms.map(term => `
        <div style="border:1px solid #eee;border-radius:12px;padding:16px;margin-bottom:16px;">
            ${term.icon ? `
                <div style="text-align:center;margin-bottom:10px;">
                    <img src="/${term.icon}" style="max-height:90px">
                </div>
            ` : ''}
            <h5 style="font-weight:600;text-align:center;">${term.title}</h5>
            <p style="font-size:14px;color:#555;">${term.description}</p>
        </div>
    `).join('');
}

function getMainHtml(alertData) {
    return `
        ${alertData.logo ? `
            <div class="text-center mb-2">
                <img src="/${alertData.logo}" style="max-width:120px" class="rounded">
            </div>
        ` : ''}

        <p style="font-size:14px; line-height:1.6;">
            ${alertData.description ?? ''}
        </p>

        <button id="openTermsBtn"
            type="button"
            class="btn btn-link p-0 fw-semibold"
            style="color:#0d6efd;text-decoration:underline;">
            Términos y Condiciones
        </button>
    `;
}


let openingTerms = false;

// Alerta de categoria servicios y privados
function showAdultServicesAlert() {

    if (!window.ALERTS || window.ALERTS.length === 0) return;

    const alertData = window.ALERTS[0];

    Swal.fire({
        title: alertData.title ?? 'Contenido sensible',
        icon: 'warning',
        html: getMainHtml(alertData),
        showCancelButton: true,
        confirmButtonText: 'Aceptar y continuar',
        cancelButtonText: 'Rechazar',
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#dc3545',
        reverseButtons: true,
        allowOutsideClick: false,
        didOpen: () => {

            document
                .getElementById('openTermsBtn')
                .addEventListener('click', () => {

                    // ACTUALIZAMOS EL MISMO MODAL
                    Swal.update({
                        title: 'Términos y Condiciones',
                        icon: undefined,
                        showCancelButton: false,
                        confirmButtonText: 'Volver',
                        html: `
                            <div style="max-height:420px;overflow-y:auto;text-align:left;">
                                ${buildTermsHtml(alertData.terms)}
                            </div>
                        `
                    });

                    // BOTÓN VOLVER
                    Swal.getConfirmButton().onclick = () => {
                        Swal.update({
                            title: alertData.title,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Aceptar y continuar',
                            cancelButtonText: 'Rechazar',
                            html: getMainHtml(alertData)
                        });

                        // volver a bindear
                        setTimeout(() => showAdultServicesAlert(), 0);
                    };
                });
        }

    }).then(result => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            location.reload();
        }
    });
}

let MAX_IMAGES = 5;
let isEmpleosCategory = false;
let previewCarouselIndex = 0;
let previewCarouselTimer = null;
const PREVIEW_INTERVAL = 2500; 

// CONTADOR DE CARACTERES EN TÍTULO
const titleInput = document.getElementById('titleInput');
const charCount = document.getElementById('charCount');

if (charCount) {
    charCount.textContent = 0;
}

titleInput.addEventListener('input', () => {
    charCount.textContent = titleInput.value.length;
});

function safeListener(id, event, callback) {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener(event, callback);
    }
}

// OBJETO BASE DEL PREVIEW
let adPreview = {
    images: [],
    title: "Título del anuncio",
    description: "Escribe una descripción...",
    subcategory: { name: "Subcategoría" },
    district: "",
    province: "",
    amount: "",
    amount_visible: 1,   
    whatsapp: "",
    call_phone: "",
    urgent_publication: 0,
    featured_publication: 0
};

// OBTENER CAMPOS DINÁMICOS PARA PREVIEW
function getPreviewDynamicFields(limit = 4) {

    const fields = [];
    const fieldCards = document.querySelectorAll("#fieldsContainer .field-card");

    fieldCards.forEach((card, index) => {

        if (index >= limit) return;

        const label = card.querySelector("label")?.innerText;
        const input = card.querySelector("input, textarea, select");

        if (!label || !input) return;

        const value = input.value?.trim();

        if (!value) return;

        fields.push({
            label,
            value
        });
    });

    return fields;
}

const districtInput  = document.querySelector('input[name="district"]');
const provinceInput  = document.querySelector('input[name="province"]');

[districtInput, provinceInput].forEach(input => {
    input?.addEventListener('input', () => {
        updatePreview(); 
    });
});

function truncateText(text, limit = 70) {
    if (!text) return '';

    const normalized = text.toLowerCase().trim();

    if (normalized.length <= limit) {
        return normalized;
    }

    const truncated = normalized.slice(0, limit);
    const lastSpace = truncated.lastIndexOf(' ');

    return (lastSpace > 0 ? truncated.slice(0, lastSpace) : truncated) + '…';
}

// CREA LA CARD DE PREVISUALIZACIÓN
function createAdCard(ad) {

    const img = ad.images.length > 0
        ? ad.images[previewCarouselIndex]?.image || ad.images[0].image
        : "/assets/img/not-found-image/failed-image.jpg";

    return `
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">

            <div class="position-relative">
                <!-- Imagen -->
                <img src="${img}" class="w-150 card-img-top">

                ${ad.top_publication
                    ? `<div class="badge-top">TOP</div>`
                    : ad.urgent_publication
                        ? `<div class="badge-urgente">URGENTE</div>`
                        : ''
                }

                ${ad.premiere_publication
                    ? `<div class="badge-estreno">ESTRENO</div>`
                    : ad.available_publication
                        ? `<div class="badge-available">DISPONIBLE</div>`
                        : ''
                }

                ${
                    ad.new_publication
                        ? `<div class="badge-new">NUEVO</div>`
                        : ad.semi_new_publication
                            ? `<div class="badge-seminew">SEMI-NUEVO</div>`
                            : ''
                }

            </div>

            <div class="card-body"> 
                
                <h3 class="ad-title">
                    ${ad.featured_publication == 1 ? `<span class="star-destacado">⭐</span>` : ''}
                    ${ad.title}

                    <!-- Compartir -->
                    <button class="btn btn-sm btn-secondary ms-auto">
                        <i class="fa-solid fa-share-nodes"></i>
                    </button>
                </h3>

                <p class="ad-desc">${ad.description}</p>

                ${ad.dynamic_fields?.length ? `
                    <ul class="ad-dynamic-fields mt-2">
                        ${ad.dynamic_fields.map(f => {
                            const truncated = truncateText(f.value, 70);
                            return `
                                <li>
                                    <strong>${f.label}:</strong>
                                    <span class="dynamic-value">${truncated}</span>
                                </li>
                            `;
                        }).join("")}
                    </ul>
                ` : ''}

                <div class="ad-tags">
                    <span class="ad-badge"><i class="fa-solid fa-tag"></i> ${ad.subcategory.name}</span>
                    <span class="ad-location"><i class="fa-solid fa-location-dot"></i> ${ad.district && ad.province ? `${ad.district} - ${ad.province}` : 'Sin ubicación'}</span>
                </div>

                <div class="ad-price-box">
                    <p class="fw-bold ${ad.amount_visible == 0 ? 'text-secondary' : 'text-success'}">
                        ${
                            ad.amount_visible == 1
                                ? `S/ ${ad.amount}`
                                : ad.amount_text
                                    ? `S/ ${ad.amount_text}`
                                    : "S/ No especificado"
                        }
                    </p>
                </div>

                <!-- CONTACTO -->
                <div class="d-flex gap-2 mt-3">

                    <!-- Ver -->
                    <button class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-eye"></i> Ver
                    </button>

                    <a href="#" 
                        class="btn btn-sm btn-success">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </a>
                    
                    <a href="#" 
                        class="btn btn-sm btn-info">
                        <i class="fa-solid fa-phone"></i> Llamar
                    </a>

                    <a href="#" class="btn btn-sm btn-danger">
                        <i class="fa-solid fa-comments"></i> Chat
                    </a>

                </div>

            </div>
        </div>
    `;
}


//PREVIEW DEL ANUNCIO
function updatePreview() {

    const dynamicPreviewFields = getPreviewDynamicFields();


    const ad = {
        title: document.querySelector("input[name='title']")?.value || "Título del anuncio",
        description: document.querySelector("textarea[name='description']")?.value || "Descripción del anuncio...",
        dynamic_fields: dynamicPreviewFields,
        //contact_location: document.querySelector("input[name='contact_location']")?.value || "Ubicación",
        district: document.querySelector("input[name='district']")?.value.toUpperCase() || "",
        province: document.querySelector("input[name='province']")?.value.toUpperCase() || "",

        amount: amountInput.value || null,
        amount_text: amountTextInput.value || null,
        amount_visible: parseInt(amountVisibleInput.value),


        featured_publication: document.querySelector("#featured_publication")?.checked ? 1 : 0,
        urgent_publication: document.querySelector("#urgent_publication")?.checked ? 1 : 0,
        premiere_publication: document.querySelector("#premiere_publication_switch")?.checked ? 1 : 0,
        semi_new_publication: document.querySelector("#semi_new_publication")?.checked ? 1 : 0,
        new_publication: document.querySelector("#new_publication")?.checked ? 1 : 0,
        available_publication: document.querySelector("#available_publication")?.checked ? 1 : 0,
        top_publication: document.querySelector("#top_publication")?.checked ? 1 : 0,

        subcategory: {
            name: document.querySelector("#subcategorySelect option:checked")?.textContent || "Subcategoría"
        },

        images: [
            ...referenceImages.map(img => ({ image: img })),
            ...previewImages.map(img => ({ image: img }))
        ],

        whatsapp: "{{ auth()->user()->whatsapp ?? '' }}",
        call_phone: "{{ auth()->user()->phone ?? '' }}",

        full_url: "#",
        time_ago: "Ahora"
    };

    document.querySelector("#previewCard").innerHTML = createAdCard(ad);
    //startPreviewCarousel(ad.images);
}

document.getElementById("premiere_publication_switch")
    .addEventListener("change", function () {
        document.getElementById("premiere_publication").value = this.checked ? 1 : 0;
    });

document.querySelectorAll("#adForm input, #adForm textarea, #adForm select")
    .forEach(el => {
        el.addEventListener("input", updatePreview);
        el.addEventListener("change", updatePreview);
    });

function startPreviewCarousel(images) {

    // Limpiar carrusel previo
    if (previewCarouselTimer) {
        clearInterval(previewCarouselTimer);
        previewCarouselTimer = null;
    }

    // No rotar si hay 0 o 1 imagen
    if (!images || images.length <= 1) return;

    previewCarouselIndex = 0;

    previewCarouselTimer = setInterval(() => {
        previewCarouselIndex++;

        if (previewCarouselIndex >= images.length) {
            previewCarouselIndex = 0;
        }

        updatePreview();
    }, PREVIEW_INTERVAL);
}


let previewImages = [];
let referenceImages = [];

document.querySelector("input[name='images[]']").addEventListener("change", function(e){
    previewImages = [];

    [...this.files].forEach(file => {
        const reader = new FileReader();
        reader.onload = () => {
            previewImages.push(reader.result);
            updatePreview();
        };
        reader.readAsDataURL(file);
    });
});

updatePreview();

// Escuchar cambios en campos dinámicos
document.getElementById("fieldsContainer")
    .addEventListener("input", function (e) {

        if (
            e.target.matches("input") ||
            e.target.matches("textarea") ||
            e.target.matches("select")
        ) {
            updatePreview();
        }
    });

document.addEventListener('DOMContentLoaded', () => {

    const requestInput = document.getElementById('verification_requested');
    const confirmBtn = document.getElementById('confirmVerifiedBtn');

    if (confirmBtn && requestInput) {
        confirmBtn.addEventListener('click', () => {
            requestInput.checked = true;

            confirmBtn.classList.remove('btn-outline-danger');
            confirmBtn.classList.add('btn-success');
            confirmBtn.innerHTML = `
                <i class="fa-solid fa-check"></i>
                Verificación solicitada
            `;
        });
    }
});


// Seleccionar Categoria y Sub
document.addEventListener("DOMContentLoaded", () => {

    const categorySelect   = document.getElementById('categorySelect');
    const subcatSelect     = document.getElementById('subcategorySelect');
    const subcatContainer  = document.getElementById('subcatContainer');

    const imagesContainer  = document.getElementById('imagesContainer');
    const imagesGrid       = document.getElementById('modalImagesGrid');
    const selectedInput    = document.getElementById('selectedImage');

    const openImagesBtn    = document.getElementById('openImagesModal');
    const previewBox       = document.getElementById('selectedPreview');
    const previewImg       = document.getElementById('selectedPreviewImg');
    const confirmBtn       = document.getElementById('confirmImage');
    const previewList = document.getElementById('selectedPreviewList');

    let currentSubcategory = null;
    let tempSelectedImages = [];
    //const MAX_IMAGES = 5;

    const modal = new bootstrap.Modal(
        document.getElementById('modalSubcategoryImages')
    );

    const containers = {
        urgent: document.getElementById('urgentContainer'),
        featured: document.getElementById('featuredContainer'),
        premiere: document.getElementById('premiereContainer'),
        semi_new: document.getElementById('semiNewContainer'),
        new: document.getElementById('newContainer'),
        available: document.getElementById('availableContainer'),
        top: document.getElementById('topContainer'),
    };


    const tagMap = {
        is_urgent:     { container: 'urgentContainer',     input: 'urgent_publication' },
        is_featured:   { container: 'featuredContainer',   input: 'featured_publication' },
        is_premiere:   { container: 'premiereContainer',   input: 'premiere_publication' },
        is_semi_new:   { container: 'semiNewContainer',    input: 'semi_new_publication' },
        is_new:        { container: 'newContainer',        input: 'new_publication' },
        is_available:  { container: 'availableContainer',  input: 'available_publication' },
        is_top:        { container: 'topContainer',        input: 'top_publication' }
    };

    document.addEventListener('change', function (e) {

        if (e.target.id !== 'semi_new_publication' && e.target.id !== 'new_publication') {
            return;
        }

        const semiNewInput = document.getElementById('semi_new_publication');
        const newInput     = document.getElementById('new_publication');

        // Si marco NUEVO → desmarco SEMI-NUEVO
        if (e.target.id === 'new_publication' && newInput.checked) {
            semiNewInput.checked = false;
        }

        // Si marco SEMI-NUEVO → desmarco NUEVO
        if (e.target.id === 'semi_new_publication' && semiNewInput.checked) {
            newInput.checked = false;
        }

        updatePreview();
    });


    // RESET IMÁGENES
    function resetImages() {

        if (imagesGrid) imagesGrid.innerHTML = '';
        if (selectedInput) selectedInput.value = '';

        previewBox?.classList.add('d-none');
        imagesContainer?.classList.add('d-none');

        tempSelectedImages = [];

        if (previewList) previewList.innerHTML = '';

        referenceImages = [];

        updatePreview();
    }


    // RESET TAGS
    function resetTags() {
        Object.values(tagMap).forEach(tag => {
            const c = document.getElementById(tag.container);
            const i = document.getElementById(tag.input);
            if (c) c.classList.add('d-none');
            if (i) i.checked = false;
        });
    }

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

    // CATEGORÍA
    categorySelect.addEventListener('change', function () {

        updateVerifiedVisibility(this.value, null);

        const categoryId = this.value;
        const selectedText = this.options[this.selectedIndex]?.textContent;

        isEmpleosCategory = selectedText === 'Empleos';
        MAX_IMAGES = isEmpleosCategory ? 1 : 5;

        // Ajustar input de imágenes propias
        const ownImagesInput = document.getElementById('ownImagesInput');
        const ownImagesHelp  = document.getElementById('ownImagesHelp');

        if (ownImagesInput && ownImagesHelp) {
            if (isEmpleosCategory) {
                ownImagesInput.removeAttribute('multiple');
                ownImagesHelp.textContent = 'Solo 1 imagen permitida';
            } else {
                ownImagesInput.setAttribute('multiple', 'multiple');
                ownImagesHelp.textContent = 'Máx. 5 imágenes';
            }
        }

        subcatSelect.innerHTML = '';
        subcatContainer.classList.add('d-none');

        resetTags();
        resetImages();

        if (!categoryId) return;

        fetch(`/advertising/my-ads/subcategories-with-category/${categoryId}`)
            .then(res => res.json())
            .then(data => {

                let html = `<option value="">-- Selecciona --</option>`;
                data.subcategories.forEach(sub => {
                    html += `<option value="${sub.id}">${sub.name}</option>`;
                });

                subcatSelect.innerHTML = html;
                subcatContainer.classList.remove('d-none');

                // SUBCATEGORÍA
                subcatSelect.onchange = function () {

                    resetTags();
                    resetImages();

                    const categoryId = categorySelect.value;
                    const subId = this.value;

                    if (!subId) return;

                    updateVerifiedVisibility(categoryId, subId);
                    currentSubcategory = subId;

                    // buscar subcategoría seleccionada
                    const selectedSub = data.subcategories.find(s => s.id == subId);

                    if (!selectedSub) return;

                    // PRECIO BASE
                    subcatPrice = parseFloat(selectedSub.price) || 0;

                    // MOSTRAR SOLO TAGS PERMITIDOS POR SUBCATEGORÍA
                    Object.entries(tagMap).forEach(([flag, tag]) => {
                        if (selectedSub[flag]) {
                            const container = document.getElementById(tag.container);
                            if (container) container.classList.remove('d-none');
                        }
                    });

                    imagesContainer.classList.remove('d-none');

                    calculateDatesAndCosts();
                };

                calculateDatesAndCosts();
            });
    });

    // ABRIR MODAL DE IMÁGENES
    openImagesBtn?.addEventListener('click', () => {

        imagesGrid.innerHTML = `<small class="text-muted">Cargando imágenes...</small>`;
        tempSelectedImages = [];

        fetch(`/advertising/subcategories/${currentSubcategory}/images`)
            .then(r => r.json())
            .then(images => {

                imagesGrid.innerHTML = '';

                if (!images.length) {
                    imagesGrid.innerHTML = `
                        <small class="text-muted">
                            Esta subcategoría no tiene imágenes
                        </small>`;
                    return;
                }

                images.forEach(img => {

                    const card = document.createElement('div');
                    card.className = 'image-card';
                    card.innerHTML = `<img src="/${img.image}">`;

                    card.addEventListener('click', () => {

                    const index = tempSelectedImages.findIndex(i => i.id === img.id);

                    // Deseleccionar
                    if (index !== -1) {
                        tempSelectedImages.splice(index, 1);
                        card.classList.remove('border', 'border-dark');
                        return;
                    }

                    // Límite dinámico
                    if (tempSelectedImages.length >= MAX_IMAGES) {

                        if (MAX_IMAGES === 1) {
                            // Empleos → reemplazar
                            tempSelectedImages = [img];

                            document.querySelectorAll('.image-card')
                                .forEach(c => c.classList.remove('border', 'border-dark'));

                            card.classList.add('border', 'border-dark');
                        } else {
                            alert(`Solo puedes seleccionar máximo ${MAX_IMAGES} imágenes`);
                        }

                        return;
                    }

                    // Seleccionar
                    tempSelectedImages.push(img);
                    card.classList.add('border', 'border-dark');
                });


                    imagesGrid.appendChild(card);
                });
            });

        modal.show();
    });

    // CONFIRMAR IMÁGENES
    confirmBtn?.addEventListener('click', () => {

        if (!tempSelectedImages.length) return;

        // Guardar IDs
        selectedInput.value = tempSelectedImages.map(i => i.id).join(',');

        // Guardar imágenes de referencia (URLs)
        referenceImages = tempSelectedImages.map(img => `/${img.image}`);

        // Render previews visuales
        previewList.innerHTML = '';

        referenceImages.forEach(src => {
            const item = document.createElement('div');
            item.className = 'image-card border border-dark';
            item.style.maxWidth = '120px';
            item.innerHTML = `<img src="${src}">`;
            previewList.appendChild(item);
        });

        previewBox.classList.remove('d-none');
        modal.hide();

        updatePreview();
    });

});


let subcatPrice = 0;

// PRECIO URGENTE 
let urgentPrice = {{ $urgentPrice }};
let featuredPrice = {{ $featuredPrice }};
let premierePrice  = {{ $premierePrice  }};
let semiNewPrice   = {{ $semiNewPrice }};
let newPrice       = {{ $newPrice }};
let availablePrice = {{ $availablePrice }};
let topPrice       = {{ $topPrice }};

// DEFINE PRIMERO LA FUNCIÓN
function calculateDatesAndCosts() {

    if (!subcatPrice || subcatPrice <= 0) return;

    const daysInput = document.getElementById("days_active");
    if (!daysInput) return;

    let days = parseInt(daysInput.value);

    if (!days || days < 2) {
        days = 2;
        daysInput.value = 2;
    }

    let total = subcatPrice * days;

    if (document.getElementById("urgent_publication")?.checked) total += urgentPrice;
    if (document.getElementById("featured_publication")?.checked) total += featuredPrice;
    if (document.getElementById("premiere_publication_switch")?.checked) total += premierePrice;
    if (document.getElementById("semi_new_publication")?.checked) total += semiNewPrice;
    if (document.getElementById("new_publication")?.checked) total += newPrice;
    if (document.getElementById("available_publication")?.checked) total += availablePrice;
    if (document.getElementById("top_publication")?.checked) total += topPrice;

    // ELEMENTOS VISUALES (PROTEGIDOS)
    const pricePerDayEl = document.getElementById("pricePerDay");
    const totalCostEl = document.getElementById("totalCost");
    const summaryTotalCostEl = document.getElementById("summaryTotalCost");
    const expiresAtEl = document.getElementById("expiresAt");

    if (pricePerDayEl) {
        pricePerDayEl.value = `S/. ${subcatPrice.toFixed(2)}`;
    }

    if (totalCostEl) {
        totalCostEl.value = `S/. ${total.toFixed(2)}`;
    }

    if (summaryTotalCostEl) {
        summaryTotalCostEl.textContent = `S/. ${total.toFixed(2)}`;
    }

    if (expiresAtEl) {
        const today = new Date();
        today.setDate(today.getDate() + days);
        expiresAtEl.value = today.toISOString().split("T")[0];
    }
}

document.addEventListener("DOMContentLoaded", () => {

    // CARGAR CAMPOS + PRECIO 
    document.getElementById('subcategorySelect').addEventListener('change', function () {

        const subcatId = this.value;
        const categoryId = document.getElementById('categorySelect').value;
        const fieldsContainer = document.getElementById('fieldsContainer');

        fieldsContainer.innerHTML = "";
        showMainFields();

        if (!subcatId) return;

        // OBTENER PRECIO DE SUBCATEGORÍA
        fetch(`/advertising/my-ads/subcategories/${categoryId}`)
            .then(res => res.json())
            .then(subcategories => {

                const selected = subcategories.find(s => s.id == subcatId);

                if (!selected) return;

                subcatPrice = parseFloat(selected.price ?? 0);
                document.getElementById("pricePerDay").value = `S/. ${subcatPrice.toFixed(2)}`;
                document.getElementById("costContainer").classList.remove("d-none");
                calculateDatesAndCosts();
            });

        // OBTENER CAMPOS DINÁMICOS (ESTO FALTABA)
        fetch(`/advertising/fields/${subcatId}`)
            .then(res => res.json())
            .then(fields => {

                if (!fields.length) return;

                fields.forEach(field => {

                    let input = '';

                    switch (field.type) {

                        case 'number':
                            input = `<input type="number" class="form-control" name="dynamic[${field.id}]">`;
                            break;

                        case 'textarea':
                            input = `
                                <textarea 
                                    class="form-control dynamic-lowercase"
                                    name="dynamic[${field.id}]" 
                                    rows="3"></textarea>`;
                            break;

                        default:
                            input = `
                                <input 
                                    type="text" 
                                    class="form-control dynamic-lowercase"
                                    name="dynamic[${field.id}]">`;
                    }

                    fieldsContainer.innerHTML += `
                        <div class="field-card">
                            <label class="fw-semibold">${field.name}</label>
                            ${input}
                        </div>
                    `;
                });
            });
    });

    //Mostrar Monto o No
    const amountContainer = document.getElementById('amountContainer');
    const amountInput = document.getElementById('amountInput');
    const amountVisibleCheckbox = document.getElementById('amountVisibleCheckbox');
    const amountVisibleInput = document.getElementById('amountVisibleInput');
    const amountTextSelect = document.getElementById('amountTextSelect');
    const amountTextInput = document.getElementById('amountTextInput');

    // Estado inicial
    toggleAmount(amountVisibleCheckbox.checked);

    function toggleAmount(visible) {
        if (visible) {
            amountInput.disabled = false;
            amountInput.required = true;
            amountTextSelect.classList.add('d-none');

            amountVisibleInput.value = 1;
            amountTextInput.value = "";
        } else {
            amountInput.disabled = true;
            amountInput.required = false;
            amountInput.value = "";

            amountTextSelect.classList.remove('d-none');

            amountVisibleInput.value = 0;
            amountTextInput.value = amountTextSelect.value || "";
        }
    }

    // Checkbox mostrar / ocultar
    amountVisibleCheckbox.addEventListener('change', function () {
        const visible = this.checked;

        amountVisibleInput.value = visible ? 1 : 0;

        if (!visible) {
            amountTextInput.value = amountTextSelect.value || "No especificado";
        } else {
            amountTextInput.value = null;
        }

        toggleAmount(visible);
        updatePreview();
    });

    // Select texto por defecto
    amountTextSelect.addEventListener('change', function () {
        if (!amountVisibleCheckbox.checked) {
            amountInput.value = this.value;
            amountTextInput.value = this.value;
            updatePreview();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {

        const titleInput = document.getElementById('titleInput');
        const charCount = document.getElementById('charCount');

        if (titleInput) {
            titleInput.addEventListener('input', () => {
                titleInput.value = titleInput.value.toUpperCase();
                charCount.textContent = titleInput.value.length;
            });
        }

    });

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('dynamic-lowercase')) {
            const cursorPos = e.target.selectionStart;
            e.target.value = e.target.value.toLowerCase();
            e.target.setSelectionRange(cursorPos, cursorPos);
        }
    });

        // escucha el cambio del switch de urgente
        safeListener("urgent_publication", "change", calculateDatesAndCosts);
        safeListener("featured_publication", "change", calculateDatesAndCosts);
        safeListener("premiere_publication_switch", "change", calculateDatesAndCosts);
        safeListener("semi_new_publication", "change", calculateDatesAndCosts);
        safeListener("new_publication", "change", calculateDatesAndCosts);
        safeListener("available_publication", "change", calculateDatesAndCosts);
        safeListener("top_publication", "change", calculateDatesAndCosts);

        document.getElementById("days_active").addEventListener("input", () => {
            const input = document.getElementById("days_active");
            if (!input.value || parseInt(input.value) < 2) {
                input.value = 2;
            }
            calculateDatesAndCosts();
        });


    // MOSTRAR CAMPOS OBLIGATORIOS 
    function showMainFields() {
        document.getElementById('titleContainer').classList.remove('d-none');
        document.getElementById('descriptionContainer').classList.remove('d-none');
        document.getElementById('locationAdContainer').classList.remove('d-none');
        document.getElementById('contactLocationContainer').classList.remove('d-none');
        document.getElementById('contactDataContainer').classList.remove('d-none');
        document.getElementById('amountContainer').classList.remove('d-none');
        document.getElementById('imagesContainer').classList.remove('d-none');
        document.getElementById('costContainer').classList.remove('d-none');
        document.getElementById('urgentContainer').classList.remove('d-none');
        document.getElementById('featuredContainer').classList.remove('d-none');
        document.getElementById('summaryContainer')?.classList.remove('d-none');
        document.getElementById('receiptContainer').classList.remove('d-none');
    }

});

// COMPROBANTE: BOLETA - FACTURA - NOTA DE VENTA - PREVIEW - DESCARGA
const notaVentaFields = document.getElementById("notaVentaFields");

// Datos del usuario autenticado (Blade)
const authUser = {
    dni: "{{ auth()->user()->dni ?? '' }}",
    full_name: "{{ auth()->user()->full_name ?? '' }}"
};


const receiptType = document.getElementById("receipt_type");
const boletaFields = document.getElementById("boletaFields");
const facturaFields = document.getElementById("facturaFields");
const receiptPreview = document.getElementById("receiptPreview");
const confirmReceiptBtn = document.getElementById("confirmReceiptBtn");

// Mostrar campos según tipo seleccionado
document.addEventListener("DOMContentLoaded", () => {

    receiptType.addEventListener("change", function () {
        const type = this.value;

        boletaFields.classList.add("d-none");
        facturaFields.classList.add("d-none");
        notaVentaFields.classList.add("d-none");

        if (type === "boleta") {
            boletaFields.classList.remove("d-none");
            document.getElementById("boleta_full_name").value = authUser.full_name;
            document.querySelector("[name='dni']").value = authUser.dni;
        }

        if (type === "factura") {
            facturaFields.classList.remove("d-none");
        }

        if (type === "nota_venta") {
            notaVentaFields.classList.remove("d-none");
            document.getElementById("nota_full_name").value = authUser.full_name;
        }

        confirmReceiptBtn.classList.remove("d-none");
        updateReceiptPreview();
    });

});

confirmReceiptBtn.classList.remove("d-none");


// Actualizar preview al escribir datos
document.addEventListener("input", function (e) {
    if (
        e.target.name === "dni" ||
        e.target.name === "full_name" ||
        e.target.name === "ruc" ||
        e.target.name === "company_name" ||
        e.target.name === "address"
    ) {
        updateReceiptPreview();
    }
});

/*Función de validación (marca campos y muestra errores)*/
function validateAdForm() {
    let isValid = true;

    // Limpia errores
    document.querySelectorAll(".error-text").forEach(e => e.remove());
    document.querySelectorAll(".is-invalid").forEach(e => e.classList.remove("is-invalid"));

    // VALIDACIÓN CAMPOS NORMALES
    const requiredFields = [
        { selector: "#categorySelect", message: "Selecciona una categoría" },
        { selector: "#subcategorySelect", message: "Selecciona una subcategoría" },
        { selector: "#titleInput", message: "El título es obligatorio" },
        { selector: "[name='description']", message: "La descripción es obligatoria" },
        { selector: "#days_active", message: "Indica los días de publicación" }//
    ];

    requiredFields.forEach(field => {
        const input = document.querySelector(field.selector);
        if (input && !input.closest(".d-none")) {
            if (!input.value || input.value.trim() === "") {
                isValid = false;
                showError(input, field.message);
            }
        }
    });

    // =====================
    // VALIDACIÓN MONTO / TEXTO (OBLIGATORIA)
    // =====================
    const amountContainer = document.getElementById("amountContainer");

    if (amountContainer && !amountContainer.classList.contains("d-none")) {

        const amountInput = document.getElementById("amountInput");
        const amountVisible = document.getElementById("amountVisibleCheckbox").checked;
        const amountTextSelect = document.getElementById("amountTextSelect");

        const hasAmount = amountVisible && amountInput.value && Number(amountInput.value) > 0;
        const hasText = !amountVisible && amountTextSelect.value;

        // Caso inválido: TODO vacío
        if (!hasAmount && !hasText) {
            isValid = false;

            showError(
                amountVisible ? amountInput : amountTextSelect,
                "Debes ingresar un monto o seleccionar un texto"
            );
        }
    }

    // =====================
    // VALIDACIÓN COMPROBANTE
    // =====================
    const receiptType = document.getElementById("receipt_type").value;

    if (receiptType === "boleta") {
        if (!checkField("[name='dni']", "El DNI es obligatorio")) isValid = false;
        if (!checkField("#boleta_full_name", "El nombre es obligatorio")) isValid = false;
    }

    if (receiptType === "factura") {
        if (!checkField("[name='ruc']", "El RUC es obligatorio")) isValid = false;
        if (!checkField("[name='company_name']", "La razón social es obligatoria")) isValid = false;
        if (!checkField("[name='address']", "La dirección es obligatoria")) isValid = false;
    }

    return isValid;
}


// Helper para validar campos
function checkField(selector, message) {
    const input = document.querySelector(selector);
    if (!input.value.trim()) {
        showError(input, message);
        return false;
    }
    return true;
}

// Mostrar error visual
function showError(input, message) {
    input.classList.add("is-invalid");

    const error = document.createElement("small");
    error.classList.add("text-danger", "error-text");
    error.textContent = message;

    input.parentNode.appendChild(error);

    input.scrollIntoView({ behavior: "smooth", block: "center" });
}

function updateReceiptPreview() {

    const type = receiptType.value;
    if (!type) {
        receiptPreview.innerHTML = `<small class="text-muted">Completa los datos para ver la previsualización.</small>`;
        return;
    }

    let html = `<strong>Tipo:</strong> ${type.toUpperCase()} <br>`;

    if (type === "boleta") {
        const dni = document.querySelector("[name='dni']").value || "-";
        const fullName = document.getElementById("boleta_full_name").value || "-";

        html += `
            <strong>DNI:</strong> ${dni}<br>
            <strong>Cliente:</strong> ${fullName}<br><br>
        `;
    }

    if (type === "factura") {
        const ruc = document.querySelector("[name='ruc']").value || "-";
        const company = document.querySelector("[name='company_name']").value || "-";
        const address = document.querySelector("[name='address']").value || "-";

        html += `
            <strong>RUC:</strong> ${ruc}<br>
            <strong>Razón Social:</strong> ${company}<br>
            <strong>Dirección:</strong> ${address}<br><br>
        `;
    }

    if (type === "nota_venta") {
        const fullName = document.getElementById("nota_full_name").value || "-";

        html += `
            <strong>Cliente:</strong> ${fullName}<br><br>
        `;
    }

    // Agregar resumen final
    const total = document.getElementById("summaryTotalCost").textContent;

        html += `
            <strong>Total a pagar:</strong> ${total}<br>
            <small class="text-muted">Este comprobante se generará al confirmar.</small>
        `;

        receiptPreview.innerHTML = html;

        confirmReceiptBtn.classList.remove("d-none");
    }

    // Acción al confirmar comprobante
    confirmReceiptBtn.addEventListener("click", function () {
        if (!validateAdForm()) return;

        confirmReceiptBtn.disabled = true;
        confirmReceiptBtn.textContent = "Enviando solicitud...";

        document.getElementById("adForm").submit();
    });

</script>

<style>
    .is-invalid {
        border: 1px solid #dc3545;
    }

    .badge-top,
    .badge-urgente {
        position: absolute;
        top: 8px;
        right: 8px;
        color: white;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        border-radius: 3px;
        z-index: 20;
        box-shadow: 0 1px 4px rgba(0,0,0,0.25);
    }

    .badge-top {
        background: #8e24aa;
    }

    .badge-urgente {
        background: red;
    }

    /* Imagen de la card */
    .card img {
        width: 100%;
        height: 400px; 
        object-fit: cover; 
        border-bottom: 1px solid #eee;
        background-color: #f3f3f3;
    }

    .ad-banner {
        width: 100%;
        height: 400px; 
        overflow: hidden;
        background: #f3f3f3;
    }

    .ad-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover; 
    }

    .ad-title {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    /* Estrella destacada */
    .star-destacado {
        font-size: 16px;
        color: #ffc107;
        filter: drop-shadow(0 0 2px rgba(255, 193, 7, 0.6));
        flex-shrink: 0;
    }

    /**/
    .badge-seminew {
        position: absolute;
        bottom: 8px;
        left: 8px;
        background: #6d4c41;
        color: #fff;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
    }

    .badge-new {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: #2e7d32;
        color: #fff;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
    }

    .badge-estreno,
    .badge-available {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #ffa726;
        color: white;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        border-radius: 3px;
        z-index: 20;
        box-shadow: 0 1px 4px rgba(0,0,0,0.25);
    }

    .badge-available {
        background: #0288d1;
    }

    /* CAMPOS DINÁMICOS EN PREVIEW */
    .ad-dynamic-fields {
        list-style: none;
        padding-left: 0;
        margin: 6px 0 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        font-size: 0.8rem;
        color: #555;
    }

    .ad-dynamic-fields li {
        background: #eef4ff;
        border: 1px solid #d6e4ff;
        border-radius: 6px;
        padding: 4px 8px;
        max-width: 100%;
        line-height: 1.35;
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal; 
    }

    .ad-dynamic-fields strong {
        font-weight: 600;
        color: #333;
    }

    .dynamic-value {
        display: inline;
    }

    /* === CARD HORIZONTAL PREMIUM === */
    .ad-card-horizontal {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #e7e7e7;
        transition: .25s;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .ad-card-horizontal:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.10);
    }

    /* Banner panorámico */
    .ad-banner {
        width: 100%;
        height: 220px;  
        overflow: hidden;
        background: #f3f3f3;
    }

    .ad-banner img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background-color: #f3f3f3; 
    }

    /* Contenido del anuncio */
    .ad-content {
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .ad-title {
        font-size: 15px;
        font-weight: 800;
        color: #202020;
        line-height: 1.3;
    }

    #titleInput {
        text-transform: uppercase;
    }

    /*Solo una linea en la card de descripcion*/
    .ad-desc {
        display: -webkit-box;
        -webkit-line-clamp: 1;  
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ad-desc {
        font-size: 12px;
        color: #606060;
        line-height: 1.4;
        margin-bottom: 4px;
    }

    /* Tags */
    .ad-tags {
        display: flex;
        gap: 10px;
        font-size: 12px;
        color: #555;
    }

    .ad-badge {
        background: #eef4ff;
        padding: 2px 6px;
        border-radius: 6px;
        color: #3a68d6;
        font-weight: 600;
    }

    .ad-location {
        font-size: 12px;
        color: #888;
    }

    .uppercase-input {
        text-transform: uppercase;
    }

    /* Precio */
    .ad-price-box {
        margin-top: 6px;
    }

    .ad-price {
        font-size: 18px;
        font-weight: 800;
        color: #d60000;
    }

    .ad-time {
        font-size: 12px;
        color: #777;
        margin-top: -4px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }


/* GRID */
.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 16px;
}

/* SCROLL CONTAINER */
.image-scroll {
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
