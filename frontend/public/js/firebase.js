// public/js/firebase.js
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
import { 
    getAuth, 
    signInWithEmailAndPassword, 
    createUserWithEmailAndPassword, 
    sendPasswordResetEmail, 
    GoogleAuthProvider, 
    signInWithPopup,
    signOut, 
} from "https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js";

const firebaseConfig = {
    apiKey: "AIzaSyDiFqUxIixd0ryIaosocCKE9yTvZtQ9qkc",
    authDomain: "alphaprint-79f90.firebaseapp.com",
    projectId: "alphaprint-79f90",
    storageBucket: "alphaprint-79f90.firebasestorage.app",
    messagingSenderId: "777419710629",
    appId: "1:777419710629:web:6a8cae685794ffb4c62d4c",
};

// Inicializar Firebase
const app = initializeApp(firebaseConfig);
const firebaseAuth = getAuth(app);
const provider = new GoogleAuthProvider();

window.firebaseAuth = firebaseAuth;

window.firebaseSignOut = async () => {
    await signOut(firebaseAuth);
};

export { firebaseAuth, signInWithEmailAndPassword, createUserWithEmailAndPassword, sendPasswordResetEmail, signInWithPopup, provider, signOut, };
