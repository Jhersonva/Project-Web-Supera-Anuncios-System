<!-- views/public/home.blade.php -->

@extends('layouts.app')

@section('content')

<div class="container mt-4 body-public">

    {{-- IMAGEN CENTRADA --}}
    <div class="text-center mb-3">
        <img src="/assets/img/logo/logo-supera-anuncios.jpeg" 
             alt="Banner" 
             style="width:200px; max-width:100%; border-radius:12px;">
    </div>

    {{-- TEXTO CENTRADO --}}
    <h5 class="text-center fw-bold mb-3">
        Lo que buscas, Aquí lo encuentras
    </h5>

    {{-- BUSCADOR --}}
    <div class="input-group mb-4">
        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input id="inputSearch" type="text" class="form-control" placeholder="Buscar anuncios...">
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


<script>

let allAds = [];

document.addEventListener("DOMContentLoaded", function () {
    loadAds();

    document.getElementById("inputSearch").addEventListener("keyup", function () {
        const text = this.value.toLowerCase();
        const filtered = allAds.filter(ad => 
            ad.title.toLowerCase().includes(text) ||
            (ad.subcategory?.name ?? "").toLowerCase().includes(text)
        );
        renderAds(filtered);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    loadAds();
});

function loadAds() {
    fetch('/api/ads')
        .then(res => res.json())
        .then(data => renderAds(data));
}

function renderAds(data) {
    const container = document.getElementById('listaAnuncios');
    container.innerHTML = '';

    // --- URGENTES ---
    if(data.urgent.length > 0){
        container.innerHTML += `<h5 class="fw-bold mt-3 mb-2">🚨 Anuncios Urgentes</h5>`;
        data.urgent.forEach(ad => {
            container.innerHTML += createAdCard(ad);
        });

        if(data.urgent.length === 10){
            container.innerHTML += `<button class="btn btn-sm btn-outline-danger mb-3" onclick="showMoreUrgent()">
                Mostrar más urgentes
            </button>`;
        }
    }

    // --- NO URGENTES ---
    if(data.normal.length > 0){
        container.innerHTML += `<h5 class="fw-bold mt-3 mb-2">📌 Otros Anuncios</h5>`;
        data.normal.forEach(ad => {
            container.innerHTML += createAdCard(ad);
        });
    }
}

function createAdCard(ad){
    const img = ad.images.length
        ? '/' + ad.images[0].image
        : '/images/no-image.png';

    const location = ad.location ?? "Ubicación no especificada";
    const subcategory = ad.subcategory?.name ?? "Sin subcategoría";

    return `
    <div class="col-12 col-md-6 col-lg-4">
        <div class="ad-card-horizontal">

            <div class="ad-banner">
                <img src="${img}" alt="Imagen del anuncio">
            </div>

            <div class="ad-content">

                <h3 class="ad-title">${ad.title.substring(0, 70)}...</h3>

                <p class="ad-desc">${ad.description.substring(0, 140)}...</p>

                <div class="ad-tags">
                    <span class="ad-badge"><i class="fa-solid fa-tag"></i> ${subcategory}</span>
                    <span class="ad-location"><i class="fa-solid fa-location-dot"></i> ${ad.contact_location ?? "Sin ubicación"}</span>
                </div>

                <div class="ad-price-box">
                    <p class="fw-bold text-success">
                        S/ ${ ad.amount }
                    </p>
                </div>

                <div class="ad-buttons">
                    <button class="btn btn-sm btn-primary" onclick="window.location.href='${ad.full_url}'">
                        <i class="fa-solid fa-eye"></i> Ver
                    </button>

                    <button class="btn btn-sm btn-success" onclick="window.location.href='/contact/${ad.id}'">
                        <i class="fa-solid fa-phone"></i> Contactar
                    </button>

                    <button class="btn btn-sm btn-secondary" onclick='shareAd(${JSON.stringify(ad).replace(/"/g,"&quot;")})'>
                        <i class="fa-solid fa-share"></i> Compartir
                    </button>
                </div>

            </div>

        </div>
    </div>`;
}

function shareAd(ad) {
    const url = ad.full_url;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalCompartir'));
    modal.show();

    // Poner link en el input
    document.getElementById('linkCompartir').value = url;

    // Actualizar links de redes
    const title = ad.title;
    document.getElementById('shareWhatsapp').href = `https://api.whatsapp.com/send?text=${encodeURIComponent(title + " " + url)}`;
    document.getElementById('shareMessenger').href = `fb-messenger://share/?link=${encodeURIComponent(url)}`;
    document.getElementById('shareFacebook').href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
    document.getElementById('shareTelegram').href = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
    document.getElementById('shareTwitter').href = `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`;
}


// Función copiar al portapapeles
function showMoreUrgent() {
    fetch('/api/ads?urgent_more=1') 
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('listaAnuncios');
            data.urgent.forEach(ad => {
                container.innerHTML += createAdCard(ad);
            });
        });
}

function copiarLink() {
    const input = document.getElementById('linkCompartir');
    input.select();
    input.setSelectionRange(0, 99999); 
    document.execCommand('copy');
    alert('Enlace copiado al portapapeles');
}

</script>

<style>
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
        height: 140px;
        overflow: hidden;
        background: #f3f3f3;
    }

    .ad-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
        .ad-banner {
            height: 160px;
        }

        .ad-title {
            font-size: 17px;
        }

        .ad-price {
            font-size: 20px;
        }
    }
</style>