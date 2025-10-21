// Importa las funciones que necesitas de los SDKs de Firebase
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js";

// Tu configuración personal de Firebase
const firebaseConfig = {
  apiKey: "AIzaSyDiFqUxlixdOryIaococCKE9yTvZtQ9qkc",
  authDomain: "alphaprint-79f90.firebaseapp.com",
  projectId: "alphaprint-79f90",
  storageBucket: "alphaprint-79f90.appspot.com",
  messagingSenderId: "777419710629",
  appId: "1:777419710629:web:6a8cae685794ffb4c62d4c"
};

// Inicializa la aplicación de Firebase
const app = initializeApp(firebaseConfig);

// Exporta el servicio de autenticación para que otros archivos lo puedan importar
export const firebaseAuth = getAuth(app);