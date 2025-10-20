// resources/js/login.js
import { auth, signInWithEmailAndPassword, signInWithPopup, provider } from "./firebase.js";

document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    const googleBtn = document.getElementById("google-login");

    // 🔹 Login con correo y contraseña
    if (loginForm) {
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            try {
                const userCredential = await signInWithEmailAndPassword(auth, email, password);
                const user = userCredential.user;
                const token = await user.getIdToken();

                await fetch("http://localhost:3000/auth/sync", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ token })
                });

                alert("Inicio de sesión exitoso");
                window.location.href = "/dashboard";
            } catch (err) {
                alert("Error: " + err.message);
            }
        });
    }

    // 🔹 Login con Google
    if (googleBtn) {
        googleBtn.addEventListener("click", async () => {
            try {
                const result = await signInWithPopup(auth, provider);
                const user = result.user;
                const token = await user.getIdToken();

                await fetch("http://localhost:3000/auth/sync", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ token })
                });

                alert("Inicio de sesión con Google exitoso");
                window.location.href = "/dashboard";
            } catch (err) {
                alert("Error con Google: " + err.message);
            }
        });
    }
});
