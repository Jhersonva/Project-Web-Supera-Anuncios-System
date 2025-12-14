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
            <form id="adForm" action="{{ route('my-ads.storeAdRequest') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- AQUÍ VA TODO TU FORMULARIO TAL COMO LO TIENES -->
                {{-- CATEGORÍA --}}
                <div class="field-card">
                    <label class="fw-semibold mb-2">Selecciona una Categoría</label>
                    <select id="categorySelect" name="category_id" class="form-select">
                        <option value="">-- Selecciona --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SUBCATEGORÍA --}}
                <div id="subcatContainer" class="field-card d-none">
                    <label class="fw-semibold mb-2">Selecciona una Subcategoría</label>
                    <select id="subcategorySelect" name="subcategory_id" class="form-select"></select>
                </div>

                {{-- LISTA DE CAMPOS DINÁMICOS --}}
                <div id="fieldsContainer"></div>

                {{-- Título --}}
                <div class="field-card d-none" id="titleContainer">
                    <label class="fw-semibold">Título del Anuncio</label>
                    <input type="text" class="form-control" name="title" placeholder="Ingresa un título descriptivo">
                </div>

                {{-- Descripción --}}
                <div class="field-card d-none" id="descriptionContainer">
                    <label class="fw-semibold">Descripción</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Describe tu anuncio"></textarea>
                </div>

                <div class="field-card d-none" id="contactLocationContainer">
                    <label class="fw-semibold">Ubicación de contacto</label>
                    <input type="text" name="contact_location" class="form-control" placeholder="Ej: Lima, Perú">
                </div>

                <!-- REEMPLAZAR: div id="amountContainer" -->
                <div class="field-card d-none" id="amountContainer">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div style="flex:1">
                            <label class="fw-semibold">Monto / Precio / Sueldo *</label>
                            <input type="number" step="0.01" min="0" name="amount" id="amountInput" class="form-control" required>
                            <small id="amountHelp" class="text-muted">Si marcas "Ocultar monto", el público verá "No especificado".</small>
                        </div>

                        <div style="min-width:170px; display:flex; align-items:center; justify-content:center;">
                            <div class="form-check form-switch" style="transform:scale(0.98);">
                                <input class="form-check-input" type="checkbox" id="amountVisibleCheckbox" checked>
                                <label class="form-check-label" for="amountVisibleCheckbox">Mostrar monto</label>
                            </div>
                        </div>
                    </div>

                    <!-- hidden field para enviar al backend si quiere controlarlo -->
                    <input type="hidden" name="amount_visible" id="amountVisibleInput" value="1">
                </div>

                <div class="field-card d-none" id="costContainer">
                    <label class="fw-semibold">Días de publicación *</label>
                    <input type="number" min="1" name="days_active" id="days_active" class="form-control" required>
                    <small class="text-muted">Indica cuántos días deseas que tu anuncio esté activo.</small>

                    <br>
                    <label class="fw-semibold">Costo por día</label>
                    <input type="text" id="pricePerDay" class="form-control mb-2" readonly>

                    <label class="fw-semibold mt-2">Costo total: Dia x Precio SubCategoria</label>
                    <input type="text" id="totalCost" class="form-control mb-2" readonly>

                    <label class="fw-semibold mt-2">Fecha de expiración</label>
                    <input type="text" id="expiresAt" class="form-control" readonly>
                </div>

                <!-- PUBLICACIÓN URGENTE (Afuera, independiente) -->
                <div class="field-card d-none" id="urgentContainer">
                    <label class="fw-semibold">¿Publicación urgente?</label>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="urgent_publication" name="urgent_publication" value="1">
                        <label class="form-check-label" for="urgent_publication">
                            Activar publicación urgente
                        </label>
                    </div>

                    <small class="text-muted">
                        Si activas esta opción, tu anuncio será marcado como "Urgente" y estara entre los primeros anuncios.
                    </small>

                    <small class="text-danger fw-bold">
                        Precio por publicación urgente: S/. {{ number_format($urgentPrice, 2) }}
                    </small>
                </div>

                <!-- PUBLICACIÓN DESTACADA (Afuera, independiente, igual que urgente) -->
                <div class="field-card d-none" id="featuredContainer">
                    <label class="fw-semibold">¿Publicación destacada?</label>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="featured_publication" name="featured_publication" value="1">
                        <label class="form-check-label" for="featured_publication">
                            Activar publicación destacada
                        </label>
                    </div>

                    <small class="text-muted">
                        Al activar esta opción, tu anuncio aparecerá en la sección de destacados con mayor visibilidad.
                    </small>

                    <small class="text-danger fw-bold">
                        Precio por publicación destacada: S/. {{ number_format($featuredPrice, 2) }}
                    </small>
                </div>

                <div class="field-card d-none" id="premiereContainer">
                    <label class="fw-semibold">¿Publicación estreno?</label>

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
                            value="0"
                        >

                        <label class="form-check-label" for="premiere_publication">
                            Activar publicación estreno
                        </label>
                    </div>

                    <small class="text-muted">
                        Esta opción se muestra solo para categorías de tipo inmueble.
                    </small>

                    <small class="text-danger fw-bold mt-1 d-block">
                        Precio de publicación estreno: S/. {{ number_format($premierePrice, 2) }}
                    </small>
                </div>

                {{-- RESUMEN DE COSTO Y SALDO --}}
                <div class="field-card d-none" id="summaryContainer">
                    <h5 class="fw-bold mb-3">Resumen de Pago</h5>

                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">Costo total:</span>
                        <span id="summaryTotalCost" class="fw-bold text-danger">S/. 0.00</span>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <span class="fw-semibold">Tu saldo:</span>
                        <span class="fw-bold text-success">S/. {{ number_format(auth()->user()->virtual_wallet, 2) }}</span>
                    </div>

                    <small class="text-muted d-block mt-2">
                        El costo se actualiza según los días y si activas la publicación urgente.
                    </small>
                </div>


                <div class="field-card d-none" id="imagesContainer">
                    <label class="fw-semibold">Imágenes (máx 5)</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">Puedes subir hasta 5 imágenes.</small>
                </div>

                <!-- COMPROBANTE -->
                <div class="field-card d-none" id="receiptContainer">

                    <h5 class="fw-bold mb-3">Datos para Comprobante de Pago</h5>

                    <!-- Tipo de comprobante -->
                    <label class="fw-semibold mb-2">Tipo de comprobante</label>
                    <select class="form-select" name="receipt_type" id="receipt_type">
                        <option value="">-- Selecciona --</option>
                        <option value="boleta">Boleta</option>
                        <option value="factura">Factura</option>
                    </select>

                    <!-- BOLETA -->
                    <div id="boletaFields" class="mt-3 d-none">
                        <label class="fw-semibold">DNI</label>
                        <input type="text" name="dni" class="form-control" maxlength="8">

                        <label class="fw-semibold mt-2">Nombre Completo</label>
                        <input type="text" name="full_name" class="form-control">
                    </div>

                    <!-- FACTURA -->
                    <div id="facturaFields" class="mt-3 d-none">
                        <label class="fw-semibold">RUC</label>
                        <input type="text" name="ruc" class="form-control" maxlength="11">

                        <label class="fw-semibold mt-2">Razón Social</label>
                        <input type="text" name="company_name" class="form-control">

                        <label class="fw-semibold mt-2">Dirección</label>
                        <input type="text" name="address" class="form-control">
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-2">Previsualización del Comprobante</h5>
                    <div class="p-3 border rounded bg-light" id="receiptPreview">
                        <small class="text-muted">Completa los datos para ver la previsualización.</small>
                    </div>

                    <button type="button" id="confirmReceiptBtn"
                        class="btn btn-danger w-100 mt-3 d-none">
                        Enviar Solicitud de Anuncio y Descargar Comprobante
                    </button>

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

// OBJETO BASE DEL PREVIEW
let adPreview = {
    images: [],
    title: "Título del anuncio",
    description: "Escribe una descripción...",
    subcategory: { name: "Subcategoría" },
    contact_location: "",
    amount: "",
    amount_visible: 1,   
    whatsapp: "",
    call_phone: "",
    urgent_publication: 0,
    featured_publication: 0
};


// CREA LA CARD DE PREVISUALIZACIÓN
function createAdCard(ad) {

    const img = ad.images.length > 0 
        ? ad.images[0].image 
        : "/assets/img/not-found-image/failed-image.jpg";

    return `
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">

            <div class="position-relative">
                <!-- Imagen -->
                <img src="${img}" class="w-100" style="height:180px; object-fit:cover;">

                <!-- DESTACADO -->
                ${ad.featured_publication == 1 ? `
                    <div class="badge-destacado">
                        DESTACADO
                    </div>
                ` : ''}

                <!-- URGENTE -->
                ${ad.urgent_publication == 1 ? `
                    <div class="badge-urgente">URGENTE</div>
                ` : ''}

                <!-- ESTRENO -->
                ${ad.premiere_publication == 1 ? `
                    <div class="badge-estreno">ESTRENO</div>
                ` : ''}

            </div>

            <div class="card-body">     

                <h3 class="ad-title">${ad.title}</h3>
                <p class="ad-desc">${ad.description}</p>

                <div class="ad-tags">
                    <span class="ad-badge"><i class="fa-solid fa-tag"></i> ${ad.subcategory.name}</span>
                    <span class="ad-location"><i class="fa-solid fa-location-dot"></i> ${ad.contact_location ?? "Sin ubicación"}</span>
                </div>

                <div class="ad-price-box">
                    <p class="fw-bold ${ad.amount_visible == 0 ? 'text-secondary' : 'text-success'}">
                        ${ad.amount_visible == 1 ? `S/. ${ad.amount}` : "S/ No especificado"}
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

                    <!-- Compartir -->
                    <button class="btn btn-sm btn-secondary">
                        <i class="fa-solid fa-share"></i> Compartir
                    </button>

                </div>

            </div>
        </div>
    `;
}


//PREVIEW DEL ANUNCIO
function updatePreview() {

    const ad = {
        title: document.querySelector("input[name='title']")?.value || "Título del anuncio",
        description: document.querySelector("textarea[name='description']")?.value || "Descripción del anuncio...",
        contact_location: document.querySelector("input[name='contact_location']")?.value || "Ubicación",

        amount: document.querySelector("#amountInput")?.value || "",
        amount_visible: document.querySelector("#amountVisibleInput")?.value || "1",

        featured_publication: document.querySelector("#featured_publication")?.checked ? 1 : 0,
        urgent_publication: document.querySelector("#urgent_publication")?.checked ? 1 : 0,
        premiere_publication: document.querySelector("#premiere_publication_switch")?.checked ? 1 : 0,

        subcategory: {
            name: document.querySelector("#subcategorySelect option:checked")?.textContent || "Subcategoría"
        },

        images: previewImages.length > 0 
            ? previewImages.map(img => ({ image: img }))
            : [],

        whatsapp: "{{ auth()->user()->whatsapp ?? '' }}",
        call_phone: "{{ auth()->user()->phone ?? '' }}",

        full_url: "#",
        time_ago: "Ahora"
    };

    document.querySelector("#previewCard").innerHTML = createAdCard(ad);
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


let previewImages = [];

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

// Seleccionar Categoria y Sub
document.addEventListener("DOMContentLoaded", () => {

    const categorySelect = document.getElementById('categorySelect');
    const subcatSelect = document.getElementById('subcategorySelect');
    const subcatContainer = document.getElementById('subcatContainer');
    const premiereContainer = document.getElementById('premiereContainer');
    const premiereSwitch = document.getElementById("premiere_publication_switch");
    const premiereHidden = document.getElementById("premiere_publication");

    if (premiereSwitch && premiereHidden) {
        premiereSwitch.addEventListener("change", function () {
            premiereHidden.value = this.checked ? "1" : "0";
            console.log("PREMIERE VALUE ENVIADO:", premiereHidden.value);
        });
    }

    // ---- CARGAR SUBCATEGORÍAS + DETECTAR CATEGORÍA ----
    categorySelect.addEventListener('change', function () {

        const categoryId = this.value;

        subcatSelect.innerHTML = "";
        subcatContainer.classList.add('d-none');
        premiereContainer.classList.add("d-none");

        if (!categoryId) return;

        fetch(`/advertising/my-ads/subcategories-with-category/${categoryId}`)
            .then(res => res.json())
            .then(data => {

                const isProperty = data.category.is_property;

                // Llenar subcategorías
                let html = `<option value="">-- Selecciona --</option>`;
                data.subcategories.forEach(sub => {
                    html += `<option value="${sub.id}">${sub.name}</option>`;
                });

                subcatSelect.innerHTML = html;
                subcatContainer.classList.remove('d-none');

                // Detectar selección de subcategoría
                subcatSelect.onchange = function () {
                    if (this.value && isProperty == 1) {
                        premiereContainer.classList.remove("d-none");
                    } else {
                        premiereContainer.classList.add("d-none");
                    }
                };
            });
    });
});


document.addEventListener("DOMContentLoaded", () => {

    let subcatPrice = 0;

    // CARGAR CAMPOS + PRECIO 
    document.getElementById('subcategorySelect').addEventListener('change', function () {

        const subcatId = this.value;
        const fieldsContainer = document.getElementById('fieldsContainer');

        fieldsContainer.innerHTML = "";
        showMainFields();

        if (!subcatId) return;

        // Obtener precio de subcategoría
        fetch(`/advertising/my-ads/subcategories/${document.getElementById('categorySelect').value}`)
            .then(res => res.json())
            .then(data => {

                const selected = data.find(s => s.id == subcatId);

                subcatPrice = parseFloat(selected.price ?? 0);

                document.getElementById("pricePerDay").value = `S/. ${subcatPrice.toFixed(2)}`;
                document.getElementById("costContainer").classList.remove("d-none");
            });

        // Cargar campos dinámicos
        fetch(`/advertising/fields/${subcatId}`)
            .then(res => res.json())
            .then(fields => {

                fields.forEach(field => {

                    // CAMPO ESPECIAL SOLO PARA EMPLEO → "Rubro"
                    if (field.type === 'multiple' && field.name === 'Rubro') {

                        fieldsContainer.innerHTML += `
                            <div class="field-card">
                                <label class="fw-semibold">${field.name} (máx 4)</label>

                                <div id="rubroList"></div>

                                <button type="button" class="btn btn-sm btn-primary mt-2" id="addRubroBtn">
                                    Agregar Rubro
                                </button>

                                <input type="hidden" name="dynamic[${field.id}]" id="rubroHidden">
                            </div>
                        `;

                    } else {

                        fieldsContainer.innerHTML += `
                            <div class="field-card">
                                <label class="fw-semibold">${field.name}</label>
                                <input type="text" class="form-control" name="dynamic[${field.id}]">
                            </div>
                        `;
                    }
                });

            })
            .catch(err => console.error("Error cargando campos:", err));
        });

    //Mostrar Monto o No
    const amountContainer = document.getElementById('amountContainer');
    const amountInput = document.getElementById('amountInput');
    const amountVisibleCheckbox = document.getElementById('amountVisibleCheckbox');
    const amountVisibleInput = document.getElementById('amountVisibleInput');

    // Función para actualizar el comportamiento del input
    function updateAmountInputVisibility(visible) {

        if (!visible) {
            // Ocultar monto
            amountInput.type = "text";
            amountInput.value = "S/ No especificado";
            amountInput.disabled = true;
        } else {
            // Mostrar monto
            amountInput.type = "number";
            amountInput.disabled = false;

            if (amountInput.value === "S/ No especificado") {
                amountInput.value = "";
            }
        }
    }

    // Asegurarnos que existen (evita errores si no se cargó el campo aún)
    if (amountContainer && amountInput && amountVisibleCheckbox && amountVisibleInput) {

        // Función para aplicar estado (llamada al cambiar o al mostrar campos)
        function applyAmountVisibility(visible) {
            if (visible) {
                amountInput.removeAttribute('disabled');
                amountInput.required = true;
                amountInput.placeholder = '';
                amountVisibleInput.value = "1";
                // si antes habías guardado un valor temporal en data, restáuralo
                if (amountInput.dataset.tmpVal) {
                    amountInput.value = amountInput.dataset.tmpVal;
                    delete amountInput.dataset.tmpVal;
                }
            } else {
                // guardamos temporalmente el valor para no perderlo
                amountInput.dataset.tmpVal = amountInput.value;
                amountInput.value = '';
                amountInput.setAttribute('disabled', 'disabled');
                amountInput.required = false;
                amountInput.placeholder = 'No especificado';
                amountVisibleInput.value = "0";
            }
        }

        // Inicializar según estado actual del checkbox
        applyAmountVisibility(amountVisibleCheckbox.checked);

        // Escuchar cambios del checkbox
        amountVisibleCheckbox.addEventListener('change', function () {
            const visible = this.checked;
            // Guardar en el input oculto (backend)
            amountVisibleInput.value = visible ? 1 : 0;
            // Guardar en la data del preview
            adPreview.amount_visible = visible ? 1 : 0;
            // Actualizar visualmente el input del formulario
            updateAmountInputVisibility(visible);
            // Actualizar tarjeta del preview
            updatePreview();
        });

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(mutation => {
                if (mutation.attributeName === 'class') {
                    const hasDnone = amountContainer.classList.contains('d-none');
                    if (!hasDnone) {
                        applyAmountVisibility(amountVisibleCheckbox.checked);
                    }
                }
            });
        });
        observer.observe(amountContainer, { attributes: true });
    }

    // CALCULAR TOTAL + FECHA 
    document.getElementById("days_active")
        .addEventListener("input", function () {

            const days = parseInt(this.value);

            if (!days || days <= 0) {
                document.getElementById('totalCost').value = "";
                document.getElementById('expiresAt').value = "";
                return;
            }

            const total = subcatPrice * days;
            document.getElementById("totalCost").value = `S/. ${total.toFixed(2)}`;

            const today = new Date();
            today.setDate(today.getDate() + days);

            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');

            document.getElementById("expiresAt").value = `${yyyy}-${mm}-${dd}`;
        });

        // PRECIO URGENTE 
        let urgentPrice = {{ $urgentPrice }};
        let featuredPrice = {{ $featuredPrice }};
        let premierePrice  = {{ $premierePrice  }};

        // escucha el cambio del switch de urgente
        document.getElementById("urgent_publication").addEventListener("change", updateTotalCost);
        // escucha el cambio del switch de destacado
        document.getElementById("featured_publication").addEventListener("change", updateTotalCost);
        // escucha el cambio del switch de estreno
        document.getElementById("premiere_publication_switch").addEventListener("change", updateTotalCost);
        // escucha cambios en días
        document.getElementById("days_active").addEventListener("input", updateTotalCost);

        function updateTotalCost() {

            const days = parseInt(document.getElementById("days_active").value);

            if (!days || days <= 0) {
                document.getElementById("totalCost").value = "";
                document.getElementById("summaryTotalCost").textContent = "S/. 0.00";
                return;
            }

            let total = subcatPrice * days;

            // Urgente
            if (document.getElementById("urgent_publication").checked) {
                total += urgentPrice;
            }

            // Destacado
            if (document.getElementById("featured_publication").checked) {
                total += featuredPrice;
            }

            // Estreno
            if (document.getElementById("premiere_publication_switch").checked) {
                total += premierePrice;
            }


            document.getElementById("totalCost").value = `S/. ${total.toFixed(2)}`;
            document.getElementById("summaryTotalCost").textContent = `S/. ${total.toFixed(2)}`;
        }

    // MOSTRAR CAMPOS OBLIGATORIOS 
    function showMainFields() {
        document.getElementById('titleContainer').classList.remove('d-none');
        document.getElementById('descriptionContainer').classList.remove('d-none');
        document.getElementById('contactLocationContainer').classList.remove('d-none');
        document.getElementById('amountContainer').classList.remove('d-none');
        document.getElementById('imagesContainer').classList.remove('d-none');
        document.getElementById('costContainer').classList.remove('d-none');
        document.getElementById('urgentContainer').classList.remove('d-none');
        document.getElementById('featuredContainer').classList.remove('d-none');
        document.getElementById('summaryContainer').classList.remove('d-none');
        document.getElementById('receiptContainer').classList.remove('d-none');

    }

});

// SISTEMA DE RUBROS DINÁMICOS
let rubroCount = 0;

$(document).on("click", "#addRubroBtn", function () {
    if (rubroCount >= 4) {
        alert("Solo puedes agregar hasta 4 rubros.");
        return;
    }

    rubroCount++;

    $("#rubroList").append(`
        <input type="text" class="form-control mt-2 rubroInput" placeholder="Ej: Cocinero">
    `);

    updateRubroHidden();
});

$(document).on("input", ".rubroInput", function () {
    updateRubroHidden();
});

function updateRubroHidden() {
    let values = [];

    $(".rubroInput").each(function () {
        if ($(this).val().trim() !== "") {
            values.push($(this).val().trim());
        }
    });

    $("#rubroHidden").val(values.join(", "));
}


// COMPROBANTE: BOLETA - FACTURA - PREVIEW - DESCARGA

const receiptType = document.getElementById("receipt_type");
const boletaFields = document.getElementById("boletaFields");
const facturaFields = document.getElementById("facturaFields");
const receiptPreview = document.getElementById("receiptPreview");
const confirmReceiptBtn = document.getElementById("confirmReceiptBtn");

// Mostrar campos según tipo seleccionado
receiptType.addEventListener("change", function () {
    const type = this.value;

    boletaFields.classList.add("d-none");
    facturaFields.classList.add("d-none");
    confirmReceiptBtn.classList.add("d-none");

    if (type === "boleta") {
        boletaFields.classList.remove("d-none");
    }
    if (type === "factura") {
        facturaFields.classList.remove("d-none");
    }

    updateReceiptPreview();
});

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

function updateReceiptPreview() {
console.log("Elemento:", document.getElementById("ID_DEL_ELEMENTO"));

    const type = receiptType.value;
    if (!type) {
        receiptPreview.innerHTML = `<small class="text-muted">Completa los datos para ver la previsualización.</small>`;
        return;
    }

    let html = `<strong>Tipo:</strong> ${type.toUpperCase()} <br>`;

    if (type === "boleta") {
        const dni = document.querySelector("[name='dni']").value || "-";
        const fullName = document.querySelector("[name='full_name']").value || "-";

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
        confirmReceiptBtn.disabled = true;
        confirmReceiptBtn.textContent = "Enviando solicitud...";

        document.getElementById("adForm").submit();
    });

</script>

<style>
    .badge-urgente {
        position: absolute;
        top: 15px;
        right: -63px;        
        background: red;
        color: white;
        padding: 6px 60px;   
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        transform: rotate(45deg);
        z-index: 20;
        box-shadow: 0 0 6px rgba(0,0,0,0.3);
        pointer-events: none;  
    }

    .badge-urgente span {
        position: absolute;
        top: -50px;  
        right: -3px; 
        color: white;
        font-size: 13px;
        font-weight: bold;
        transform: rotate(45deg); 
        text-transform: uppercase;
    }

    .ad-banner {
        position: relative;
    }

    .badge-destacado {
        position: absolute;
        bottom: 12px;
        left: 12px;
        background: linear-gradient(135deg, #f7d458, #e0b743);
        color: #4a3a00;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.25);
        z-index: 20;
        border: 1px solid rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(2px);
    }

    /* Icono estrella más elegante */
    .badge-destacado::before {
        content: "⭐";
        font-size: 14px;
        filter: drop-shadow(0 0 2px rgba(255,255,255,0.7));
    }

    /* CINTA ESTRENO (izquierda) */
    .badge-estreno {
        position: absolute;
        top: 15px;
        left: -63px;
        background: #ffa726;
        color: white;
        padding: 6px 60px;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        transform: rotate(-45deg);
        z-index: 20;
        box-shadow: 0 0 6px rgba(0,0,0,0.3);
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
</style>

@endsection
