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

    <!-- FORMULARIO -->
    <form id="adForm" action="{{ route('my-ads.storeAdRequest') }}" method="POST" enctype="multipart/form-data">
        @csrf

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

        <!-- BOTÓN ENVIAR -->
        <button class="btn btn-danger w-100 py-2 fw-semibold mt-3 d-none" id="submitBtn">
            Publicar Anuncio
        </button>

    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    // CARGAR SUBCATEGORÍAS
    document.getElementById('categorySelect').addEventListener('change', function () {

        const categoryId = this.value;
        const subcatSelect = document.getElementById('subcategorySelect');
        const subcatContainer = document.getElementById('subcatContainer');

        subcatSelect.innerHTML = "";
        subcatContainer.classList.add('d-none');

        if (!categoryId) return;

        fetch(`/advertising/my-ads/subcategories/${categoryId}`)
            .then(res => res.json())
            .then(data => {

                let html = `<option value="">-- Selecciona --</option>`;

                data.forEach(sub => {
                    //html += `<option value="${sub.id}">${sub.name} (S/. ${sub.price})</option>`;
                    html += `<option value="${sub.id}">${sub.name}</option>`;
                });

                subcatSelect.innerHTML = html;
                subcatContainer.classList.remove('d-none');
            });
    });

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
                fields.forEach(f => {
                    fieldsContainer.innerHTML += `
                        <div class="field-card">
                            <label class="fw-semibold">${f.name}</label>
                            <input type="text" class="form-control" name="dynamic[${f.id}]">
                        </div>
                    `;
                });
            });
    });

    //Mostrar Monto o No
    // Elementos
    const amountContainer = document.getElementById('amountContainer');
    const amountInput = document.getElementById('amountInput');
    const amountVisibleCheckbox = document.getElementById('amountVisibleCheckbox');
    const amountVisibleInput = document.getElementById('amountVisibleInput');

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
            applyAmountVisibility(this.checked);
        });

        // Si tu función showMainFields() muestra el amountContainer, asegúrate de
        // reaplicar el estado (por si el usuario lo configuró antes):
        // Llamar applyAmountVisibility cuando se quite la clase d-none:
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(mutation => {
                if (mutation.attributeName === 'class') {
                    const hasDnone = amountContainer.classList.contains('d-none');
                    if (!hasDnone) {
                        // reaplicar por seguridad
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

        // escucha el cambio del switch de urgente
        document.getElementById("urgent_publication").addEventListener("change", updateTotalCost);
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

            if (document.getElementById("urgent_publication").checked) {
                total += urgentPrice;
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
        document.getElementById('submitBtn').classList.remove('d-none');
        document.getElementById('costContainer').classList.remove('d-none');
        document.getElementById('urgentContainer').classList.remove('d-none');
        document.getElementById('summaryContainer').classList.remove('d-none');

    }

});

</script>

@endsection
