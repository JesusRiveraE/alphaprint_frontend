// public/js/firebase.js
// ES Module (cargar con type="module")
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
import {
  getAuth,
  onIdTokenChanged,
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword,
  sendPasswordResetEmail,
  GoogleAuthProvider,
  signInWithPopup,
  signOut,
} from "https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js";

// 🔰 1. IMPORTAR FUNCIONES DE REALTIME DATABASE (RTDB)
import { 
    getDatabase, 
    ref, 
    onValue, 
    off 
} from "https://www.gstatic.com/firebasejs/9.23.0/firebase-database.js";

/* -------------------------------------------------------------------------- */
/* Configuración Firebase                                                    */
/* -------------------------------------------------------------------------- */
const firebaseConfig = {
  apiKey: "AIzaSyDiFqUxIixd0ryIaosocCKE9yTvZtQ9qkc",
  authDomain: "alphaprint-79f90.firebaseapp.com",
  projectId: "alphaprint-79f90",
  storageBucket: "alphaprint-79f90.firebasestorage.app",
  messagingSenderId: "777419710629",
  appId: "1:777419710629:web:6a8cae685794ffb4c62d4c",
};

const app = initializeApp(firebaseConfig);
const firebaseAuth = getAuth(app);
const provider = new GoogleAuthProvider();

// 🔰 2. INICIALIZAR LA INSTANCIA DE REALTIME DATABASE
const database = getDatabase(app);

/* -------------------------------------------------------------------------- */
/* Estado y utilidades internas                                              */
/* -------------------------------------------------------------------------- */
let refreshIntervalId = null;
const REFRESH_MS = 5 * 60 * 1000; // 5 minutos (Vigilante de respaldo)

// Evita doble logout en condiciones de carrera
let logoutInProgress = false;

// Útil para que otros scripts esperen a que tengamos el primer estado de sesión
let _resolveAuthReady;
const authReady = new Promise((res) => (_resolveAuthReady = res));

// 🔰 3. VARIABLE PARA GUARDAR EL LISTENER DE RTDB
// (Guardará la función para "des-suscribirnos" o apagar el listener)
let sessionUnsubscribe = null;

/* -------------------------------------------------------------------------- */
/* Helpers (SIN CAMBIOS)                                                      */
/* -------------------------------------------------------------------------- */
function isOnLoginPage() {
  const p = (location.pathname || "").toLowerCase();
  return p.includes("/login");
}

function clearLocalState() {
  try {
    localStorage.removeItem("userRole");
    localStorage.removeItem("userName");
    sessionStorage.clear();
  } catch (_) {}
}

/**
 * Cierre centralizado de sesión (SIN CAMBIOS)
 */
async function forceLogout(reason = "revoked") {
  if (logoutInProgress) return;
  logoutInProgress = true;
  clearLocalState();
  try {
    await signOut(firebaseAuth);
  } catch (_) {}
  
  try {
    await fetch('/logout', {
      method: 'GET',
      headers: { 'Accept': 'application/json' }
    });
  } catch (err) {
    console.warn("Error al intentar desloguear de Laravel, pero continuamos...", err);
  }
  finally {
    try {
      window.dispatchEvent(new CustomEvent("alphaprint:forced-logout", { detail: { reason } }));
    } catch (_) {}
    if (!isOnLoginPage()) {
      const qs = new URLSearchParams({ reason }).toString();
      location.replace(`/login?${qs}`);
    }
  }
}

// ... (Las funciones getCurrentIdToken, startTokenAutoRefresh, stopTokenAutoRefresh, 
// ... setupVisibilityRefresh y authorizedFetch se quedan EXACTAMENTE IGUAL) ...

/** Obtiene el ID token actual (opcionalmente forzando refresh desde el servidor) */
async function getCurrentIdToken(forceRefresh = false) {
  const user = firebaseAuth.currentUser;
  if (!user) return null;
  try {
    return await user.getIdToken(!!forceRefresh);
  } catch (err) {
    await forceLogout("token-error");
    return null;
  }
}

/** Inicia/renueva el intervalo que fuerza refresh periódico del token */
function startTokenAutoRefresh() {
  stopTokenAutoRefresh();
  refreshIntervalId = setInterval(async () => {
    const user = firebaseAuth.currentUser;
    if (user) {
      try {
        await user.getIdToken(true);
      } catch (_) {
        await forceLogout("token-refresh-failed");
      }
    }
  }, REFRESH_MS);
}

function stopTokenAutoRefresh() {
  if (refreshIntervalId) {
    clearInterval(refreshIntervalId);
    refreshIntervalId = null;
  }
}

/** Escucha visibilidad de pestaña para refrescar inmediato al volver al foco */
function setupVisibilityRefresh() {
  document.addEventListener("visibilitychange", async () => {
    if (document.visibilityState === "visible") {
      const user = firebaseAuth.currentUser;
      if (user) {
        try {
          await user.getIdToken(true);
        } catch (_) {
          await forceLogout("token-refresh-visibility");
        }
      }
    }
  });
}

/**
 * Wrapper de fetch (SIN CAMBIOS)
 */
async function authorizedFetch(input, init = {}) {
  const opts = { ...init, headers: new Headers(init.headers || {}) };
  const token = await getCurrentIdToken(false);
  if (!token) {
    await forceLogout("no-user");
    throw new Error("No authenticated user.");
  }
  opts.headers.set("Authorization", `Bearer ${token}`);
  if (!opts.cache) opts.cache = "no-store";
  const resp = await fetch(input, opts);
  if (resp.status === 401) {
    let payload = {};
    try {
      payload = await resp.clone().json();
    } catch (_) {}
    const msg = (payload?.error || "").toString().toLowerCase();
    const revoked = payload?.revoked === true;
    if (
      revoked ||
      msg.includes("revocado") ||
      msg.includes("revoked") ||
      msg.includes("deshabilitada") ||
      msg.includes("disabled") ||
      msg.includes("inválido") ||
      msg.includes("invalid")
    ) {
      await forceLogout(revoked ? "revoked" : "unauthorized");
      throw new Error("Session revoked/unauthorized.");
    }
  }
  return resp;
}


/* -------------------------------------------------------------------------- */
/* Listeners de autenticación (CON LÓGICA DE RTDB)                            */
/* -------------------------------------------------------------------------- */
onIdTokenChanged(firebaseAuth, async (user) => {
  try {
    if (user) {
      // Usuario autenticado
      startTokenAutoRefresh(); // Mantenemos el vigilante de 5 min por si falla RTDB
      window.dispatchEvent(new CustomEvent("alphaprint:user-signed-in", { detail: { uid: user.uid } }));
      if (window.startInactivityTimer) {
        window.startInactivityTimer();
      }

      // 🔰 4. INICIAR EL LISTENER INSTANTÁNEO DE RTDB
      // Limpiamos cualquier listener anterior (por si acaso)
      if (sessionUnsubscribe) sessionUnsubscribe();

      // Esta es la ruta en la RTDB que vamos a escuchar
      const userSessionRef = ref(database, `sesiones_revocadas/${user.uid}`);
      
      // onValue() se suscribe a cambios en esa ruta EN TIEMPO REAL
      sessionUnsubscribe = onValue(userSessionRef, (snapshot) => {
          // Si la ruta existe y su valor es 'true'
          if (snapshot.exists() && snapshot.val() === true) {
              console.warn("RTDB (Kill Switch): ¡Sesión revocada por un admin! Cerrando sesión AHORA.");
              // Llama a tu función de logout. 
              // forceLogout() ya tiene un 'if (logoutInProgress)' para evitar loops.
              forceLogout("revoked-rtdb");
          }
      });

    } else {
      // Sesión terminada (no hay usuario)
      stopTokenAutoRefresh();
      clearLocalState();
      if (window.stopInactivityTimer) {
        window.stopInactivityTimer();
      }

      // 🔰 5. APAGAR EL LISTENER DE RTDB
      // Si el usuario cerró sesión, ya no necesitamos escuchar.
      if (sessionUnsubscribe) {
          sessionUnsubscribe(); // Llama a la función 'off()'
          sessionUnsubscribe = null;
      }

      if (!isOnLoginPage() && !logoutInProgress) {
        location.replace("/login?reason=signed-out");
      }
    }
  } finally {
    // Resuelve la promesa
    if (_resolveAuthReady) {
      _resolveAuthReady(user); 
      _resolveAuthReady = null;
    }
  }
});

// Config extra
setupVisibilityRefresh();

/* -------------------------------------------------------------------------- */
/* Exponer utilidades globales (SIN CAMBIOS)                                 */
/* -------------------------------------------------------------------------- */
window.firebaseAuth = firebaseAuth;
window.firebaseProvider = provider;
window.authorizedFetch = authorizedFetch;
window.handleForcedLogout = forceLogout;
window.firebaseSignOut = async () => {
  await forceLogout("manual");
};
window.firebaseGetToken = getCurrentIdToken;
window.authReady = authReady;

/* -------------------------------------------------------------------------- */
/* Exports para módulos que importan este archivo                            */
/* -------------------------------------------------------------------------- */
export {
  firebaseAuth,
  provider,
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword,
  sendPasswordResetEmail,
  signInWithPopup,
  signOut,
  authorizedFetch,
  getCurrentIdToken,
  authReady,
  // 🔰 6. EXPORTAR LAS NUEVAS UTILIDADES (opcional pero buena práctica)
  database,
  ref,
  onValue,
  off
};

export async function getIdToken() {
  const user = await authReady;      // ya lo usas en authorizedFetch
  if (!user) {
    throw new Error('No hay usuario autenticado');
  }
  return user.getIdToken();
}