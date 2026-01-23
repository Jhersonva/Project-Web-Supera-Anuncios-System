@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/rechargues.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
.selector-btn {
    padding: 10px;
    font-weight: 600;
    border-radius: 10px;
    border: 2px solid #dc3545;
    transition: .3s;
}
.selector-btn.active {
    background: #dc3545;
    color: white;
}
.selector-btn:not(.active) {
    background: white;
    color: #dc3545;
}

.card-historial {
    border-radius: 15px;
    padding: 15px;
    background: #fff;
    border: 1px solid #eee;
    margin-bottom: 15px;
}
</style>

<div class="container mt-5 mb-5">

    <!-- TÍTULO -->
    <div class="text-center mb-4 mt-4">
        <h3 class="fw-bold">Recargar</h3>
        <p class="text-secondary">Aquí puedes hacer tus recargas para publicar tus anuncios.</p>
    </div>

    <!-- SELECTOR -->
    <div class="d-flex gap-2 mb-4">
        <button id="btnRecargar" class="selector-btn w-50 active">Realizar Recarga</button>
        <button id="btnHistorial" class="selector-btn w-50">Historial de Recargas</button>
    </div>

    <!-- SECCIÓN REALIZAR RECARGA -->
    <div id="seccionRecargar">

        <!-- SALDO -->
        <div class="d-flex justify-content-center mb-3">
            <div class="wallet-box d-flex align-items-center px-3 py-2 shadow-sm">
                <i class="fa-solid fa-wallet me-2 text-success" style="font-size: 1.6rem;"></i>

                <div>
                    <span class="small text-muted">Mi saldo</span>
                    <h5 class="m-0 fw-bold">S/. {{ number_format(Auth::user()->virtual_wallet, 2) }}</h5>
                </div>
            </div>
        </div>

        <!-- MONTO -->
        <div class="mt-4">
            <h5 class="fw-semibold">Ingresa un monto</h5>

            <input 
                type="number" 
                id="montoLibre" 
                class="form-control mt-2" 
                placeholder="Ingresa un monto (mínimo S/. 1)"
                min="1"
            >
        </div>

        <hr class="my-4">

        <div id="infoPago" class="mt-4" style="display:none;"></div>

        <h5 class="fw-semibold">Método de pago</h5>

        <div class="row g-3 mt-2">
            @foreach ($paymentMethods as $method)
            <div class="col-6 col-md-3">
                <div class="pago-opcion" data-metodo-id="{{ $method->id }}">
                    <img 
                        src="{{ asset($method->logo ?? 'assets/default-payment.png') }}" 
                        alt="{{ $method->logo }}"
                    >
                    <p class="mt-2">{{ $method->name_method }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- FORMULARIO -->
        <form method="POST" action="{{ route('recharges.store') }}" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="monto" id="inputMonto">
            <input type="hidden" name="payment_method_id" id="inputMetodo">

            <div class="mt-4">
                <label class="fw-bold">Sube tu comprobante</label>
                <input 
                    type="file" 
                    name="img_cap_pago" 
                    class="form-control mt-2" 
                    accept="image/*" 
                    required
                    id="imgComprobante"
                >
                <!-- Preview de la imagen -->
                <div class="mt-2">
                    <img id="previewComprobante" src="#" alt="Preview del comprobante" style="display:none; max-width:200px; border-radius:8px;">
                </div>
            </div>

            <button id="btnRecargar" class="btn btn-danger w-100 mt-4 py-2 fw-bold" type="submit">
                Enviar solicitud de recarga
            </button>
        </form>

    </div>

    <!-- ================== SECCIÓN HISTORIAL ================== -->
    <div id="seccionHistorial" style="display:none;">

        <h5 class="fw-bold mt-4 mb-3">Historial de tus recargas</h5>

        @if($recharges->isEmpty())
            <p class="text-center text-muted mt-4">No tienes recargas registradas.</p>
        @else

        @foreach ($recharges as $r)
        <div class="card-historial">

            <div class="m-0">
                <strong>Monto:</strong>
                S/. {{ number_format($r->monto, 2) }}
            </div>

            <p class="m-0">
                <strong>Método:</strong> {{ $r->paymentMethod->name_method ?? 'No definido' }}
            </p>

            <p class="m-0">
                <strong>Fecha:</strong> {{ $r->created_at->format('d/m/Y H:i') }}
            </p>

            <p class="m-0">
                <strong>Estado:</strong>
                @if($r->status === 'pendiente')
                    <span class="badge bg-warning text-dark">Pendiente</span>
                @elseif($r->status === 'aceptado')
                    <span class="badge bg-success">Aprobado</span>
                @else
                    <span class="badge bg-danger">Rechazado</span>
                @endif
            </p>

        </div>
        @endforeach

        @endif

    </div>

</div>

<script>
/* SELECTOR ENTRE SECCIONES */
const btnRecargar = document.getElementById("btnRecargar");
const btnHistorial = document.getElementById("btnHistorial");

const seccionRecargar = document.getElementById("seccionRecargar");
const seccionHistorial = document.getElementById("seccionHistorial");

btnRecargar.addEventListener("click", () => {
    btnRecargar.classList.add("active");
    btnHistorial.classList.remove("active");

    seccionRecargar.style.display = "block";
    seccionHistorial.style.display = "none";
});

btnHistorial.addEventListener("click", () => {
    btnHistorial.classList.add("active");
    btnRecargar.classList.remove("active");

    seccionRecargar.style.display = "none";
    seccionHistorial.style.display = "block";
});


/* SISTEMA DE PAGO*/
const paymentMethodsData = @json($paymentMethods);

const inputMonto = document.getElementById("inputMonto");
const inputMetodo = document.getElementById("inputMetodo");

// Monto libre
document.getElementById("montoLibre").addEventListener("input", function () {
    inputMonto.value = this.value;
});

// Selección método pago
document.querySelectorAll(".pago-opcion").forEach(btn => {
    btn.addEventListener("click", () => {

        document.querySelectorAll(".pago-opcion").forEach(b => b.classList.remove("selected"));
        btn.classList.add("selected");

        const metodoId = btn.dataset.metodoId;
        const metodo = paymentMethodsData.find(m => m.id == metodoId);

        inputMetodo.value = metodo.id;
        mostrarDatosPago(metodo);
    });
});

// Mostrar info del método seleccionado
function mostrarDatosPago(m) {

    let html = `
        <div class="card p-3 shadow-sm">

            <h5 class="fw-bold text-danger text-center">${m.name_method}</h5>

            ${m.holder_name ? `<p><strong>Titular:</strong> ${m.holder_name }</p>` : ''}
            ${m.cell_phone_number ? `<p><strong>Número:</strong> ${m.cell_phone_number }</p>` : ''}
            ${m.account_number ? `<p><strong>Cuenta:</strong> ${m.account_number }</p>` : ''}
            ${m.cci ? `<p><strong>CCI:</strong> ${m.cci}</p>` : ''}

            ${m.qr ? `
                <div class="text-center mt-2">
                    <img src="${m.qr.startsWith('http') ? m.qr : '/'+m.qr}" width="160" class="img-fluid rounded shadow">
                    <p class="mt-2 text-muted small">Escanea el QR</p>
                </div>
            ` : ''}
        </div>
    `;

    document.getElementById("infoPago").innerHTML = html;
    document.getElementById("infoPago").style.display = "block";
}

/*Preview del Comprobante de Pago*/
document.getElementById('imgComprobante').addEventListener('change', function(event) {
        const preview = document.getElementById('previewComprobante');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    });

document.addEventListener("DOMContentLoaded", function () {

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Recarga enviada!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            showConfirmButton: true
        });
    @endif


    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Verifica los datos',
            html: `
                <ul style="text-align:left;">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonText: 'Entendido'
        });
    @endif

});

</script>

@endsection
