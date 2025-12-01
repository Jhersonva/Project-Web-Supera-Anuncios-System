<div class="navbar-bottom-detail shadow-lg bg-white p-3 position-fixed bottom-0 start-0 end-0">

    <!-- BOTÓN CONTACTAR -->
    <div class="text-center mb-2">
        <a href="/contact/{{ $ad->id }}" class="btn btn-danger px-5 py-2 fw-bold rounded-pill">
            Contactar con el Anunciante
        </a>
    </div>

    <!-- ICONOS DE CONTACTO -->
    <div class="d-flex justify-content-around text-center">

        <div>
            <a href="https://wa.me/{{ $ad->phone ?? '' }}" target="_blank" class="text-success fs-4 d-block">
                <i class="fab fa-whatsapp"></i>
            </a>
            <small class="text-secondary">WhatsApp</small>
        </div>

        <div>
            <a href="tel:{{ $ad->phone ?? '' }}" class="text-primary fs-4 d-block">
                <i class="fa-solid fa-phone"></i>
            </a>
            <small class="text-secondary">Llamar</small>
        </div>

        <div>
            <a href="mailto:{{ $ad->email ?? '' }}" class="text-danger fs-4 d-block">
                <i class="fa-solid fa-envelope"></i>
            </a>
            <small class="text-secondary">Email</small>
        </div>

        <div>
            <a href="#" class="text-dark fs-4 d-block">
                <i class="fa-solid fa-location-dot"></i>
            </a>
            <small class="text-secondary">Dirección</small>
        </div>

    </div>
</div>
