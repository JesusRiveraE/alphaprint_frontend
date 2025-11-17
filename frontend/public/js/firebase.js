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
  off,
} from "https://www.gstatic.com/firebasejs/9.23.0/firebase-database.js";

/* -------------------------------------------------------------------------- */
/* Configuración Firebase                                                     */
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
/* Estado y utilidades internas                                              */
/* -------------------------------------------------------------------------- */
let refreshIntervalId = null;
const REFRESH_MS = 5 * 60 * 1000; // 5 minutos (Vigilante de respaldo)

// Evita doble logout en condiciones de carrera
let logoutInProgress = false;

// Útil para que otros scripts esperen a que tengamos el primer estado de sesión
let _resolveAuthReady;
const authReady = new Promise((res) => (_resolveAuthReady = res));

// Guardará la función para "des-suscribirnos" o apagar el listener de RTDB
let sessionUnsubscribe = null;

// Flag para evitar múltiples sincronizaciones simultáneas con Laravel
let loginSyncInProgress = false;

// Evita sincronizar más de una vez por UID (para que no salgan mensajes duplicados)
let lastSyncedUid = null;

/* -------------------------------------------------------------------------- */
/* Helpers                                                                    */
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
 * Cierre centralizado de sesión
 */
async function forceLogout(reason = "revoked") {
  if (logoutInProgress) return;
  logoutInProgress = true;
  clearLocalState();
  try {
    await signOut(firebaseAuth);
  } catch (_) {}

  try {
    await fetch("/logout", {
      method: "GET",
      headers: { Accept: "application/json" },
    });
  } catch (err) {
    console.warn(
      "Error al intentar desloguear de Laravel, pero continuamos...",
      err
    );
  } finally {
    try {
      window.dispatchEvent(
        new CustomEvent("alphaprint:forced-logout", { detail: { reason } })
      );
    } catch (_) {}
    if (!isOnLoginPage()) {
      const qs = new URLSearchParams({ reason }).toString();
      location.replace(`/login?${qs}`);
    }
  }
}

/* -------------------------------------------------------------------------- */
/* Token & Fetch                                                              */
/* -------------------------------------------------------------------------- */

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
 * Wrapper de fetch con Authorization: Bearer <token>
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
/* Sincronización con Laravel (/firebase/login)                               */
/* -------------------------------------------------------------------------- */

/**
 * Obtiene el token CSRF de Laravel (meta o window.Laravel)
 */
function getCsrfToken() {
  try {
    if (window.Laravel && window.Laravel.csrfToken) {
      return window.Laravel.csrfToken;
    }
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : "";
  } catch (_) {
    return "";
  }
}

/**
 * Sincroniza la sesión de Firebase con Laravel a través de /firebase/login
 * - Envía uid + email.
 * - Incluye X-CSRF-TOKEN para evitar el 419.
 * - Guarda el rol en localStorage si viene en la respuesta.
 * - Si hay error de negocio (403/422/etc.), muestra el mensaje y hace logout.
 */
async function syncLaravelSession(user) {
  if (!user) return false;
  if (loginSyncInProgress) return true; // ya hay una sincronización en curso

  loginSyncInProgress = true;

  try {
    const payload = {
      uid: user.uid,
      email: user.email,
      displayName: user.displayName || "",
    };

    const headers = {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    };

    const csrf = getCsrfToken();
    if (csrf) {
      headers["X-CSRF-TOKEN"] = csrf;
    }

    const res = await fetch("/firebase/login", {
      method: "POST",
      headers,
      body: JSON.stringify({ user: payload }),
    });

    let data = {};
    let rawText = "";

    try {
      data = await res.clone().json();
    } catch (_) {
      try {
        rawText = await res.text();
      } catch (_) {}
    }

    if (!res.ok || !data.ok) {
      // Intentamos sacar un mensaje lo más útil posible
      const backendMsg =
        data.error ||
        data.message ||
        (rawText && rawText.substring(0, 200)) || // por si devuelve HTML o texto
        "No se pudo iniciar sesión en el sistema local. Verifica tus permisos.";

      console.error(
        "Error en /firebase/login:",
        res.status,
        backendMsg,
        rawText
      );

      alert(backendMsg);

      // Para errores de permisos o autenticación, cerramos sesión
      if (res.status === 401 || res.status === 403 || res.status === 419) {
        await forceLogout("forbidden");
      }

      return false;
    }

    // Guardar rol localmente (para el frontend, ej. ocultar ADMINISTRACIÓN)
    if (data.rol) {
      try {
        localStorage.setItem("userRole", data.rol);
      } catch (_) {}
    }

    return true;
  } catch (err) {
    console.error("Error al sincronizar sesión con Laravel:", err);
    alert(
      "Ocurrió un error al conectar con el servidor. Inténtalo de nuevo en un momento."
    );
    await forceLogout("backend-error");
    return false;
  } finally {
    loginSyncInProgress = false;
  }
}

/**
 * Solo sincroniza si todavía no se ha sincronizado este UID
 */
async function syncLaravelSessionIfNeeded(user) {
  if (!user) return false;
  if (lastSyncedUid === user.uid) {
    return true;
  }
  const ok = await syncLaravelSession(user);
  if (ok) {
    lastSyncedUid = user.uid;
  }
  return ok;
}

/* -------------------------------------------------------------------------- */
/* Listeners de autenticación (CON LÓGICA DE RTDB + LOGIN LARAVEL)           */
/* -------------------------------------------------------------------------- */
onIdTokenChanged(firebaseAuth, async (user) => {
  try {
    if (user) {
      // Usuario autenticado
      startTokenAutoRefresh(); // Mantenemos el vigilante de 5 min por si falla RTDB

      window.dispatchEvent(
        new CustomEvent("alphaprint:user-signed-in", {
          detail: { uid: user.uid },
        })
      );

      if (window.startInactivityTimer) {
        window.startInactivityTimer();
      }

      // 🔰 Kill Switch en RTDB
      if (sessionUnsubscribe) sessionUnsubscribe();

      const userSessionRef = ref(database, `sesiones_revocadas/${user.uid}`);

      sessionUnsubscribe = onValue(userSessionRef, (snapshot) => {
        if (snapshot.exists() && snapshot.val() === true) {
          console.warn(
            "RTDB (Kill Switch): ¡Sesión revocada por un admin! Cerrando sesión AHORA."
          );
          forceLogout("revoked-rtdb");
        }
      });

      // 🔰 SINCRONIZAR CON LARAVEL (/firebase/login + RBAC)
      const ok = await syncLaravelSessionIfNeeded(user);

      // Si todo salió bien y estamos en /login, vamos al dashboard
      if (ok && isOnLoginPage() && !logoutInProgress) {
        location.replace("/home");
      }
    } else {
      // Sesión terminada (no hay usuario)
      stopTokenAutoRefresh();
      clearLocalState();

      if (window.stopInactivityTimer) {
        window.stopInactivityTimer();
      }

      // Apagar el listener de RTDB
      if (sessionUnsubscribe) {
        sessionUnsubscribe();
        sessionUnsubscribe = null;
      }

      if (!isOnLoginPage() && !logoutInProgress) {
        location.replace("/login?reason=signed-out");
      }
    }
  } finally {
    // Resuelve la promesa para quien esté esperando authReady
    if (_resolveAuthReady) {
      _resolveAuthReady(user);
      _resolveAuthReady = null;
    }
  }
});

// Config extra
setupVisibilityRefresh();

/* -------------------------------------------------------------------------- */
/* Exponer utilidades globales                                               */
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
/* Exports para módulos que importan este archivo                            */
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
  database,
  ref,
  onValue,
  off,
};

export async function getIdToken() {
  const user = await authReady;
  if (!user) {
    throw new Error("No hay usuario autenticado");
  }
  return user.getIdToken();
}
