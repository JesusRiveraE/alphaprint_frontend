@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom-login.css') }}">
@endpush

@section('auth_header', 'Iniciar Sesión')

@section('auth_body')
<form id="loginForm" method="POST" action="javascript:void(0);">
    
    {{-- 🔰 CAMBIO: Añadido el grupo completo para el email --}}
    <div class="input-group mb-3">
        <input type="email" id="email" class="form-control" placeholder="Correo electrónico" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span> {{-- Ícono del sobre --}}
            </div>
        </div>
    </div>

    {{-- Grupo de Contraseña con el ojito (como lo dejamos antes) --}}
    <div class="input-group mb-3">
        <input type="password" id="password" class="form-control" placeholder="Contraseña" required>
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-left: 0; border-color: #ced4da;">
                <span id="toggleIcon" class="fas fa-eye"></span>
            </button>
        </div>
    </div>

    {{-- Botón de Ingresar (sin cambios) --}}
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
        </div>
    </div>
</form>

<p class="mb-1 mt-3">
    <a href="#" id="forgotPassword">Olvidé mi contraseña</a>
</p>
@stop

{{-- ... (auth_footer sin cambios) ... --}}

@push('js')
<!-- Cargar primero firebase.js -->
<script type="module" src="{{ asset('js/firebase.js') }}"></script>

<script type="module">
    // PASO 1: Importar las funciones que necesitamos del SDK de Firebase
    import { signInWithEmailAndPassword, sendPasswordResetEmail } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js";
    
    // PASO 2: Usar la instancia global de Firebase inicializada desde firebase.js
    const firebaseAuth = window.firebaseAuth;

    // PASO 3: Lógica de la página
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('loginForm');
        const forgot = document.getElementById('forgotPassword');
        const passwordInput = document.getElementById('password'); // 🔰 Definimos el input aquí para reusarlo

        // 🔰🔰 INICIO: Lógica para mostrar/ocultar contraseña 🔰🔰
        const togglePassword = document.getElementById('togglePassword');
        const toggleIcon = document.getElementById('toggleIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                // Cambia el tipo del input
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Cambia el ícono del ojo
                if (type === 'text') {
                    // Ojo tachado
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    // Ojo normal
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
            });
        }
        // 🔰🔰 FIN: Lógica para mostrar/ocultar contraseña 🔰🔰

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const pass = passwordInput.value; // 🔰 Usamos la variable que ya definimos

            try {
                // ... (el resto de tu lógica de login de Firebase) ...
                
                const { user } = await signInWithEmailAndPassword(firebaseAuth, email, pass);
                const token = await user.getIdToken();

                const syncResponse = await fetch("http://localhost:3000/auth/sync", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ token }),
                });

                const syncData = await syncResponse.json();
                if (!syncResponse.ok) {
                    throw new Error(syncData.error || 'Error de sincronización.');
                }
                
                localStorage.setItem('userRole', syncData.rol);

                await fetch("{{ route('firebase.login') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({ user: { email: user.email, uid: user.uid } }),
                });

                window.location.href = "{{ url('home') }}";

            } catch (err) {
                console.error("Error detallado:", err); 

                let mensajeParaElUsuario = "Ocurrió un error inesperado. Intenta de nuevo.";

                // "Traducimos" el código de error de Firebase
                switch (err.code) {
                    case 'auth/user-not-found':
                    case 'auth/wrong-password':
                    case 'auth/invalid-credential':
                    case 'auth/invalid-login-credentials':
                        mensajeParaElUsuario = "Correo o contraseña incorrectos. Por favor, verifique sus datos.";
                        break;
                    case 'auth/invalid-email':
                        mensajeParaElUsuario = "El formato del correo electrónico no es válido.";
                        break;
                    case 'auth/too-many-requests':
                        mensajeParaElUsuario = "Detectamos demasiados intentos fallidos. Su cuenta ha sido bloqueada temporalmente. Intente de nuevo más tarde.";
                        break; 
                    case 'auth/user-disabled':
                        mensajeParaElUsuario = "Esta cuenta ha sido deshabilitada por un administrador.";
                        break;
                    case 'auth/network-request-failed':
                        mensajeParaElUsuario = "Error de red. Revisa tu conexión a internet.";
                        break;
                }

                alert(mensajeParaElUsuario);
            }
        });

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
    });
</script>
@endpush
