//public/js/auth.js

/* Toggle Password */
document.querySelectorAll(".toggle-pass").forEach(btn => {
    btn.addEventListener("click", () => {
        const input = btn.parentElement.querySelector("input");
        input.type = input.type === "password" ? "text" : "password";
        btn.querySelector("i").classList.toggle("fa-eye");
        btn.querySelector("i").classList.toggle("fa-eye-slash");
    });
});

/* LOGIN */
const loginForm = document.getElementById("loginForm");

if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const email = document.getElementById("loginEmail").value;
        const password = document.getElementById("loginPass").value;
        const errorBox = document.getElementById("loginError");

        errorBox.classList.add("d-none");

        try {
            const res = await fetch("/auth/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ email, password })
            });

            const data = await res.json();

            if (!res.ok) {
                errorBox.classList.remove("d-none");
                errorBox.textContent = data.message || "Error al iniciar sesión";
                return;
            }

            // UNA SOLA REDIRECCIÓN
            window.location.href = data.redirect;

        } catch (err) {
            errorBox.classList.remove("d-none");
            errorBox.textContent = "Error de conexión con el servidor";
        }
    });
}
