document.addEventListener("DOMContentLoaded", () => {

    const pendingAction  = localStorage.getItem("pending_action");
    const pendingPayload = localStorage.getItem("pending_payload");

    // Si no hay acción → nada
    if (!pendingAction) return;

    // IS_AUTHENTICATED debe existir
    if (!window.IS_AUTHENTICATED) return;

    const data = pendingPayload ? JSON.parse(pendingPayload) : {};

    switch (pendingAction) {

        case "ver":
            window.location.href = data.url;
            break;

        case "whatsapp":
            abrirWhatsapp(data.numero, data.titulo);
            break;

        case "llamar":
            realizarLlamada(data.numero);
            break;

        case "contact":
            window.location.href = `/contact/${data.adId}`;
            break;
    }

    // Limpiar
    localStorage.removeItem("pending_action");
    localStorage.removeItem("pending_payload");
});