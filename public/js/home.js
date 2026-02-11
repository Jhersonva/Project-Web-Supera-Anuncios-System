//js/home.js
const isAuthenticated = Boolean(window.IS_AUTHENTICATED);


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


//let allAds = { urgent: [], normal: [] };

document.addEventListener("DOMContentLoaded", function () {
    loadAds();

    const inputSearch = document.getElementById('inputSearch');
    const inputLocation = document.getElementById('inputLocation');
    const selectCategory = document.getElementById('selectCategory');
    const selectSubcategory = document.getElementById('selectSubcategory');
    const btnSearch = document.getElementById('btnSearch');
    const btnClear = document.getElementById('btnClear');

    // ENTER dispara búsqueda
    [inputSearch, inputLocation, selectCategory, selectSubcategory].forEach(el => {
        el.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnSearch.click();
            }
        });
    });

    // Llenar subcategorías según categoría
    selectCategory.addEventListener('change', function () {
        const categoryId = this.value;
        selectSubcategory.innerHTML = '<option value="">Todas las subcategorías</option>';
        if (!categoryId) return;
        allSubcategories.filter(sub => sub.ad_categories_id == categoryId)
                        .forEach(sub => {
                            const option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = sub.name;
                            selectSubcategory.appendChild(option);
                        });
    });

    // Botón Buscar
    btnSearch.addEventListener('click', function (e) {
        e.preventDefault();

        const titleQuery = inputSearch.value.trim().toLowerCase();
        const locationQuery = inputLocation.value.trim().toLowerCase();
        const categoryId = parseInt(selectCategory.value) || null;
        const subcategoryId = parseInt(selectSubcategory.value) || null;

        if (!titleQuery && !locationQuery && !categoryId && !subcategoryId) {
            alert("Ingresa algún término de búsqueda");
            return;
        }

        // Si es Servicios → Privados
        if (categoryId === window.SERVICIOS_CATEGORY_ID &&
            subcategoryId === window.PRIVADOS_SUBCATEGORY_ID) {

            // Usuario no autenticado
            if (!window.IS_AUTHENTICATED) {
                requireLogin("search_private_ads", { categoryId, subcategoryId, titleQuery, locationQuery });
                return;
            }

            // Usuario autenticado pero con alert adulto
            if (window.ALERTS?.length > 0) {
                const alertData = window.ALERTS[0];
                showAdultServicesSearchAlert(() => {
                    filterAdsFull(titleQuery, locationQuery, categoryId, subcategoryId);
                });
                return;
            }
        }

        // Búsqueda normal
        filterAdsFull(titleQuery, locationQuery, categoryId, subcategoryId);
    });

    // Botón Limpiar
    btnClear.addEventListener('click', function () {
        inputSearch.value = '';
        inputLocation.value = '';
        selectCategory.value = '';
        selectSubcategory.innerHTML = '<option value="">Todas las subcategorías</option>';
        renderAds(allAds);
    });
});

// Filtrar por título, ubicación, categoría y subcategoría
function filterAdsFull(titleQuery, locationQuery, categoryId, subcategoryId) {
    // Verificar si es Servicios → Privados y usuario no autenticado
    if (
        categoryId == window.SERVICIOS_CATEGORY_ID &&
        subcategoryId == window.PRIVADOS_SUBCATEGORY_ID &&
        !window.IS_AUTHENTICATED
    ) {
        requireLogin("search_private_ads", {
            categoryId,
            subcategoryId,
            titleQuery,
            locationQuery
        });
        return; 
    }

    // Construir URL de la API
    let url = `/api/ads?`;
    if (categoryId) url += `category_id=${categoryId}&`;
    if (subcategoryId) url += `subcategory_id=${subcategoryId}&`;

    // Guardar filtro SOLO si es Servicios → Privados
    if (
        categoryId === window.SERVICIOS_CATEGORY_ID &&
        subcategoryId === window.PRIVADOS_SUBCATEGORY_ID
    ) {
        localStorage.setItem('private_services_filter', JSON.stringify({
            titleQuery,
            locationQuery,
            categoryId,
            subcategoryId
        }));
    } else {
        // cualquier otra búsqueda limpia el guardado
        localStorage.removeItem('private_services_filter');
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            const filteredAds = [];

            for (const type in data) {
                if (!data[type]?.data) continue;
                filteredAds.push(...data[type].data);
            }

            // Filtrar por título y ubicación
            const finalAds = filteredAds.filter(ad => {
                const matchesTitle = titleQuery ? ad.title.toLowerCase().includes(titleQuery) : true;
                const matchesLocation = locationQuery
                    ? ((ad.province && ad.province.toLowerCase().includes(locationQuery)) ||
                    (ad.district && ad.district.toLowerCase().includes(locationQuery)))
                    : true;
                return matchesTitle && matchesLocation;
            });


            // Mostrar resultados
            const container = document.getElementById('listaAnuncios');
            if (finalAds.length === 0) {
                container.innerHTML = `<p class="text-center mt-4 fw-bold">No se encontraron anuncios para tu búsqueda</p>`;
                return;
            }

            const adsPayload = {
                featured: { data: [] },
                urgent: { data: [] },
                premiere: { data: [] },
                semi_new: { data: [] },
                new: { data: [] },
                available: { data: [] },
                top: { data: [] },
                normal: { data: finalAds } 
            };
            renderAds(adsPayload);
        });
}

// Mostrar resultados o mensaje de "no se encontraron"
function showResultsOrMessage(filtered, hasResults, query) {
    if (!hasResults) {
        const container = document.getElementById('listaAnuncios');
        container.innerHTML = `<p class="text-center mt-4 fw-bold">No se encontraron anuncios para "${query}"</p>`;
        return;
    }

    renderAds(filtered);
}

function showAdultAlert() {

    if (!window.ALERTS || window.ALERTS.length === 0) {
        return;
    }

    const alert = window.ALERTS[0]; 

    Swal.fire({
        title: alert.title,
        html: `
            ${alert.logo ? `
                <img src="/${alert.logo}" style="max-width:120px" class="mb-3 rounded">
            ` : ''}
            <p>${alert.description}</p>
            <a href="${window.ADULT_TERMS_URL}" target="_blank" class="fw-bold text-primary">
                Términos y Condiciones
            </a>
        `,
        icon: 'warning',
        confirmButtonText: 'Entendido'
    });
}

function getMainSearchHtml(alertData) {
    return `
        ${alertData.logo ? `
            <img src="/${alertData.logo}" style="max-width:120px" class="mb-3 rounded">
        ` : ''}
        <p>${alertData.description}</p>
        <button id="openTermsBtn"
            class="btn btn-link p-0 fw-bold text-primary">
            Términos y Condiciones
        </button>
    `;
}

function buildTermsHtml(terms) {
    if (!terms || !terms.length) {
        return '<p>No hay términos disponibles.</p>';
    }

    return terms.map(t => `
        <h6>${t.title}</h6>
        <p>${t.description}</p>
        <hr>
    `).join('');
}

function openMainAdultAlert(alertData, onAccept) {
    Swal.fire({
        title: alertData.title ?? 'Contenido sensible',
        icon: 'warning',
        html: getMainSearchHtml(alertData),
        showCancelButton: true,
        confirmButtonText: 'Aceptar y continuar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#dc3545',
        reverseButtons: true,
        allowOutsideClick: false,
        didOpen: bindTermsButton
    }).then(result => {
        if (result.isConfirmed && typeof onAccept === 'function') {
            onAccept();
        }
    });
}

function openTermsModal(alertData, onAccept) {
  
    fetch(window.ADULT_VIEW_TERMS_API)
        .then(res => res.json())
        .then(terms => {

            Swal.update({
                title: 'Términos y Condiciones',
                icon: undefined,
                showCancelButton: false,
                confirmButtonText: 'Volver',
                html: `
                    <div style="max-height:420px;overflow-y:auto;text-align:left;">
                        ${buildTermsHtml(terms)}
                    </div>
                `
            });

            Swal.getConfirmButton().onclick = () => {
                openMainAdultAlert(alertData, onAccept);
            };
        });
}

function bindTermsButton() {
    const btn = document.getElementById('openTermsBtn');
    if (!btn) return;

    btn.onclick = () => {
        const alertData = window.ALERTS[0];
        openTermsModal(alertData, window.__adultAcceptCallback);
    };
}

function showAdultServicesSearchAlert(onAccept) {
    if (!window.ALERTS || !window.ALERTS.length) return;

    const alertData = window.ALERTS[0];

    // Guardamos callback para reutilizarlo
    window.__adultAcceptCallback = onAccept;

    openMainAdultAlert(alertData, onAccept);
}

document.addEventListener("DOMContentLoaded", function () {

    // Restaurar filtro Servicios → Privados si existe
    const savedFilter = localStorage.getItem('private_services_filter');

    if (savedFilter) {
        const {
            titleQuery,
            locationQuery,
            categoryId,
            subcategoryId
        } = JSON.parse(savedFilter);

        inputSearch.value   = titleQuery ?? '';
        inputLocation.value = locationQuery ?? '';
        selectCategory.value = categoryId;

        // cargar subcategorías
        selectSubcategory.innerHTML = '<option value="">Todas las subcategorías</option>';
        allSubcategories
            .filter(sub => sub.ad_categories_id == categoryId)
            .forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.name;
                selectSubcategory.appendChild(option);
            });

        selectSubcategory.value = subcategoryId;

        // ejecutar búsqueda automáticamente
        filterAdsFull(titleQuery, locationQuery, categoryId, subcategoryId);
        return; // evita loadAds()
    }

    loadAds();
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

    const adsMap = new Map();

    if (data.featured?.data?.length > 0) {
        data.featured.data.forEach(ad => adsMap.set(ad.id, ad));
    }

    const otherTypes = ['urgent','premiere','semi_new','new','available','top','normal'];
    otherTypes.forEach(type => {
        data[type]?.data?.forEach(ad => {
            if (!adsMap.has(ad.id)) adsMap.set(ad.id, ad);
        });
    });

    const finalAds = Array.from(adsMap.values());

    if (!finalAds.length) {
        container.innerHTML = `<p class="text-center mt-4 fw-bold">No hay anuncios disponibles</p>`;
        return;
    }

    let html = '';

    finalAds.forEach(ad => {
        html += createAdCard(ad);
    });

    // PAGINACIÓN ANTES
    if (data.normal?.data?.length > 0) {
        html += `
            <nav class="mt-3 d-flex justify-content-center">
                ${renderPagination(data.normal, 'normal')}
            </nav>
        `;
    }

    container.innerHTML = html;
    initAdCarousels();
}

function initAdCarousels() {

    document.querySelectorAll('.carousel-container').forEach(container => {

        if (container.dataset.started === '1') return;
        container.dataset.started = '1';

        const images = JSON.parse(container.dataset.images);
        const crops  = JSON.parse(container.dataset.crops);

        if (images.length <= 1) return;

        const img = container.querySelector('.carousel-image');
        let index = 0;

        applyCrop(img, crops[0]);

        setInterval(() => {
            index = (index + 1) % images.length;
            img.src = images[index];
            applyCrop(img, crops[index]);
        }, 3000);
    });
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

    if (type === 'normal') {
        params.set('page_normal', page);
    }

    fetch('/api/ads?' + params.toString())
        .then(res => res.json())
        .then(data => {
            allAds = data;
            renderAds(data);
        });
}

function formatAmount(amount) {
    if (amount === null || amount === undefined) return '';

    return Number(amount).toLocaleString('es-PE', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

function applyCrop(img, crop) {

    const container = img.closest('.card-crop-box');
    const boxWidth  = container.offsetWidth;
    const boxHeight = container.offsetHeight;

    // Si no hay crop → mostrar imagen normal adaptada
    if (!crop || !crop.width || !crop.height) {

        img.style.transform = '';
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        img.style.top = '0';
        img.style.left = '0';

        return;
    }

    /*
        El crop fue creado en proporción 450/430
        Entonces escalamos según el ancho del crop
    */

    const scaleX = boxWidth  / crop.width;
    const scaleY = boxHeight / crop.height;

    // usamos el mayor para cubrir completamente el box
    const scale = Math.max(scaleX, scaleY);

    img.style.width = 'auto';
    img.style.height = 'auto';
    img.style.objectFit = 'unset';

    img.style.transform = `
        scale(${scale})
        translate(-${crop.x}px, -${crop.y}px)
    `;

    img.style.transformOrigin = 'top left';
}

function createAdCard(ad){
    const images = ad.images.length
        ? ad.images.map(i => '/' + i.image)
        : ['assets/img/not-found-image/failed-image.jpg'];

    const crops = ad.images.map(i => i.crop_data ?? null);

    const subcategory = ad.subcategory?.name ?? "Sin subcategoría";

    // Detectar Servicios → Privados
    const isPrivateService =
        ad.ad_categories_id == window.SERVICIOS_CATEGORY_ID &&
        ad.ad_subcategories_id == window.PRIVADOS_SUBCATEGORY_ID;

    // Usuario
    const userImg = ad.user_info.profile_image;
    const userName = ad.user_info.display_name ?? 'Usuario';
    const userVerified = Number(ad.user_info.is_verified) === 1;
    const adVerified   = Number(ad.is_verified) === 1;

    // verificado si el anuncio O el usuario lo está
    const amountVisible = Number(ad.amount_visible);
    const currencySymbol = ad.amount_currency === 'USD' ? '$' : 'S/';

    return `
    <div class="ad-card-wrapper col-12 col-md-6 col-lg-4 d-flex justify-content-center">
        <div class="ad-card-horizontal">

            <div class="position-relative">

                <div class="carousel-container card-crop-box"
                    data-images='${JSON.stringify(images)}'
                    data-crops='${JSON.stringify(crops)}'>

                    <img src="${images[0]}" class="carousel-image">
                </div>



                ${ad.top_publication == 1 ? `<div class="badge-top">TOP</div>`
                    : ad.urgent_publication
                    ? `<div class="badge-urgente">URGENTE</div>`
                    : ''
                }

                ${ad.premiere_publication == 1 ? `<div class="badge-estreno">ESTRENO</div>`
                    : ad.available_publication
                        ? `<div class="badge-available">DISPONIBLE</div>`
                        : ''
                }

                ${ad.semi_new_publication ? `<div class="badge-seminew">SEMI-NUEVO</div>` : ''}
                ${ad.new_publication ? `<div class="badge-new">NUEVO</div>` : ''}
                
            </div>

            <div class="ad-content">

                <h3 class="ad-title d-flex align-items-start">
                    ${ad.featured_publication == 1 ? `<span class="star-destacado">⭐</span>` : ''}
                    <span class="ad-title-text flex-grow-1">${ad.title}</span>

                    <!-- Acciones derecha -->
                    <div class="share-wrapper ms-2">

                        <!-- Compartir -->
                        <button class="btn btn-sm btn-secondary mb-1"
                            onclick='shareAd(${JSON.stringify(ad).replace(/"/g,"&quot;")})'>
                            <i class="fa-solid fa-share-nodes"></i>
                        </button>

                        <!-- SELLO USUARIO -->
                        ${userVerified ? `
                            <img src="/assets/img/verified-icon/verified.png"
                                class="verified-icon-below"
                                title="Usuario verificado">
                        ` : ''}

                        <!-- SELLO ANUNCIO -->
                        ${adVerified ? `
                            <img src="/assets/img/verified-icon/verified.png"
                                class="verified-icon-below ad-verified"
                                title="Anuncio verificado">
                        ` : ''}
                    </div>
                </h3>

                <p class="ad-desc">${ad.description}</p>

                ${ad.dynamic_fields?.length ? `
                    <ul class="ad-dynamic-fields mt-2">
                        ${ad.dynamic_fields.map(f => {
                            const value = String(f.value ?? '');
                            const truncated =
                                value.length > 70
                                    ? value.slice(0, 70) + '...'
                                    : value;

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
                    <span class="ad-badge"><i class="fa-solid fa-tag"></i> ${subcategory}</span>
                    <span class="ad-location">
                        <i class="fa-solid fa-location-dot"></i>
                        ${ad.district && ad.province ? `${ad.district} - ${ad.province}` : 'Sin ubicación'}
                    </span>
                </div>

                <div class="ad-price-box">
                    <p class="fw-bold ${amountVisible === 0 ? 'text-secondary' : 'text-success'}">
                        ${
                            amountVisible === 1
                                ? `${currencySymbol} ${formatAmount(ad.amount)}`
                                : ad.amount_text
                                    ? `${currencySymbol} ${ad.amount_text}`
                                    : 'No especificado'
                        }
                    </p>
                </div>

                <!-- USUARIO (SOLO SI NO ES PRIVADO) -->
                ${!isPrivateService ? `
                <div class="d-flex align-items-center mb-2 user-info">
                    <div class="position-relative me-2">
                        <img src="${userImg}" class="rounded-circle user-avatar">
                    </div>
                    <span class="fw-bold user-name">${userName}</span>
                </div>
                ` : ''}

                <div class="ad-buttons row g-2">

                    <div class="col-6 col-md-auto">
                        <button class="btn btn-sm btn-primary w-100"
                            onclick="handleVer('${ad.full_url}')">
                            <i class="fa-solid fa-eye"></i> Ver
                        </button>
                    </div>

                    <div class="col-6 col-md-auto">
                        <button class="btn btn-sm btn-success w-100"
                            onclick="handleWhatsapp('${ad.whatsapp}', '${ad.title}')">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                        </button>
                    </div>

                    <div class="col-6 col-md-auto">
                        <button class="btn btn-sm btn-info w-100"
                            onclick="handleLlamada('${ad.call_phone}')">
                            <i class="fa-solid fa-phone"></i> Llamar
                        </button>
                    </div>

                    <div class="col-6 col-md-auto">
                        <button class="btn btn-sm btn-danger w-100"
                            onclick="handleContact(${ad.id})">
                            <i class="fa-solid fa-comments"></i> Chat
                        </button>
                    </div>

                </div>
                
                <p class="ad-time">
                    <i class="fa-regular fa-clock"></i> ${ad.time_ago}
                </p>

            </div>

        </div>
    </div>`;
}

// Acción VER
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

// Acción CONTACTAR
function handleContact(adId) {
    if (!isAuthenticated) {
        requireLogin("contact", { adId });
        return;
    }

    window.location.href = `/contact/${adId}`;
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