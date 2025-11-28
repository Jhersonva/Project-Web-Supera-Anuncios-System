//admin-solicitudes.js

// CARGA DE SOLICITUDES SIMULADAS
const solicitudes = JSON.parse(localStorage.getItem("solicitudes")) || [];

// RENDERIZAR SOLICITUDES

function renderSolicitudes() {
    const contenedor = document.getElementById("listaSolicitudes");
    contenedor.innerHTML = "";

    solicitudes.forEach(sol => {
        contenedor.innerHTML += `
        <div class="solicitud-card shadow-sm">
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="${sol.usuario.avatar}" class="avatar">
                    <div>
                        <strong>${sol.usuario.nombre}</strong><br>
                        <small>${sol.fecha}</small>
                    </div>
                </div>
                <span class="estado">${sol.estado}</span>
            </div>

            <img src="${sol.imagen}" class="solicitud-img">

            <h6 class="mt-2">${sol.titulo}</h6>
            <p class="descripcion">${sol.descripcion}</p>

            <div class="acciones d-flex gap-2 mt-2">
                <button class="btn-aprobar" onclick="aprobar(${sol.id})">
                    <i class="fa-solid fa-check"></i> Aprobar
                </button>
                <button class="btn-rechazar" onclick="rechazar(${sol.id})">
                    <i class="fa-solid fa-xmark"></i> Rechazar
                </button>
            </div>
        </div>
        `;
    });
}

renderSolicitudes();

// ACCIONES

function aprobar(id) {
    let solicitudes = JSON.parse(localStorage.getItem("solicitudes")) || [];
    let anunciosPublicados = JSON.parse(localStorage.getItem("anunciosPublicados")) || [];
    let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

    const index = solicitudes.findIndex(s => s.id === id);
    if (index === -1) return;

    const solicitud = solicitudes[index];

    // Cambiar estado
    solicitud.estado = "aprobado";

    // Guardar en anuncios publicados
    anunciosPublicados.push(solicitud);
    localStorage.setItem("anunciosPublicados", JSON.stringify(anunciosPublicados));

    // Remover de solicitudes
    solicitudes.splice(index, 1);
    localStorage.setItem("solicitudes", JSON.stringify(solicitudes));

    alert("✔ Publicación aprobada");
    location.reload();
}

function rechazar(id) {
    let solicitudes = JSON.parse(localStorage.getItem("solicitudes")) || [];
    let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

    const index = solicitudes.findIndex(s => s.id === id);
    if (index === -1) return;

    const solicitud = solicitudes[index];

    // Devolver dinero
    const uIndex = usuarios.findIndex(u => u.email === solicitud.usuario);
    if (uIndex !== -1) {
        usuarios[uIndex].saldo += solicitud.costo;
        localStorage.setItem("usuarios", JSON.stringify(usuarios));
    }

    solicitudes.splice(index, 1);
    localStorage.setItem("solicitudes", JSON.stringify(solicitudes));

    alert("❌ Publicación rechazada y saldo devuelto");
    location.reload();
}



// NAV USUARIO (HEADER)

document.addEventListener("DOMContentLoaded", () => {
    const nav = document.getElementById("userNav");
    if (!nav) return;

    const usuario = JSON.parse(localStorage.getItem("usuario"));

    if (usuario) {
        nav.innerHTML = `
            <i class="fa-solid fa-circle-user"></i> ${usuario.nombre}
            <button class="btn btn-danger btn-sm" onclick="cerrarSesion()">Salir</button>
        `;
    } else {
        nav.innerHTML = `
            <button class="btn btn-primary btn-sm" onclick="location.href='login.html'">Ingresar</button>
        `;
    }
});

function cerrarSesion() {
    localStorage.removeItem("usuario");
    window.location.href = "login.html";
}
