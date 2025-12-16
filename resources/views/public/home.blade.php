<!-- views/public/home.blade.php -->

@extends('layouts.app')

@section('content')

<div class="container mt-4 body-public">

    {{-- IMAGEN CENTRADA --}}
    <div class="text-center mb-3">
        <img src="{{ system_logo() }}" alt="{{ system_company_name() }}" style="width:200px; max-width:100%; border-radius:12px;">
    </div>

    {{-- TEXTO CENTRADO --}}
    <h5 class="text-center fw-bold mb-3">
        Lo que buscas, Aquí lo encuentras
    </h5>

    {{-- BUSCADOR + SELECTOR DE SUBCATEGORÍAS EN UNA MISMA FILA --}}
    <div class="row g-2 mb-4 align-items-center">

        <div class="col-12 col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input id="inputSearch" type="text" class="form-control"
                    placeholder="Buscar anuncios...">
            </div>
        </div>

        <div class="col-12 col-md-4">
            <select id="filterSubcategory" class="form-select">
                <option value="all">Todas las subcategorías</option>
            </select>
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
          <a id="shareTwitter" class="share-icon text-info fs-3" target="_blank"><i class="fa-brands fa-twitter"></i></a>

        </div>

      </div>

    </div>
  </div>
</div>


{{-- BOTÓN FLOTANTE --}}
@auth
<button class="btn btn-danger shadow btn-float d-flex align-items-center gap-2 px-3 py-2 rounded-pill"
        onclick="location.href='{{ route('my-ads.createAd') }}'">
    <i class="fa-solid fa-plus"></i>
    <span>Crear Anuncio</span>
</button>
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

const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

function requireLogin(action, payload = null) {

    // Guardamos lo que intentó hacer
    localStorage.setItem("pending_action", action);
    if (payload !== null) {
        localStorage.setItem("pending_payload", JSON.stringify(payload));
    }

    Swal.fire({
        icon: "warning",
        title: "Inicia sesión",
        text: "Para realizar esta acción necesitas iniciar sesión o crear una cuenta.",
        showCancelButton: true,
        confirmButtonText: "Iniciar sesión",
        cancelButtonText: "Crear cuenta"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "/auth/login";
        } else {
            window.location.href = "/auth/register";
        }
    });
}

let deferredPrompt;
const banner = document.getElementById('installBanner');
const installBtn = document.getElementById('installBtn');
const closeBtn = document.getElementById('closeInstall');

// Detecta si ya está instalada
const isInstalled =
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true;

if (!isInstalled) {
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;

        // Mostrar banner SIEMPRE que se recargue
        banner.classList.remove('d-none');
    });
}

installBtn?.addEventListener('click', async () => {
    if (!deferredPrompt) return;

    deferredPrompt.prompt();
    await deferredPrompt.userChoice;

    deferredPrompt = null;
    banner.classList.add('d-none');
});

closeBtn?.addEventListener('click', () => {
    banner.classList.add('d-none');
});


let allAds = { urgent: [], normal: [] };

 function loadSubcategories() {
    fetch('/api/subcategories')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById("filterSubcategory");

            data.forEach(sub => {
                let opt = document.createElement("option");
                opt.value = sub.id;
                opt.textContent = sub.name;
                select.appendChild(opt);
            });
        });
    }


document.addEventListener("DOMContentLoaded", function () {
    loadAds();
    loadSubcategories();

    const input = document.getElementById("inputSearch");

    input.addEventListener("keyup", function () {
        const text = this.value.toLowerCase().trim();

        // Si el buscador queda vacío → mostrar todo
        if (text === "") {
            renderAds(allAds);
            return;
        }

        const filteredUrgent = allAds.urgent.filter(ad =>
            ad.title.toLowerCase().includes(text) ||
            (ad.subcategory?.name ?? "").toLowerCase().includes(text) ||
            (ad.category?.name ?? "").toLowerCase().includes(text)
        );

        const filteredNormal = allAds.normal.filter(ad =>
            ad.title.toLowerCase().includes(text) ||
            (ad.subcategory?.name ?? "").toLowerCase().includes(text) ||
            (ad.category?.name ?? "").toLowerCase().includes(text)
        );

        renderAds({
            urgent: filteredUrgent,
            normal: filteredNormal
        });
    });

    document.getElementById("filterSubcategory").addEventListener("change", function () {
        const selected = this.value;

        // Si elige "Todas"
        if (selected === "all") {
            renderAds(allAds);
            return;
        }

        const filteredUrgent = allAds.urgent.filter(ad =>
            ad.subcategory?.id == selected
        );
        const filteredNormal = allAds.normal.filter(ad =>
            ad.subcategory?.id == selected
        );

        renderAds({
            urgent: filteredUrgent,
            normal: filteredNormal
        });
    });
});

function loadAds() {
    fetch('/api/ads')
        .then(res => res.json())
        .then(data => {
            allAds = data;  
            renderAds(data);
        });
}

function renderAds(data) {
    const container = document.getElementById('listaAnuncios');
    container.innerHTML = '';

    // DESTACADOS
    if (data.featured.data.length > 0) {
        container.innerHTML += `<h5 class="fw-bold mt-3 mb-2">⭐ Anuncios Destacados</h5>`;
        data.featured.data.forEach(ad => container.innerHTML += createAdCard(ad));

        container.innerHTML += `<nav class="mt-2 d-flex justify-content-center">
            ${renderPagination(data.featured, 'featured')}
        </nav>`;
    }

    // URGENTES
    if (data.urgent.data.length > 0) {
        container.innerHTML += `<h5 class="fw-bold mt-3 mb-2">🚨 Anuncios Urgentes</h5>`;
        data.urgent.data.forEach(ad => container.innerHTML += createAdCard(ad));

        container.innerHTML += `<nav class="mt-2 d-flex justify-content-center">
            ${renderPagination(data.urgent, 'urgent')}
        </nav>`;
    }

    // ESTRENO
    if (data.premiere?.data?.length > 0) {
        container.innerHTML += `<h5 class="fw-bold mt-3 mb-2">🔥 Anuncios en Estreno</h5>`;
        data.premiere.data.forEach(ad => container.innerHTML += createAdCard(ad));

        container.innerHTML += `<nav class="mt-2 d-flex justify-content-center">
            ${renderPagination(data.premiere, 'premiere')}
        </nav>`;
    }

    // NORMALES
    if (data.normal.data.length > 0) {
        container.innerHTML += `<h5 class="fw-bold mt-3 mb-2">📌 Otros Anuncios</h5>`;
        data.normal.data.forEach(ad => container.innerHTML += createAdCard(ad));

        container.innerHTML += `<nav class="mt-2 d-flex justify-content-center">
            ${renderPagination(data.normal, 'normal')}
        </nav>`;
    }
}

function renderPagination(paginatedData, type) {
    let html = `<ul class="pagination pagination-sm">`;

    // Página anterior
    if (paginatedData.prev_page_url) {
        html += `<li class="page-item">
            <a class="page-link" href="#" onclick="goToPage('${type}', ${paginatedData.current_page - 1}); return false;">&laquo;</a>
        </li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">&laquo;</span></li>`;
    }

    // Páginas numeradas
    for (let i = 1; i <= paginatedData.last_page; i++) {
        html += `<li class="page-item ${i === paginatedData.current_page ? 'active' : ''}">
            <a class="page-link" href="#" onclick="goToPage('${type}', ${i}); return false;">${i}</a>
        </li>`;
    }

    // Página siguiente
    if (paginatedData.next_page_url) {
        html += `<li class="page-item">
            <a class="page-link" href="#" onclick="goToPage('${type}', ${paginatedData.current_page + 1}); return false;">&raquo;</a>
        </li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">&raquo;</span></li>`;
    }

    html += `</ul>`;
    return html;
}

function goToPage(type, page) {
    const params = new URLSearchParams();
    if (type === 'urgent') params.set('page_urgent', page);
    if (type === 'normal') params.set('page_normal', page);

    fetch('/api/ads?' + params.toString())
        .then(res => res.json())
        .then(data => {
            allAds = data;
            renderAds(data);
        });
}


function createAdCard(ad){
    const img = ad.images.length
        ? '/' + ad.images[0].image
        : '/images/no-image.png';

    const subcategory = ad.subcategory?.name ?? "Sin subcategoría";

    return `
    <div class="col-12 col-md-6 col-lg-4">
        <div class="ad-card-horizontal">

            <div class="position-relative">

                <img src="${img}" class="w-100 home-card-img">

                <!-- DESTACADO -->
                ${ad.featured_publication == 1 ? `
                    <div class="badge-destacado">DESTACADO</div>
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


            <div class="ad-content">

                <h3 class="ad-title">${ad.title.substring(0, 70)}...</h3>

                <p class="ad-desc">${ad.description.substring(0, 140)}...</p>

                <div class="ad-tags">
                    <span class="ad-badge"><i class="fa-solid fa-tag"></i> ${subcategory}</span>
                    <span class="ad-location"><i class="fa-solid fa-location-dot"></i> ${ad.contact_location ?? "Sin ubicación"}</span>
                </div>

                <div class="ad-price-box">
                    <p class="fw-bold ${ad.amount_visible == 0 ? 'text-secondary' : 'text-success'}">
                        ${ ad.amount_visible == 1 ? `S/ ${ad.amount}` : "S/ No especificado" }
                    </p>
                </div>

                <div class="ad-buttons"> 

                    <!-- Ver -->
                    <button class="btn btn-sm btn-primary"
                        onclick="handleVer('${ad.full_url}')">
                        <i class="fa-solid fa-eye"></i> Ver
                    </button>

                    <!-- WhatsApp -->
                    <button class="btn btn-sm btn-success"
                        onclick="handleWhatsapp('${ad.whatsapp}', '${ad.title}')">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </button>

                    <!-- Llamar -->
                    <button class="btn btn-sm btn-info"
                        onclick="handleLlamada('${ad.call_phone}')">
                        <i class="fa-solid fa-phone"></i> Llamar
                    </button>


                    <!-- Compartir -->
                    <button class="btn btn-sm btn-secondary"
                        onclick='shareAd(${JSON.stringify(ad).replace(/"/g,"&quot;")})'>
                        <i class="fa-solid fa-share"></i> Compartir
                    </button>

                </div>
                
                <p class="ad-time">
                    <i class="fa-regular fa-clock"></i> ${ad.time_ago}
                </p>

            </div>

        </div>
    </div>`;
}

// -Acción VER
function handleVer(url) {
    if (!isAuthenticated) {
        requireLogin("ver", { url });
        return;
    }
    window.location.href = url;
}

// Acción WHATSAPP 
function handleWhatsapp(numero, titulo) {
    if (!isAuthenticated) {
        requireLogin("whatsapp", { numero, titulo });
        return;
    }
    abrirWhatsapp(numero, titulo);
}

// Acción LLAMAR
function handleLlamada(numero) {
    if (!isAuthenticated) {
        requireLogin("llamar", { numero });
        return;
    }
    realizarLlamada(numero);
}


// Detecta si es dispositivo móvil
function isMobileDevice() {
    return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
}

// WhatsApp (Web en PC / App en móvil)
function abrirWhatsapp(numero, titulo) {
    if (!numero) {
        alert("El anunciante no tiene número registrado.");
        return;
    }

    const mensaje = encodeURIComponent(`Hola, vi tu anuncio: ${titulo}`);

    if (isMobileDevice()) {
        // Móvil → abre en la app
        window.location.href = `https://wa.me/51${numero}?text=${mensaje}`;
    } else {
        // PC → abre WhatsApp Web
        window.open(`https://web.whatsapp.com/send?phone=51${numero}&text=${mensaje}`, "_blank");
    }
}

// Llamar (solo móvil, en PC abre WhatsApp Web)
function realizarLlamada(numero) {
    if (!numero) {
        alert("El anunciante no tiene número registrado.");
        return;
    }

    if (isMobileDevice()) {
        // Llamada directa en celular
        window.location.href = `tel:+51${numero}`;
    } else {
        // En PC → abrir WhatsApp Web
        window.open(`https://web.whatsapp.com/send?phone=51${numero}`, "_blank");
    }
}

// Función principal para abrir el modal y mostrar datos del anuncio
function shareAd(ad) {
    const modal = new bootstrap.Modal(document.getElementById('modalCompartir'));

    const link = ad.full_url;
    document.getElementById("linkCompartir").value = link;

    // Generar enlaces dinámicos de compartir
    document.getElementById("shareWhatsapp").href   = `https://wa.me/?text=${encodeURIComponent(link)}`;
    document.getElementById("shareMessenger").href  = `https://www.facebook.com/dialog/send?link=${encodeURIComponent(link)}&app_id=YOUR_APP_ID`;
    document.getElementById("shareFacebook").href   = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(link)}`;
    document.getElementById("shareTelegram").href   = `https://t.me/share/url?url=${encodeURIComponent(link)}`;
    document.getElementById("shareTwitter").href    = `https://twitter.com/intent/tweet?url=${encodeURIComponent(link)}`;

    modal.show();
}


// Copiar el link al portapapeles
function copiarLink() {
    const input = document.getElementById("linkCompartir");
    input.select();
    input.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(input.value)
        .then(() => {
            Swal.fire({
                icon: "success",
                title: "Enlace copiado",
                text: "Ahora puedes compartirlo donde quieras",
                timer: 1500,
                showConfirmButton: false
            });
        });
}

//  AUTO-EJECUTAR ACCIÓN PENDIENTE AL VOLVER DEL LOGIN
document.addEventListener("DOMContentLoaded", () => {
    const pendingAction = localStorage.getItem("pending_action");
    const pendingPayload = localStorage.getItem("pending_payload");

    // Si NO hay acción → no hacemos nada
    if (!pendingAction) return;

    const data = pendingPayload ? JSON.parse(pendingPayload) : null;

    // Se ejecuta solo si ahora SÍ está autenticado
    if (isAuthenticated) {

        if (pendingAction === "ver") {
            handleVer(data.url);
        }

        if (pendingAction === "whatsapp") {
            handleWhatsapp(data.numero, data.titulo);
        }

        if (pendingAction === "llamar") {
            handleLlamada(data.numero);
        }

        // Limpiar para que no se repita
        localStorage.removeItem("pending_action");
        localStorage.removeItem("pending_payload");
    }
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

    /* CINTA ESTRENO (izquierda, MISMA POSICIÓN Y TAMAÑO QUE URGENTE) */
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
        z-index: 25;
        box-shadow: 0 0 6px rgba(0,0,0,0.3);
        pointer-events: none;
    }

   /* CARD HORIZONTAL PREMIUM */

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

    /* ESTANDARIZAR LAS IMÁGENES DEL HOME */
    .home-card-img {
        width: 100%;
        height: 400px;       
        object-fit: cover;   
        object-position: center;
        border-bottom: 1px solid #eee;
        background-color: #f3f3f3;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .home-card-img {
            height: 260px;
        }
    }

</style>