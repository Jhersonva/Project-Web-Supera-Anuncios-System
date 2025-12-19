@php
    $system = \App\Models\SystemSetting::first();
    $complaintBook = \App\Models\ComplaintBookSetting::first();
@endphp

<footer class="footer-dark mt-5 pt-5 pb-4">
    <div class="container">

        <div class="row align-items-center footer-row">

            {{-- IZQUIERDA: INFO EMPRESA --}}
            <div class="col-md-6 mb-4 mb-md-0 footer-company">
                <div class="d-flex align-items-center gap-3 footer-company-inner">
                    @if($system?->logo)
                        <img src="{{ asset($system->logo) }}"
                             alt="{{ $system->company_name }}"
                             class="footer-logo">
                    @endif

                    <div>
                        <h6 class="fw-bold mb-1">
                            {{ $system->company_name ?? 'Nombre de la empresa' }}
                        </h6>
                        <p class="text-muted small mb-0">
                            {{ $system->company_description }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- DERECHA: LIBRO DE RECLAMACIONES --}}
            <div class="col-md-6 text-md-end footer-complaint">
                <a href="{{ route('public.complaint-book') }}"
                   class="d-inline-flex align-items-center gap-2 text-decoration-none">

                    <img src="{{ asset('assets/img/complaints_book/libro_reclamaciones.png') }}"
                         alt="Libro de Reclamaciones"
                         class="footer-complaint-img">
                </a>
            </div>

        </div>

        <hr class="my-3">

        <div class="text-center small text-muted">
            © {{ now()->year }} {{ $system->company_name }} — Todos los derechos reservados
        </div>
    </div>
</footer>


<style>
/* ===== FOOTER BASE ===== */
.footer-dark {
    background-color: #121212;
    color: #e0e0e0;
}

.footer-dark h6 {
    color: #ffffff;
}

.footer-dark p {
    color: #b5b5b5;
}

.footer-dark hr {
    border-color: rgba(255, 255, 255, 0.08);
}

.footer-dark .text-muted {
    color: #9a9a9a !important;
}

/* Logo */
.footer-logo {
    width: 90px;
    height: 90px;
    object-fit: contain;
    border-radius: 14px;
    background-color: #ffffff;
    padding: 6px;
}

/* Libro reclamaciones */
.footer-complaint-img {
    width: 180px;
}

/* ===== MOBILE ONLY ===== */
@media (max-width: 768px) {

    .footer-row {
        text-align: center;
    }

    .footer-company {
        display: flex;
        justify-content: center;
    }

    .footer-company-inner {
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .footer-company p {
        max-width: 280px;
        margin: 0 auto;
        font-size: 13px;
    }

    .footer-complaint {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .footer-complaint-img {
        width: 160px;
    }

    .footer-logo {
        width: 80px;
        height: 80px;
    }
}


</style>