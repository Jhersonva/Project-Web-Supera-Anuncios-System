// js/admin-reload-request.js

// Obtener solicitudes (solo 1 por ahora)
let solicitudes = JSON.parse(localStorage.getItem("recargas")) || [];
const contenedor = document.getElementById("contenedorRecargas");

renderLista();

function renderLista() {
    if (solicitudes.length === 0) {
        contenedor.innerHTML = `
            <div class="empty">
                <i class="fa-solid fa-inbox fa-3x"></i>
                <p class="mt-3">No hay solicitudes de recarga.</p>
            </div>
        `;
        return;
    }

    contenedor.innerHTML = "";

    solicitudes.forEach(sol => {
        contenedor.innerHTML += `
            <div class="card-recarga mt-3">
                <h5 class="fw-bold text-danger">Monto: S/. ${sol.monto}</h5>
                <p><strong>Usuario:</strong> ${sol.usuarioEmail}</p>
                <p><strong>Método de pago:</strong> ${sol.pago}</p>
                <p><strong>Promoción:</strong> ${sol.promo ? "Sí" : "No"}</p>

                <div class="mt-2 mb-2">
                    <span class="estado estado-${sol.estado}">
                        ${sol.estado.toUpperCase()}
                    </span>
                </div>

                ${
                    sol.estado === "pendiente"
                    ? `<button class="btn-accion btn-aceptar" onclick="aceptar(${sol.id})">Aceptar</button>
                       <button class="btn-accion btn-rechazar" onclick="rechazar(${sol.id})">Rechazar</button>`
                    : ""
                }
            </div>
        `;
    });
}

function aceptar(id) {
    let solicitudes = JSON.parse(localStorage.getItem("recargas")) || [];
    let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

    const solicitud = solicitudes.find(s => s.id === id);
    solicitud.estado = "aceptado";

    // Buscar usuario dueño de la solicitud
    let user = usuarios.find(u => u.email === solicitud.usuarioEmail);
    if (user) {
        user.saldo = (user.saldo || 0) + solicitud.monto;
    }

    localStorage.setItem("usuarios", JSON.stringify(usuarios));
    localStorage.setItem("recargas", JSON.stringify(solicitudes));

    alert("Recarga acreditada al usuario.");
    renderLista();
}

function rechazar(id) {
    let solicitudes = JSON.parse(localStorage.getItem("recargas")) || [];
    const solicitud = solicitudes.find(s => s.id === id);

    solicitud.estado = "rechazado";

    localStorage.setItem("recargas", JSON.stringify(solicitudes));

    alert("La solicitud fue rechazada.");
    renderLista();
}
