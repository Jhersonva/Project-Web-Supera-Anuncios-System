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
                Swal.fire({
                    title: alertData.title,
                    html: `
                        ${alertData.logo ? `<img src="/${alertData.logo}" style="max-width:120px" class="mb-3 rounded">` : ''}
                        <p>${alertData.description}</p>
                        <a href="${window.ADULT_TERMS_URL}" target="_blank" class="fw-bold text-primary">Términos y Condiciones</a>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar y continuar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#dc3545',
                }).then((result) => {
                    if (result.isConfirmed) {
                        filterAdsFull(titleQuery, locationQuery, categoryId, subcategoryId);
                    }
                    // si cancela, simplemente no hace nada
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

document.addEventListener("DOMContentLoaded", function () {
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
    container.innerHTML = '';

    const adsMap = new Map(); //clave: ad.id

    // Destacados primero (prioridad máxima)
    if (data.featured?.data?.length > 0) {
        data.featured.data.forEach(ad => {
            adsMap.set(ad.id, ad);
        });
    }

    // Resto de tipos (si ya existe, NO se duplica)
    const otherTypes = ['urgent', 'premiere', 'semi_new', 'new', 'available', 'top', 'normal'];

    otherTypes.forEach(type => {
        if (data[type]?.data?.length > 0) {
            data[type].data.forEach(ad => {
                if (!adsMap.has(ad.id)) {
                    adsMap.set(ad.id, ad);
                }
            });
        }
    });

    const finalAds = Array.from(adsMap.values());

    // Si no hay anuncios
    if (finalAds.length === 0) {
        container.innerHTML = `<p class="text-center mt-4 fw-bold">No hay anuncios disponibles</p>`;
        return;
    }

    // Render único (SIN DUPLICADOS)
    finalAds.forEach(ad => {
        container.innerHTML += createAdCard(ad);
    });

    // Paginación SOLO NORMAL (se mantiene)
    if (data.normal?.data?.length > 0) {
        container.innerHTML += `
            <nav class="mt-3 d-flex justify-content-center">
                ${renderPagination(data.normal, 'normal')}
            </nav>
        `;
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

function createAdCard(ad){
    const img = ad.images.length
        ? '/' + ad.images[0].image
        : '/images/no-image.png';

    const subcategory = ad.subcategory?.name ?? "Sin subcategoría";

    // Detectar Servicios → Privados
    const isPrivateService =
        ad.ad_categories_id == window.SERVICIOS_CATEGORY_ID &&
        ad.ad_subcategories_id == window.PRIVADOS_SUBCATEGORY_ID;

    // Usuario
    const userImg = ad.user_info.profile_image;
    const userName = ad.user_info.full_name;
    const userVerified = ad.user_info.is_verified;
    const adVerified   = ad.is_verified;

    // verificado si el anuncio O el usuario lo está
    const showVerified = userVerified || adVerified;

    return `
    <div class="ad-card-wrapper col-12 col-md-6 col-lg-4 d-flex justify-content-center">
        <div class="ad-card-horizontal">

            <div class="position-relative">

                <img src="${img}" class="w-100 home-card-img">

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

                        <!-- Verificado -->
                        ${showVerified ? `
                            <img src="/assets/img/verified-icon/verified.png"
                                class="verified-icon-below"
                                title="${adVerified ? 'Anuncio verificado' : 'Usuario verificado'}">
                        ` : ''}
                    </div>
                </h3>

                <p class="ad-desc">${ad.description}</p>

                ${ad.dynamic_fields?.length ? `
                    <ul class="ad-dynamic-fields mt-2">
                        ${ad.dynamic_fields.map(f => `
                            <li>
                                <strong>${f.label}:</strong> ${f.value}
                            </li>
                        `).join("")}
                    </ul>
                ` : ''}

                <div class="ad-tags">
                    <span class="ad-badge"><i class="fa-solid fa-tag"></i> ${subcategory}</span>
                    <span class="ad-location">
                        <i class="fa-solid fa-location-dot"></i>
                        ${ad.department && ad.province ? `${ad.department} - ${ad.province}` : 'Sin ubicación'}
                    </span>
                </div>

                <div class="ad-price-box">
                    <p class="fw-bold ${ad.amount_visible === 0 ? 'text-secondary' : 'text-success'}">
                        ${
                            ad.amount_visible === 1
                                ? `S/ ${ad.amount}`
                                : ad.amount_text
                                    ? `S/ ${ad.amount_text}`
                                    : "S/ No especificado"
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

                    <!-- CHAT / CONTACTO -->
                    <button class="btn btn-sm btn-danger"
                        onclick="handleContact(${ad.id})">
                        <i class="fa-solid fa-comments"></i> Chat
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

//Accion Contactar
if (pendingAction === "contact") {
    handleContact(data.adId);
}


// Acción CONTACTAR
function handleContact(adId) {
    if (!isAuthenticated) {
        requireLogin("contact", { adId });
        return;
    }

    window.location.href = `/contact/${adId}`;
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