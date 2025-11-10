// public/js/inactivity-timer.js

// 1) Páginas donde NO queremos el timer
const AUTH_EXCLUDED_PATHS = [
    '/login',
    '/register',
    '/password/reset',
    '/password/email',
    '/forgot-password',
    '/verify-email',
];

// Si la ruta actual es una de las excluidas, no hacemos nada
const currentPath = window.location.pathname;
const isExcluded = AUTH_EXCLUDED_PATHS.some(p => currentPath.startsWith(p));
if (isExcluded) {
    // Opcional: para depurar
    console.log('⏱️ Inactivity timer desactivado en esta página:', currentPath);
    // Salimos del script
    // IMPORTANTE: no seguimos creando listeners ni timers
    // porque aquí el usuario todavía no está dentro del panel
    // y podría estar logueándose.
    // eslint-disable-next-line no-unused-expressions
    null;
} else {

    // 2) Timer normal para las páginas protegidas

    // 10 segundos para pruebas 10000
    const INACTIVITY_TIMEOUT = 900000;

    let inactivityTimer;

    const handleLogout = async () => {
        console.log("Cerrando sesión por inactividad...");
        alert("Tu sesión ha expirado por inactividad.");

        try {
            // usamos la función global que dejamos en firebase.js
            if (window.firebaseSignOut) {
                await window.firebaseSignOut();
            }
        } catch (error) {
            console.error("Error al cerrar sesión de Firebase por inactividad:", error);
        } finally {
            // cerrar sesión en Laravel
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

    // iniciar contador
    resetTimer();
}
