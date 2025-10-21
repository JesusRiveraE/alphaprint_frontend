@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', 'Iniciar Sesión')

@section('auth_body')
<form id="loginForm" method="POST" action="javascript:void(0);">
    <div class="input-group mb-3">
        <input type="email" id="email" class="form-control" placeholder="Correo electrónico" required>
        {{-- ... (código del input sin cambios) ... --}}
    </div>

    <div class="input-group mb-3">
        <input type="password" id="password" class="form-control" placeholder="Contraseña" required>
        {{-- ... (código del input sin cambios) ... --}}
    </div>

    {{-- 🔰 CAMBIO 1: Botón de Ingresar ahora ocupa todo el ancho --}}
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
        </div>
        {{-- El botón de registrarse se ha eliminado de aquí --}}
    </div>
</form>

<p class="mb-1 mt-3">
    <a href="#" id="forgotPassword">Olvidé mi contraseña</a>
</p>
@stop

{{-- ... (auth_footer sin cambios) ... --}}

@push('js')
<script type="module">
    // PASO 1: Importar las funciones que necesitamos del SDK de Firebase
    import { signInWithEmailAndPassword, sendPasswordResetEmail } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js";
    
    // PASO 2: Importar nuestra configuración de Firebase desde el archivo que creamos
    import { firebaseAuth } from "{{ asset('js/firebase.js') }}";

    // PASO 3: Lógica de la página (ahora se ejecuta después de que todo se ha importado)
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('loginForm');
        const forgot = document.getElementById('forgotPassword');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const pass = document.getElementById('password').value;

            try {
                // Ahora las variables 'signInWithEmailAndPassword' y 'firebaseAuth' existen
                // gracias a los 'import' de arriba y están listas para usar.
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
                alert("Error al iniciar sesión: " + err.message);
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