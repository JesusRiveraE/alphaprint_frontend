// Se elimina 'createUserWithEmailAndPassword' porque el backend se encargará de eso.
import { auth, signInWithEmailAndPassword, signInWithPopup, provider } from "./firebase.js";

document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form"); 
    const googleBtn = document.getElementById("google-login");

    // 🔹 Lógica de REGISTRO (Corregida)
    // Ahora solo envía los datos al backend para que él haga todo.
    if (registerForm) {
        registerForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            
            // Asume que tienes campos distintos para el registro
            const email = document.getElementById("email-register").value;
            const password = document.getElementById("password-register").value;
            // Podrías tener un campo para el nombre o generarlo automáticamente
            const nombreUsuario = document.getElementById("nombre-register")?.value || email.split('@')[0];
            const rol = "Empleado"; // Rol por defecto para nuevos registros

            try {
                // ÚNICO PASO: Llamar a tu endpoint del backend.
                // Él se encargará de crear el usuario en Firebase y en MySQL.
                const response = await fetch("http://localhost:3000/auth/register", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        nombreUsuario: nombreUsuario,
                        rol: rol
                    })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    // Muestra el error que viene del backend (ej: "email-already-exists")
                    throw new Error(data.details || "Error al registrar en el servidor.");
                }

                alert("✅ ¡Usuario registrado exitosamente!\nAhora puedes iniciar sesión.");
                // Opcional: limpiar el formulario o redirigir a login si está en otra página.
                window.location.reload(); // Recarga la página para que el usuario pueda iniciar sesión.

            } catch (err) {
                alert("❌ Error al registrar: " + err.message);
                console.error(err);
            }
        });
    }

    // 🔹 Lógica de LOGIN (Sin cambios, ya era correcta)
    if (loginForm) {
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const email = document.getElementById("email-login").value;
            const password = document.getElementById("password-login").value;

            try {
                // Inicia sesión en Firebase para obtener el token
                const userCredential = await signInWithEmailAndPassword(auth, email, password);
                const user = userCredential.user;
                const token = await user.getIdToken();

                // Envía el token al backend para verificar el estado en MySQL (activo/inactivo)
                const syncResponse = await fetch("http://localhost:3000/auth/sync", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ token })
                });

                if (!syncResponse.ok) {
                    const errorData = await syncResponse.json();
                    throw new Error(errorData.error || 'Error de sincronización con el servidor.');
                }

                alert("Inicio de sesión exitoso");
                window.location.href = "/dashboard";

            } catch (err) {
                alert("Error: " + err.message);
            }
        });
    }

    // 🔹 Login con Google (No requiere cambios en su lógica)
    if (googleBtn) {
        googleBtn.addEventListener("click", async () => {
            try {
                const result = await signInWithPopup(auth, provider);
                const user = result.user;
                const token = await user.getIdToken();

                // Importante: También debes tener una lógica en el backend para manejar
                // el primer login con Google y registrar al usuario en MySQL si no existe.
                // La ruta /sync actual solo valida, no crea.
                const syncResponse = await fetch("http://localhost:3000/auth/sync", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ token })
                });

                if (!syncResponse.ok) {
                    const errorData = await syncResponse.json();
                    // Si el usuario no existe en tu DB, aquí podrías llamar a una ruta de registro
                    if (syncResponse.status === 404) {
                        alert("Usuario no registrado en la base de datos. Contacta al administrador.");
                        // O podrías tener una ruta '/auth/sync-google' que sí cree el usuario.
                        return;
                    }
                    throw new Error(errorData.error || 'Error de sincronización con el servidor.');
                }
                
                alert("Inicio de sesión con Google exitoso");
                window.location.href = "/dashboard";
            } catch (err) {
                alert("Error con Google: " + err.message);
            }
        });
    }
});