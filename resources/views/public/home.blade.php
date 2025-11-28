<!-- views/public/home.blade.php -->

@extends('layouts.app')

@section('content')

<div class="container mt-4">

    {{-- BUSCADOR --}}
    <div class="input-group mb-3">
        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" class="form-control" placeholder="Buscar anuncios...">
    </div>

    {{-- BOTÓN VOLVER --}}
    <button id="btnVolver" class="btn btn-outline-secondary mb-3 d-none">
        ← Volver
    </button>

    {{-- CARDS DE ANUNCIOS --}}
    <div id="listaAnuncios" class="row g-3"></div>

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
document.addEventListener("DOMContentLoaded", function () {
    loadAds();
});

function loadAds() {
    fetch('/api/ads')
        .then(res => res.json())
        .then(data => renderAds(data));
}

function renderAds(ads) {

    const container = document.getElementById('listaAnuncios');
    container.innerHTML = '';

    ads.forEach(ad => {

        const img = ad.images.length
            ? '/' + ad.images[0].image
            : '/images/no-image.png';

        const location = ad.location ?? "Ubicación no especificada";
        const subcategory = ad.subcategory?.name ?? "Sin subcategoría";

        container.innerHTML += `
            <div class="col-12 col-md-6 col-lg-4">
    <div class="ad-card-horizontal">

        <div class="ad-banner">
            <img src="${img}" alt="Imagen del anuncio">
        </div>

        <div class="ad-content">

            <h3 class="ad-title">${ad.title}</h3>

            <p class="ad-desc">${ad.description.substring(0, 70)}...</p>

            <div class="ad-tags">
                <span class="ad-badge"><i class="fa-solid fa-tag"></i> ${subcategory}</span>
                <span class="ad-location"><i class="fa-solid fa-location-dot"></i> ${location}</span>
            </div>

            <div class="ad-price-box">
                <p class="fw-bold text-success">
                    S/ ${ ad.amount }
                </p>
            </div>
        </div>

    </div>
</div>

        `;
    });
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