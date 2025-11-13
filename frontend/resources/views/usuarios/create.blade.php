@extends('adminlte::page')

@section('title','Nuevo Usuario')

@section('content_header')
<h1 class="m-0 page-title">
    <i class="fas fa-user-plus mr-2 brand-text"></i> Nuevo Usuario
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex align-items-center">
        <strong class="brand-text mb-0">
            <i class="fas fa-id-badge mr-2"></i> Datos del Usuario
        </strong>
    </div>

    <div class="card-body">
        <form id="formCrearUsuario">
            <div class="form-row">
                {{-- NOMBRE DE USUARIO --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Nombre de usuario</label>
                    <input
                        type="text"
                        id="nombre-crear"
                        class="form-control"
                        minlength="4"
                        maxlength="30"
                        required
                    >
                    <small class="form-text text-muted">
                        Debe tener entre 4 y 30 caracteres.
                    </small>
                </div>

                {{-- CORREO --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Correo electrónico</label>
                    <input
                        type="email"
                        id="email-crear"
                        class="form-control"
                        required
                    >
                </div>

                {{-- CONTRASEÑA --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Contraseña</label>
                    <input
                        type="password"
                        id="password-crear"
                        class="form-control"
                        minlength="6"
                        required
                    >
                    <small class="form-text text-muted">
                        Debe tener al menos 6 caracteres.
                    </small>
                </div>

                {{-- ROL --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Rol</label>
                    <select id="rol-crear" class="form-control custom-select" required>
                        <option value="Empleado">Empleado</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-secondary mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
                <button type="submit" class="btn btn-sm btn-brand">
                    <i class="fas fa-save mr-1"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
:root{
    --brand:#e24e60;
    --brand-100:#fde5e9;
    --brand-600:#cc4656;
    --ink:#2b2f33;
}
.page-title{
    font-size:1.7rem;
    color:#1f2937;
}
.brand-text{
    color: var(--brand);
}
.card-soft{
    border:1px solid #eff1f5;
    border-radius:.6rem;
}
.card-soft.shadow-sm:hover{
    box-shadow:0 0 15px rgba(226,78,96,.08);
}
.border-brand{
    border-left:4px solid var(--brand);
    background:#fff;
}
.label-brand{
    font-weight:600;
    color:#4b5563;
}
.btn-brand{
    background:var(--brand);
    border-color:var(--brand);
    color:#fff;
    font-weight:600;
}
.btn-brand:hover{
    background:var(--brand-600);
    border-color:var(--brand-600);
}
</style>
@stop

@push('js')
<script type="module">
    import { authReady, authorizedFetch } from "{{ asset('js/firebase.js') }}";

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('formCrearUsuario');

        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const nombreUsuario = document.getElementById('nombre-crear').value.trim();
            const email         = document.getElementById('email-crear').value.trim();
            const password      = document.getElementById('password-crear').value;
            const rol           = document.getElementById('rol-crear').value;

            if (!nombreUsuario || !email || !password || !rol) {
                alert('Completa todos los campos requeridos.');
                return;
            }

            try {
                await authReady;

                const response = await authorizedFetch('http://localhost:3000/api/usuarios', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    // 👇 Misma forma que usabas antes en el modal
                    body: JSON.stringify({ nombreUsuario, email, password, rol }),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    let msg = data.msg || data.error || 'Error al crear el usuario.';
                    if (data.errores && Array.isArray(data.errores)) {
                        msg += '\n\nDetalles:\n' + data.errores.map(e => `• ${e.msg}`).join('\n');
                    }
                    throw new Error(msg);
                }

                alert('✅ ¡Usuario creado exitosamente!');
                window.location.href = "{{ route('usuarios.index') }}";
            } catch (err) {
                console.error('Error creando usuario:', err);
                alert('❌ Error al crear el usuario:\n\n' + err.message);
            }
        });
    });
</script>
@endpush
