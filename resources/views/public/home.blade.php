<!-- views/public/home.blade.php -->

@extends('layouts.app')

@section('content')

<div class="container mt-4 body-public">
    {{-- TEXTO CENTRADO --}}
    <h5 class="text-center fw-bold mb-3">
        Lo que buscas, Aquí lo encuentras
    </h5>

    {{-- BUSCADOR  --}}
    <div class="row justify-content-center mb-5">
        <div class="col-12 col-lg-10">
            <div class="search-bar d-flex flex-wrap gap-2 p-3 bg-white shadow-sm rounded-3 align-items-center">
                
                {{-- Input título --}}
                <div class="search-input flex-grow-1 position-relative">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input id="inputSearch" type="text" class="form-control" placeholder="Buscar anuncios por título...">
                </div>

                {{-- Input ubicación --}}
                <div class="search-input flex-grow-1 position-relative">
                    <i class="fa-solid fa-location-dot search-icon"></i>
                    <input id="inputLocation" type="text" class="form-control" placeholder="Provincia o Distrito...">
                </div>

                {{-- Select categoría --}}
                <div class="flex-grow-1">
                    <select id="selectCategory" class="form-select">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Select subcategoría --}}
                <div class="flex-grow-1">
                    <select id="selectSubcategory" class="form-select">
                        <option value="">Todas las subcategorías</option>
                    </select>
                </div>

                {{-- Botones --}}
                <div class="d-flex gap-2 flex-wrap">
                    <button id="btnSearch" class="btn btn-primary px-4">Buscar</button>
                    <button id="btnClear" class="btn btn-outline-secondary px-4">Limpiar</button>
                </div>
            </div>
        </div>
    </div>


    {{-- CARDS DE ANUNCIOS --}}
    <div id="listaAnuncios" class="row g-3"></div>

</div>

<!-- MODAL COMPARTIR -->
<div class="modal fade" id="modalCompartir" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Compartir anuncio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">

        <input id="linkCompartir" class="form-control mb-3" readonly>

        <button class="btn btn-outline-dark w-100 mb-2" onclick="copiarLink()">
          <i class="fa-solid fa-copy me-1"></i> Copiar enlace
        </button>

        <div class="d-flex flex-wrap justify-content-center gap-3 mt-3">

          <a id="shareWhatsapp" class="share-icon text-success fs-3" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
          <a id="shareMessenger" class="share-icon text-primary fs-3" target="_blank"><i class="fa-brands fa-facebook-messenger"></i></a>
          <a id="shareFacebook" class="share-icon text-primary fs-3" target="_blank"><i class="fa-brands fa-facebook"></i></a>
          <a id="shareTelegram" class="share-icon text-info fs-3" target="_blank"><i class="fa-brands fa-telegram"></i></a>
          <a id="shareTwitter" class="share-icon text-info fs-3" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>
        </div>

      </div>

    </div>
  </div>
</div>


{{-- BOTÓN FLOTANTE --}}
@auth
    @if(!in_array((int) auth()->user()->role_id, [1, 3]))
        <div class="floating-actions">

            <!-- WhatsApp -->
            <a href="https://wa.me/51{{ $systemSettings->whatsapp_number }}?text={{ urlencode('Hola quiero más información') }}"
               target="_blank"
               class="btn btn-success shadow d-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-2">
                <i class="fa-brands fa-whatsapp"></i>
            </a>

            <!-- Crear anuncio -->
            <button class="btn btn-danger shadow d-flex align-items-center gap-2 px-3 py-2 rounded-pill"
                    onclick="location.href='{{ route('my-ads.createAd') }}'">
                <i class="fa-solid fa-plus"></i>
                <span>Crear Anuncio</span>
            </button>

        </div>
    @endif
@endauth

<div id="installBanner"
     class="position-fixed bottom-0 start-0 end-0 bg-white border-top shadow p-3 d-none"
     style="z-index: 1050;">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <strong>Instala Supera Anuncios</strong><br>
            <small class="text-muted">Accede más rápido desde tu celular</small>
        </div>
        <div class="d-flex gap-2">
            <button id="installBtn" class="btn btn-danger btn-sm">
                Instalar
            </button>
            <button id="closeInstall" class="btn btn-outline-secondary btn-sm">
                ✕
            </button>
        </div>
    </div>
</div>

<script>
    window.ALERTS = [];
    window.ADULT_VIEW_TERMS_API = "{{ route('terminos.adult.view.terms') }}";
    @auth
        fetch("{{ route('api.alerts') }}")
            .then(response => response.json())
            .then(data => {
                window.ALERTS = data;
            })
            .catch(err => console.error("Error cargando alertas:", err));
    @endauth
    
    window.ADULT_TERMS_URL = "{{ route('adult.terms') }}";

    window.IS_AUTHENTICATED = @json(auth()->check());
    window.SERVICIOS_CATEGORY_ID = 4;
    window.PRIVADOS_SUBCATEGORY_ID = 21;
    const allSubcategories = @json($subcategories);
</script>

<script src="{{ asset('js/home.js') }}"></script>
<script src="{{ asset('js/auth-actions.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

    .floating-actions {
        position: fixed;
        bottom: 85px; 
        right: 20px;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        align-items: center; 
    }


    .floating-actions .btn {
        padding: 12px 20px;   
        font-size: 15px;      
        border-radius: 1000px; 
    }

    .floating-actions .fa-whatsapp {
        font-size: 20px;
    }

    .share-wrapper {
        position: relative;
        display: inline-block;
    }

    .verified-icon-below {
        position: absolute;
        top: 100%;       
        left: 50%;
        transform: translateX(-50%);
        width: 50px;      
        height: 50px;
        margin-top: 4px;
        pointer-events: none;
    }

    /* Contenedor del usuario */
    .user-info {
        display: flex;
        align-items: center;
        gap: 6px; 
    }

    /* Avatar del usuario */
    .user-avatar {
        width: 28px;
        height: 28px;
        object-fit: cover;
        border-radius: 50%;
        border: 1px solid #ddd;
    }

    /* Insignia de verificado */
    .verified-badge {
        width: 16px;
        height: 16px;
        position: absolute;
        bottom: 0;
        right: 0;
        transform: translate(25%, 25%);
    }

    /* Nombre de usuario */
    .user-name {
        font-size: 0.85rem;
        line-height: 1;
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

    .ad-banner {
        position: relative;
    }

    .ad-title {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    /* Estrella destacada */
    .star-destacado {
        font-size: 16px;
        color: #ffc107;
        filter: drop-shadow(0 0 2px rgba(255, 193, 7, 0.6));
        flex-shrink: 0;
    }

    /* CINTA ESTRENO (izquierda, MISMA POSICIÓN Y TAMAÑO QUE URGENTE) */
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

    /*Solo una linea en la card de descripcion*/
    .ad-desc {
        display: -webkit-box;
        -webkit-line-clamp: 1;  
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* CAMPOS DINÁMICOS EN PREVIEW */
    .ad-dynamic-fields {
        list-style: none;
        padding-left: 0;
        margin: 6px 0 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        font-size: 0.75rem;
        color: #555;
    }

    .ad-dynamic-fields li {
        background: #eef4ff;
        border: 1px solid #d6e4ff;
        border-radius: 6px;
        padding: 3px 8px;
        white-space: nowrap;
        display: flex;
        gap: 4px;
        max-width: 100%;
    }

    .ad-dynamic-fields li strong {
        font-weight: 600;
        color: #333;
        white-space: nowrap;
    }

    .dynamic-value {
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
        line-height: 1.3;
        flex: 1;
    }

    .ad-card-horizontal:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.10);
    }

    /* Banner panorámico */ 
    .ad-banner {
        width: 100%;
        height: 400px; 
        overflow: hidden;
        background: #f3f3f3;
    }

    .ad-banner img {
        width: 100%;
        height: 400px;
        object-fit: contain; 
        background-color: #f3f3f3; 
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

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .ad-banner {
            height: 260px; 
        }

        .ad-title {
            font-size: 17px;
        }

        .ad-price {
            font-size: 20px;
        }
    }

    /* Contenedor buscador */
    .search-bar {
        transition: 0.3s;
    }

    .search-bar:hover {
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }

    /* Inputs con íconos */
    .search-input {
        position: relative;
    }

    .search-input .search-icon {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        color: #888;
        font-size: 16px;
    }

    .search-input input {
        padding-left: 38px;
        height: 42px;
        border-radius: 8px;
        border: 1px solid #ccc;
        transition: border 0.3s, box-shadow 0.3s;
    }

    .search-input input:focus {
        border-color: #3a68d6;
        box-shadow: 0 0 0 3px rgba(58, 104, 214, 0.15);
        outline: none;
    }

    /* Selects */
    .search-bar .form-select {
        height: 42px;
        border-radius: 8px;
        border: 1px solid #ccc;
        transition: border 0.3s, box-shadow 0.3s;
    }

    .search-bar .form-select:focus {
        border-color: #3a68d6;
        box-shadow: 0 0 0 3px rgba(58, 104, 214, 0.15);
        outline: none;
    }

    /* Botones */
    .search-bar .btn-primary {
        height: 42px;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.3s;
    }

    .search-bar .btn-primary:hover {
        background-color: #2f55b0;
    }

    .search-bar .btn-outline-secondary {
        height: 42px;
        border-radius: 8px;
    }

    .privacy-modal {
    border-radius: 14px;
    }

    .privacy-modal::-webkit-scrollbar {
        width: 6px;
    }

    .privacy-modal::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,.2);
        border-radius: 4px;
    }

    .privacy-btn-confirm,
    .privacy-btn-cancel {
        padding: 10px 18px !important;
        font-size: 14px !important;
        border-radius: 8px !important;
    }

    /* Responsive: móviles */
    @media (max-width: 992px) {
        .search-bar {
            flex-direction: column;
            gap: 12px;
        }
        .search-bar .flex-grow-1 {
            width: 100%;
        }
        .search-bar .d-flex.gap-2 {
            justify-content: flex-start;
            width: 100%;
        }
    }

    /* ESTANDARIZAR LAS IMÁGENES DEL HOME */
    .home-card-img {
        width: 100%;
        height: 350px;       
        object-fit: cover;
        object-position: center;
        border-bottom: 1px solid #eee;
        background-color: #f3f3f3;
        transition: height 0.3s;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .home-card-img {
            height: 260px;
        }
    }

    /* Wrapper de la card para centrarla */
    .ad-card-wrapper {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

/* CARD HORIZONTAL PREMIUM */
.ad-card-horizontal {
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border-radius: 14px;
    overflow: visible;
    overflow: hidden;
    border: 1px solid #e7e7e7;
    transition: .25s;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);

    width: 100%;
    max-width: 420px;  
    min-height: 630px;  
}

/* Banner panorámico OR max-height: 320px;   */
.home-card-img {
    width: 100%;
    height: auto;          
    max-height: 420px;  
    object-fit: contain;   
    background-color: #f3f3f3;
    display: block;
}

/* Contenido del anuncio */
.ad-content {
    display: flex;
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 6px;
}

.carousel-container {
    overflow: hidden;
    border-radius: 14px 14px 0 0;
}

/* Responsive: tablets y móviles */
@media (max-width: 992px) {
    .ad-card-horizontal {
        max-width: 320px;
        min-height: 600px;
    }

    .ad-card-horizontal .home-card-img {
        height: 220px;
    }
}

@media (max-width: 768px) {
    .ad-card-horizontal {
        max-width: 280px;
        min-height: 550px;
    }

    .ad-card-horizontal .home-card-img {
        height: 180px;
    }
}

@media (max-width: 576px) {
    .ad-card-horizontal {
        max-width: 95%;  
        min-height: auto;
    }

    .ad-card-horizontal .home-card-img {
        height: 160px;
    }
}

.card-crop-box {
    width: 100%;
    height: 230px;
    overflow: hidden;
    position: relative;
    background: #f3f3f3;
}

.carousel-image {
    position: absolute;
    top: 0;
    left: 0;

    max-width: none;
    max-height: none;

    transform-origin: top left;
}

</style>