<div class="navbar-bottom-detail shadow-lg bg-white p-2 position-fixed bottom-0 start-0 end-0">

    <!-- BOTÓN CONTACTAR -->
    <div class="text-center mb-1">
        <a href="/contact/{{ $ad->id }}" class="btn btn-danger px-4 py-1 fw-bold rounded-pill fs-6">
            Contactar con el Anunciante
        </a>
    </div>

    <!-- ICONOS DE CONTACTO DEL USUARIO -->
    <div class="d-flex justify-content-around text-center fs-6">

        {{-- WhatsApp --}}
        <div>
            <a href="https://wa.me/{{ $ad->whatsapp ?? $ad->user->whatsapp ?? $ad->user->phone }}"
            target="_blank"
            class="text-success fs-3 d-block">
                <i class="fab fa-whatsapp"></i>
            </a>

            <small class="text-secondary">WhatsApp</small>
        </div>

        {{-- Llamada --}}
        <div>
            <a href="tel:{{ $ad->call_phone ?? $ad->user->call_phone ?? $ad->user->phone }}"
            class="text-primary fs-3 d-block">
                <i class="fa-solid fa-phone"></i>
            </a>
            <small class="text-secondary">Llamar</small>
        </div>

        {{-- Email --}}
        @if($ad->user->email)
            <div>
                <a href="mailto:{{ $ad->user->email }}" class="text-danger fs-3 d-block">
                    <i class="fa-solid fa-envelope"></i>
                </a>
                <small class="text-secondary">Email</small>
            </div>
        @endif

        {{-- Dirección --}}
        <div>
            <a href="https://www.google.com/maps/search/{{ urlencode($ad->user->address) }}"
               target="_blank"
               class="text-dark fs-3 d-block">
                <i class="fa-solid fa-location-dot"></i>
            </a>
            <small class="text-secondary">Dirección</small>
        </div>

    </div>
</div>

<style>
/* Ajuste de tamaño para navbar */
.navbar-bottom-detail {
    font-size: 14px; 
}

.navbar-bottom-detail .btn {
    padding: 0.5rem 1.25rem; 
    font-size: 0.875rem; 
}

.navbar-bottom-detail .d-flex {
    gap: 1rem; 
}

.navbar-bottom-detail .fs-3 {
    font-size: 1.25rem; 
}

.navbar-bottom-detail .fs-6 {
    font-size: 0.875rem; 
}

.navbar-bottom-detail small {
    font-size: 0.75rem; 
}

</style>
