// public/js/inactivity-timer.js

/* -------------------------------------------------------------------------- */
/*  Páginas excluidas (no activar timer)                                       */
/* -------------------------------------------------------------------------- */
const AUTH_EXCLUDED_PATHS = [
  '/login',
  '/register',
  '/password/reset',
  '/password/email',
  '/forgot-password',
  '/verify-email',
];

const currentPath = (window.location.pathname || '').toLowerCase();
const isExcluded = AUTH_EXCLUDED_PATHS.some(p => currentPath.startsWith(p));

if (isExcluded) {
  // Útil para depurar
  // console.log('⏱️ Timer de inactividad desactivado en:', currentPath);
} else {
  /* ------------------------------------------------------------------------ */
  /*  Configuración                                                           */
  /* ------------------------------------------------------------------------ */
  // 15 minutos (en ms). Para pruebas cortas puedes bajarlo (p. ej., 10_000).
  const INACTIVITY_TIMEOUT = 100000;

  let inactivityTimer = null;
  let listenersAttached = false;
  let destroyed = false;

  /* ------------------------------------------------------------------------ */
  /*  Utilidades                                                              */
  /* ------------------------------------------------------------------------ */
  const clearTimer = () => {
    if (inactivityTimer) {
      clearTimeout(inactivityTimer);
      inactivityTimer = null;
    }
  };

  const destroy = () => {
    if (destroyed) return;
    destroyed = true;
    clearTimer();
    if (listenersAttached) {
      activityEvents.forEach(ev => document.removeEventListener(ev, resetTimer, true));
      document.removeEventListener('visibilitychange', onVisibility);
      window.removeEventListener('alphaprint:forced-logout', onForcedLogout);
      listenersAttached = false;
    }
  };

  const fallbackLogout = async (reason = 'idle') => {
    try {
      if (typeof window.handleForcedLogout === 'function') {
        await window.handleForcedLogout(reason);
      } else if (typeof window.firebaseSignOut === 'function') {
        await window.firebaseSignOut();
        location.replace(`/login?reason=${encodeURIComponent(reason)}`);
      } else {
        location.replace(`/login?reason=${encodeURIComponent(reason)}`);
      }
    } catch (_) {
      location.replace(`/login?reason=${encodeURIComponent(reason)}`);
    }
  };

  const handleLogout = async () => {
    alert("Tu sesión ha expirado por inactividad. Serás redirigido al login.");
    // console.log('⏱️ Cerrando sesión por inactividad…');
    await fallbackLogout('idle');
  };

  const resetTimer = () => {
    clearTimer();
    inactivityTimer = setTimeout(handleLogout, INACTIVITY_TIMEOUT);
  };

  const onVisibility = () => {
    if (document.visibilityState === 'visible') {
      // Al volver al foco, resetea el contador
      resetTimer();
    }
  };

  const onForcedLogout = () => {
    // Si otra causa (revocado/manual) cerró sesión, limpiaremos todo aquí
    destroy();
  };

  const activityEvents = ['mousemove', 'mousedown', 'keypress', 'scroll', 'touchstart'];

/* ------------------------------------------------------------------------ */
  /*  Arranque                                                                */
  /* ------------------------------------------------------------------------ */
  // ... en inactivity-timer.js ...

  // Hacemos que la función 'destroy' sea la que detiene todo.
window.stopInactivityTimer = () => {
  console.log('⏱️ Timer: Recibida orden de DETENER.');
  destroy(); // 'destroy' ya limpia los timers y listeners
};

// Esta es la función que arrancará todo
window.startInactivityTimer = () => {
  // No arrancar si ya está corriendo o si fue destruido
  if (listenersAttached || destroyed) return; 

  console.log('⏱️ Timer: Recibida orden de INICIAR.');
  try {
    // Listeners de actividad
    activityEvents.forEach(ev => {
      document.addEventListener(ev, resetTimer, true);
    });
    document.addEventListener('visibilitychange', onVisibility);
    
    // ❗️ IMPORTANTE: 'onForcedLogout' ahora es manejado por 'destroy'
    // así que lo renombramos a 'stopInactivityTimer'
    window.addEventListener('alphaprint:forced-logout', window.stopInactivityTimer);

    listenersAttached = true;

    // Inicia el timer
    resetTimer();
    console.log('⏱️ Timer: ¡Iniciado! Esperando inactividad.');
  } catch (e) {
    console.error('⏱️ Timer: Error en el arranque:', e);
  }
};
}
