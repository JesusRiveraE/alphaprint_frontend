// Ya no necesitamos la primera línea de 'import'

// El resto del código es casi igual
import { signOut } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js";


// 900000 segundos para pruebas

const INACTIVITY_TIMEOUT = 900000; // 15 segundos para pruebas

let inactivityTimer;

const handleLogout = async () => {
    console.log("Cerrando sesión por inactividad...");
    alert("Tu sesión ha expirado por inactividad.");

    try {
        // 🔰 CAMBIO: Usamos la variable global 'window.firebaseAuth'
        await signOut(window.firebaseAuth);
    } catch (error) {
        console.error("Error al cerrar sesión de Firebase por inactividad:", error);
    } finally {
        window.location.href = '/logout'; 
    }
};

const resetTimer = () => {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(handleLogout, INACTIVITY_TIMEOUT);
};

const activityEvents = [
    'mousemove', 'mousedown', 'keypress', 'scroll', 'touchstart'
];

activityEvents.forEach(eventName => {
    document.addEventListener(eventName, resetTimer, true);
});

resetTimer();