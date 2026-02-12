<!-- Navbar detalle anuncio -->
<nav class="navbar bg-white px-3 shadow-sm fixed-top d-flex justify-content-between align-items-center">

    {{-- IZQUIERDA: BOTÓN VOLVER --}}
    <a href="javascript:void(0)" onclick="history.back()" class="text-dark">
       <i class="fa-solid fa-arrow-left fs-5"></i>
    </a>

    {{-- CENTRO: TÍTULO --}}
    <span class="fw-bold">Detalle del Anuncio</span>

    {{-- DERECHA: FECHA --}}
    <span class="text-muted small">
        {{ $ad->created_at->format('d/m/Y') }}
    </span>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const backButton = document.querySelector("a[onclick='history.back()']");
        const lastPage = localStorage.getItem("last_ads_page");

        if (backButton && lastPage) {

            backButton.onclick = function (e) {
                e.preventDefault();

                // Limpiamos para que no quede guardado eternamente
                localStorage.removeItem("last_ads_page");

                window.location.href = lastPage;
            };
        }

    });
</script>