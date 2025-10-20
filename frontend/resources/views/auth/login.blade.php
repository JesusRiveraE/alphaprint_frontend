@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', 'Iniciar Sesión')

@section('auth_body')
<form id="loginForm" method="POST" action="javascript:void(0);">
    <div class="input-group mb-3">
        <input type="email" id="email" class="form-control" placeholder="Correo electrónico" required>
        <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
        </div>
    </div>

    <div class="input-group mb-3">
        <input type="password" id="password" class="form-control" placeholder="Contraseña" required>
        <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
        </div>
        <div class="col-6">
            <button type="button" id="registerBtn" class="btn btn-success btn-block">Registrarse</button>
        </div>
    </div>
</form>

<p class="mb-1 mt-3">
    <a href="#" id="forgotPassword">Olvidé mi contraseña</a>
</p>
@stop

@section('auth_footer')
<p class="text-center text-muted">© {{ date('Y') }} - Tu Empresa</p>
@stop

@push('js')
<script type="module" src="{{ asset('js/firebase.js') }}"></script>
<script type="module">
import { 
    signInWithEmailAndPassword, 
    createUserWithEmailAndPassword, 
    sendPasswordResetEmail 
} from "https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js";
import { firebaseAuth } from "/js/firebase.js";

const form = document.getElementById('loginForm');
const registerBtn = document.getElementById('registerBtn');
const forgot = document.getElementById('forgotPassword');

/**
 * 🔹 Guarda sesión en Laravel (ya lo haces)
 * 🔹 Y sincroniza el usuario con MySQL vía Node.js
 */
async function saveSession(user) {
    const userData = { email: user.email, uid: user.uid };
    
    // 1️⃣ Guardar sesión en Laravel (como ya tenías)
    await fetch("{{ route('firebase.login') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: JSON.stringify({ user: userData }),
    });

    // 2️⃣ Obtener token del usuario desde Firebase
    const token = await user.getIdToken();

    // 3️⃣ Enviar token al backend Node.js para registrarlo en MySQL
    await fetch("http://localhost:3000/auth/sync", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ token }),
});
}

/** ==========================
 * EVENTOS DEL LOGIN
 * ========================== **/

// 🔹 Login con email y contraseña
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const pass = document.getElementById('password').value;
    try {
        const { user } = await signInWithEmailAndPassword(firebaseAuth, email, pass);
        await saveSession(user);
        window.location.href = "{{ url('home') }}";
    } catch (err) {
        alert(err.message);
    }
});

// 🔹 Registro de nuevo usuario
registerBtn.addEventListener('click', async () => {
    const email = document.getElementById('email').value;
    const pass = document.getElementById('password').value;
    try {
        const { user } = await createUserWithEmailAndPassword(firebaseAuth, email, pass);
        await saveSession(user);
        alert("Usuario registrado correctamente");
        window.location.href = "{{ url('home') }}";
    } catch (err) {
        alert(err.message);
    }
});

// 🔹 Recuperar contraseña
forgot.addEventListener('click', async () => {
    const email = document.getElementById('email').value;
    if (!email) return alert("Ingresa tu correo");
    try {
        await sendPasswordResetEmail(firebaseAuth, email);
        alert("Correo de recuperación enviado");
    } catch (err) {
        alert(err.message);
    }
});
</script>
@endpush

