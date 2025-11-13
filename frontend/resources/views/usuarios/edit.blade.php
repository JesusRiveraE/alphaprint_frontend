@extends('adminlte::page')

@section('title','Editar Usuario')

@section('content_header')
<h1 class="m-0 page-title">
    <i class="fas fa-user-edit mr-2 brand-text"></i> Editar Usuario
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex align-items-center justify-content-between">
        <strong class="brand-text mb-0">
            <i class="fas fa-id-badge mr-2"></i> Actualizar Datos
        </strong>

        <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-brand-outline">
            <i class="fas fa-list mr-1"></i> Ver listado
        </a>
    </div>

    <div class="card-body">
        {{-- Para mostrar errores si quieres usarlos después desde Laravel --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <hr>
        @endif

        {{-- ID del usuario para que JS sepa qué buscar en la API --}}
        <input type="hidden" id="usuario-id" value="{{ $usuarioId ?? '' }}">

        <form id="formEditarUsuario">
            <div class="form-row">
                {{-- NOMBRE DE USUARIO --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Nombre de usuario</label>
                    <input
                        type="text"
                        id="nombre-actualizar"
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
                        id="email-actualizar"
                        class="form-control"
                        required
                    >
                </div>

                {{-- NUEVA CONTRASEÑA (OPCIONAL) --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Nueva contraseña (opcional)</label>
                    <input
                        type="password"
                        id="password-actualizar"
                        class="form-control"
                        minlength="6"
                        placeholder="Dejar en blanco para no cambiar"
                    >
                    <small class="form-text text-muted">
                        Debe tener al menos 6 caracteres si la cambias.
                    </small>
                </div>

                {{-- ROL --}}
                <div class="form-group col-md-3">
                    <label class="label-brand">Rol</label>
                    <select id="rol-actualizar" class="form-control custom-select" required>
                        <option value="Empleado">Empleado</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>

                {{-- ESTADO --}}
                <div class="form-group col-md-3">
                    <label class="label-brand">Estado</label>
                    <select id="activo-actualizar" class="form-control custom-select" required>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-secondary mr-2">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-sm btn-brand">
                    <i class="fas fa-save mr-1"></i> Guardar Cambios
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
.btn-brand-outline{
    border:1px solid var(--brand);
    color:var(--brand);
    background:#fff;
    font-weight:600;
}
.btn-brand-outline:hover{
    background:var(--brand-100);
    color:var(--brand);
}
</style>
@stop

@push('js')
<script type="module">
    import { authReady, authorizedFetch } from "{{ asset('js/firebase.js') }}";

    async function cargarUsuarioEnFormulario() {
        const idInput = document.getElementById('usuario-id');
        if (!idInput || !idInput.value) {
            console.error('No se proporcionó usuarioId a la vista.');
            return;
        }

        const usuarioId = idInput.value;

        const nombreInput   = document.getElementById('nombre-actualizar');
        const emailInput    = document.getElementById('email-actualizar');
        const rolSelect     = document.getElementById('rol-actualizar');
        const activoSelect  = document.getElementById('activo-actualizar');

        try {
            await authReady;

            // Como no sabemos si tienes GET /api/usuarios/:id,
            // reutilizamos GET /api/usuarios y filtramos.
            const response = await authorizedFetch('http://localhost:3000/api/usuarios');
            const data     = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'No se pudo obtener la lista de usuarios.');
            }

            const usuarios = Array.isArray(data) ? data : [];
            const usuario  = usuarios.find(u => String(u.id_usuario) === String(usuarioId));

            if (!usuario) {
                alert('No se encontró el usuario seleccionado.');
                window.location.href = "{{ route('usuarios.index') }}";
                return;
            }

            // Rellenar campos
            if (nombreInput)  nombreInput.value  = usuario.nombre_usuario ?? '';
            if (emailInput)   emailInput.value   = usuario.correo ?? usuario.email ?? '';
            if (rolSelect)    rolSelect.value    = usuario.rol ?? 'Empleado';
            if (activoSelect) activoSelect.value = usuario.activo ? '1' : '0';

        } catch (err) {
            console.error('Error cargando datos del usuario:', err);
            alert('❌ Error al cargar el usuario:\n\n' + err.message);
            window.location.href = "{{ route('usuarios.index') }}";
        }
    }

    async function enviarActualizacion() {
        const idInput = document.getElementById('usuario-id');
        if (!idInput || !idInput.value) return;
        const usuarioId = idInput.value;

        const nombreInput    = document.getElementById('nombre-actualizar');
        const emailInput     = document.getElementById('email-actualizar');
        const passwordInput  = document.getElementById('password-actualizar');
        const rolSelect      = document.getElementById('rol-actualizar');
        const activoSelect   = document.getElementById('activo-actualizar');

        const updateData = {
            // 👇 Misma estructura que tenías en el modal
            nombre_usuario: nombreInput.value.trim(),
            email:          emailInput.value.trim(),
            rol:            rolSelect.value,
            activo:         parseInt(activoSelect.value, 10),
        };

        const password = passwordInput.value;
        if (password) {
            updateData.password = password;
        }

        try {
            await authReady;

            const response = await authorizedFetch(`http://localhost:3000/api/usuarios/${usuarioId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(updateData),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                let msg = data.msg || data.error || 'Error al actualizar el usuario.';
                if (data.errores && Array.isArray(data.errores)) {
                    msg += '\n\nDetalles:\n' + data.errores.map(e => `• ${e.msg}`).join('\n');
                }
                throw new Error(msg);
            }

            alert('✅ Usuario actualizado con éxito.');
            window.location.href = "{{ route('usuarios.index') }}";

        } catch (err) {
            console.error('Error actualizando usuario:', err);
            alert('❌ Error al actualizar el usuario:\n\n' + err.message);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        cargarUsuarioEnFormulario();

        const form = document.getElementById('formEditarUsuario');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                enviarActualizacion();
            });
        }
    });
</script>
@endpush
