// public/js/logout.js

import { firebaseAuth } from './firebase.js';
import { signOut } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js";

document.addEventListener('DOMContentLoaded', () => {
    // El botón de logout de AdminLTE suele estar dentro de un formulario.
    // Buscamos ese formulario para interceptar su envío.
    const logoutForm = document.querySelector('form[action$="/logout"]');

    if (logoutForm) {
        logoutForm.addEventListener('submit', async (e) => {
            // 1. Detenemos el envío del formulario temporalmente
            e.preventDefault();

            try {
                // 2. Cerramos la sesión en Firebase
                await signOut(firebaseAuth);
                console.log('Sesión cerrada en Firebase exitosamente.');

            } catch (error) {
                console.error('Error al cerrar la sesión de Firebase:', error);
            
            } finally {
                // 3. Pase lo que pase, continuamos con el logout de Laravel
                // enviando el formulario original.
                logoutForm.submit();
            }
        });
    }
});