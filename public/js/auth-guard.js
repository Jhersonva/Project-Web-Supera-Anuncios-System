function requireLogin(action, payload = null) {

    // Guardar lo que intentó hacer
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
