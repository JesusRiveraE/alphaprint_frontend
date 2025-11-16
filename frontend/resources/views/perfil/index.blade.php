@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content_header')
    <h1 class="m-0 text-center page-title">
        <i class="fas fa-user-circle mr-2 brand-text"></i> Mi Perfil
    </h1>
@stop

@section('content')
@php
    // Datos que vienen desde Laravel (Firebase / sesión)
    $emailFb   = $usuario['email'] ?? 'Sin correo';
    $nombreFb  = $usuario['displayName'] ?? 'No disponible';
@endphp

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        {{-- Contenedor con el email como data-atributo para usarlo en JS --}}
        <div id="perfil-wrapper"
             class="card card-soft shadow-sm"
             data-email="{{ $emailFb }}">

            <div class="card-header header-accent text-center">
                <h3 class="card-title mb-0 text-brand">
                    Información del Usuario
                </h3>
            </div>

            <div class="card-body text-center">

                {{-- Icono --}}
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-6x brand-text"></i>
                </div>

                {{-- Datos (los llenamos y luego JS los actualizará si encuentra el usuario en la DB) --}}
                <div class="perfil-datos text-left d-inline-block">
                    <p>
                        <strong>ID Usuario:</strong>
                        <span id="perfil-id">—</span>
                    </p>

                    <p>
                        <strong>Usuario:</strong>
                        <span id="perfil-nombre">{{ $nombreFb }}</span>
                    </p>

                    <p>
                        <strong>Email:</strong>
                        <span id="perfil-correo">{{ $emailFb }}</span>
                    </p>

                    <p>
                        <strong>Rol:</strong>
                        <span class="badge badge-rol" id="perfil-rol">(Sin rol)</span>
                    </p>
                </div>

                <hr>

                <h5 class="text-brand mb-2">Sesión</h5>
                <p class="mb-0">
                   @php
    $inicioSesion = session('session_started_at');
@endphp

Sesión iniciada:
<strong>
    {{ $inicioSesion
        ? \Carbon\Carbon::parse($inicioSesion)->timezone('America/Tegucigalpa')->format('d/m/Y h:i A')
        : '—'
    }}
</strong>

                </p>

            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
:root{
    --brand:#e24e60;
    --brand-100:#fde5e9;
    --ink:#2b2f33;
}

.page-title{
    font-size:1.7rem;
    color:#1f2937;
}
.brand-text{
    color: var(--brand);
}

/* Card y header con marca */
.card-soft{
    border:1px solid #f0f1f5;
    border-radius:.75rem;
}
.card-soft.shadow-sm:hover{
    box-shadow:0 0 18px rgba(226,78,96,.08);
}
.header-accent{
    border-left:4px solid var(--brand);
    background:#fff;
    padding:.7rem 1rem;
}
.text-brand{
    color:var(--brand);
}

/* Bloque de datos centrado pero texto alineado a la izquierda */
.perfil-datos{
    text-align:left;
    max-width:380px;
}

/* Badge de rol */
.badge-rol{
    background:var(--brand-100);
    color:var(--brand);
    font-weight:600;
    border-radius:999px;
    padding:.3rem .7rem;
    font-size:.9rem;
}
</style>
@stop

@push('js')
<script type="module">
    import { authReady, authorizedFetch } from "{{ asset('js/firebase.js') }}";

    document.addEventListener('DOMContentLoaded', async () => {
        const wrapper   = document.getElementById('perfil-wrapper');
        if (!wrapper) return;

        const email     = wrapper.dataset.email || '';
        const idEl      = document.getElementById('perfil-id');
        const nombreEl  = document.getElementById('perfil-nombre');
        const correoEl  = document.getElementById('perfil-correo');
        const rolEl     = document.getElementById('perfil-rol');

        // Si no tenemos email, no podemos mapear contra la API
        if (!email) return;

        try {
            await authReady;

            const res = await authorizedFetch('http://localhost:3000/api/usuarios');
            if (!res.ok) {
                console.error('No se pudo obtener usuarios para el perfil');
                return;
            }

            const usuarios = await res.json();
            if (!Array.isArray(usuarios)) return;

            // Buscar usuario cuyo correo coincida con el email de la sesión
            const user = usuarios.find(u => u.correo === email);

            if (!user) {
                console.warn('No se encontró usuario en la API para el correo', email);
                return;
            }

            // Actualizar datos del perfil en pantalla
            if (idEl)     idEl.textContent     = user.id_usuario ?? '—';
            if (nombreEl) nombreEl.textContent = user.nombre_usuario ?? nombreEl.textContent;
            if (correoEl) correoEl.textContent = user.correo ?? correoEl.textContent;
            if (rolEl)    rolEl.textContent    = user.rol || '(Sin rol)';

        } catch (err) {
            console.error('Error cargando datos de perfil:', err);
        }
    });
</script>
@endpush
