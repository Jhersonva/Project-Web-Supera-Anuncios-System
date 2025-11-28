@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/rechargues.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">



<div class="container mt-5 mb-5">
    
    <!-- TÍTULO -->
    <div class="text-center mb-4 mt-4">
        <h3 class="fw-bold">Recargar</h3>
        <p class="text-secondary">Aquí puedes hacer tus recargas, para publicar tu anuncio.</p>
    </div>

    <!-- SALDO DE BILLETERA -->
    <div class="d-flex justify-content-center mb-3">
        <div class="wallet-box d-flex align-items-center px-3 py-2 shadow-sm">

            <i class="fa-solid fa-wallet me-2 text-success" style="font-size: 1.6rem;"></i>

            <div>
                <span class="small text-muted">Mi saldo</span>
                <h5 class="m-0 fw-bold">S/. {{ number_format(Auth::user()->virtual_wallet, 2) }}</h5>
            </div>
        </div>
    </div>

  <!-- SELECTOR -->
  <div class="d-flex gap-2 mt-3">
    <button id="btnShowPromos" class="btn btn-danger w-50">Promociones</button>
    <button id="btnShowLibre" class="btn btn-outline-danger w-50">Recarga libre</button>
  </div>

  <!-- PROMOCIONES -->
  <div id="seccionPromos" class="mt-4" style="display:none;">
    <h5 class="fw-semibold">Promociones</h5>
    <div id="promoContainer" class="row g-3 mt-2"></div>
  </div>

  <!-- RECARGA LIBRE -->
  <div id="seccionLibre" class="mt-4" style="display:none;">
    <h5 class="fw-semibold">Ingresa un monto</h5>
    <input type="number" id="montoLibre" class="form-control mt-2" placeholder="Ingresa un monto (mínimo S/. 1)">
  </div>

  <hr class="my-4">

  <div id="infoPago" class="mt-4" style="display:none;"></div>

  <h5 class="fw-semibold">Método de pago</h5>

  <div class="row g-3 mt-2">
    @foreach(['yape','plin','bcp','interbank'] as $metodo)
      <div class="col-6 col-md-3">
        <div class="pago-opcion" data-metodo="{{ $metodo }}">
          <img src="{{ asset("/assets/img/pagos/$metodo" . ($metodo == 'plin' ? '.webp' : ($metodo == 'yape' ? '.png' : ($metodo == 'bcp' ? '.jpg' : '.png'))) ) }}">
          <p class="mt-2 text-capitalize">{{ $metodo }}</p>
        </div>
      </div>
    @endforeach
  </div>

  <!-- FORM PARA ENVIAR RECARGA -->
  <form method="POST" action="{{ route('recharges.store') }}" enctype="multipart/form-data">
    @csrf

    <input type="hidden" name="monto" id="inputMonto">
    <input type="hidden" name="metodo_pago" id="inputMetodo">

    <div class="mt-4">
      <label class="fw-bold">Sube tu comprobante (opcional)</label>
      <input type="file" name="img_cap_pago" class="form-control mt-2" accept="image/*">
    </div>

    <button id="btnRecargar" class="btn btn-danger w-100 mt-4 py-2 fw-bold" type="submit">
      Enviar solicitud de recarga
    </button>
  </form>

</div>

<script>
// =========================
// DATOS DE PAGO
// =========================
const datosPago = {
    yape: {
        nombre: "Jherson Valdez",
        numero: "987 654 321",
        qr: "{{ asset('assets/pagos/qr-yape.png') }}"
    },
    plin: {
        nombre: "Jherson Valdez",
        numero: "987 654 321",
        qr: "{{ asset('assets/pagos/qr-plin.png') }}"
    },
    bcp: {
        nombre: "Jherson Valdez",
        numero: "987 654 321",
        cuenta: "123-45678901-0-12",
        cci: "00212345678901234567"
    },
    interbank: {
        nombre: "Jherson Valdez",
        numero: "987 654 321",
        cuenta: "123-4567890123",
        cci: "00312345678901234567"
    }
};

const promociones = [
    { id: 1, monto: 10, bonus: 2 },
    { id: 2, monto: 20, bonus: 5 },
    { id: 3, monto: 50, bonus: 15 },
    { id: 4, monto: 100, bonus: 40 }
];

const seccionPromos = document.getElementById("seccionPromos");
const seccionLibre = document.getElementById("seccionLibre");
const promoContainer = document.getElementById("promoContainer");
const inputMonto = document.getElementById("inputMonto");
const inputMetodo = document.getElementById("inputMetodo");

// Mostrar promociones
document.getElementById("btnShowPromos").addEventListener("click", () => {
    seccionPromos.style.display = "block";
    seccionLibre.style.display = "none";
});

// Mostrar libre
document.getElementById("btnShowLibre").addEventListener("click", () => {
    seccionPromos.style.display = "none";
    seccionLibre.style.display = "block";
});

// Render promos
promociones.forEach(p => {
    promoContainer.innerHTML += `
        <div class="col-6 col-md-3">
            <div class="promo-card text-center" data-monto="${p.monto}">
                <h5 class="fw-bold">S/. ${p.monto}</h5>
                <p class="text-success small">+ S/. ${p.bonus} extra</p>
            </div>
        </div>
    `;
});

// Selección de promo
document.addEventListener("click", (e) => {
    let card = e.target.closest(".promo-card");
    if (!card) return;

    document.querySelectorAll(".promo-card").forEach(c => c.classList.remove("selected"));
    card.classList.add("selected");

    inputMonto.value = card.dataset.monto;
    document.getElementById("montoLibre").value = "";
});

// Método de pago
document.querySelectorAll(".pago-opcion").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".pago-opcion").forEach(b => b.classList.remove("selected"));
        btn.classList.add("selected");

        const metodo = btn.dataset.metodo;
        inputMetodo.value = metodo;
        mostrarDatosPago(metodo);
    });
});

// Monto libre
document.getElementById("montoLibre").addEventListener("input", function () {
    inputMonto.value = this.value;
    document.querySelectorAll(".promo-card").forEach(c => c.classList.remove("selected"));
});

// Mostrar info del método seleccionado
function mostrarDatosPago(metodo) {
    const info = datosPago[metodo];
    const cont = document.getElementById("infoPago");

    let html = `
        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold text-danger text-center text-capitalize">${metodo}</h5>
            <p><strong>Nombre:</strong> ${info.nombre}</p>
            <p><strong>Número:</strong> ${info.numero}</p>
    `;

    if (info.cuenta) html += `<p><strong>Cuenta:</strong> ${info.cuenta}</p>`;
    if (info.cci) html += `<p><strong>CCI:</strong> ${info.cci}</p>`;
    if (info.qr) {
        html += `
            <div class="text-center mt-2">
                <img src="${info.qr}" width="160" class="img-fluid rounded shadow">
                <p class="mt-2 text-muted small">Escanea el QR</p>
            </div>
        `;
    }

    html += `</div>`;
    cont.innerHTML = html;
    cont.style.display = "block";
}
</script>

@endsection
