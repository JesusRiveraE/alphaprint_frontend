@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1 class="m-0 page-title">
        <i class="fas fa-users-cog mr-2 brand-text"></i> Usuarios
    </h1>
@stop

@section('content')

<div class="card card-soft shadow-sm">

    <div class="card-header header-accent d-flex align-items-center justify-content-between">
        <strong class="card-title text-brand mb-0">
            <i class="fas fa-users mr-2"></i> Listado de Usuarios
        </strong>

        <div class="ml-auto">
            <a href="{{ route('usuarios.create') }}"
               id="btnCrearUsuario"
               class="btn btn-sm btn-brand-outline"
               style="display:none;">
                <i class="fas fa-user-plus mr-1"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-brand">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Activo</th>
                        <th>Fecha de Creación</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-usuarios-body">
                    <tr>
                        <td colspan="7" class="text-center">Cargando usuarios...</td>
                    </tr>
                </tbody>
            </table>
        </div>
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
    border:1px solid #f0f1f5;
    border-radius:.6rem;
}
.card-soft.shadow-sm:hover{
    box-shadow:0 0 15px rgba(226,78,96,.08);
}
.header-accent{
    border-left:4px solid var(--brand);
    background:#fff;
    padding-top:.6rem;
    padding-bottom:.6rem;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.text-brand{
    color: var(--brand);
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
.thead-brand th{
    border-bottom:2px solid var(--brand) !important;
    color:#4b5563;
    font-weight:700;
}
.badge-chip{
    background:var(--brand-100);
    color:var(--brand);
    font-weight:600;
    border-radius:999px;
    padding:.35rem .6rem;
}
.badge-active{
    background:#16a34a1a;
    color:#16a34a;
    font-weight:600;
    border-radius:999px;
    padding:.25rem .6rem;
}
.badge-inactive{
    background:#b91c1c1a;
    color:#b91c1c;
    font-weight:600;
    border-radius:999px;
    padding:.25rem .6rem;
}
</style>
@stop

@push('js')
<script type="module">
    import { authReady, authorizedFetch } from "{{ asset('js/firebase.js') }}";

    /**
     * Carga los usuarios desde la API y los dibuja en la tabla.
     */
    async function cargarUsuarios() {
        const tbody   = document.getElementById('tabla-usuarios-body');
        const userRole = localStorage.getItem('userRole') || 'Empleado';

        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Cargando usuarios...</td></tr>';

        try {
            await authReady;

            const response = await authorizedFetch('http://localhost:3000/api/usuarios');

            if (!response.ok) {
                let errMsg = 'No se pudo cargar la lista de usuarios.';
                try {
                    const errData = await response.json();
                    errMsg = errData.error || errData.details || errMsg;
                } catch (_) {}
                throw new Error(errMsg);
            }

            const usuarios = await response.json();

            if (!usuarios || usuarios.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay usuarios para mostrar.</td></tr>';
                return;
            }

            tbody.innerHTML = '';

            usuarios.forEach((item) => {
                const tr = document.createElement('tr');
                tr.id = `fila-usuario-${item.id_usuario}`;

                const estadoTexto  = item.activo ? 'Activo' : 'Inactivo';
                const estadoClase  = item.activo ? 'badge-active' : 'badge-inactive';
                const fechaCreacion = item.fecha_creacion
                    ? new Date(item.fecha_creacion).toLocaleDateString()
                    : '';

                let botonesAccion = '';
                if (userRole === 'Admin') {
                    const editUrl = `/usuarios/${item.id_usuario}/edit`;

                    botonesAccion = `
                        <a
                            href="${editUrl}"
                            class="btn btn-xs btn-outline-primary mr-1"
                            title="Editar"
                        >
                            <i class="fas fa-edit"></i>
                        </a>
                        <button
                            type="button"
                            class="btn btn-xs btn-outline-danger btn-eliminar-usuario"
                            title="Eliminar"
                            onclick="eliminarUsuario(${item.id_usuario})"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }

                tr.innerHTML = `
                    <td class="text-muted">${item.id_usuario ?? ''}</td>
                    <td><strong>${item.nombre_usuario ?? ''}</strong></td>
                    <td>${item.correo ?? ''}</td>
                    <td>
                        <span class="badge badge-chip">
                            ${item.rol || '(Sin rol)'}
                        </span>
                    </td>
                    <td>
                        <span class="${estadoClase}">
                            ${estadoTexto}
                        </span>
                    </td>
                    <td>${fechaCreacion}</td>
                    <td class="text-right">
                        ${botonesAccion}
                    </td>
                `;

                tbody.appendChild(tr);
            });

        } catch (error) {
            console.error('Error cargando usuarios:', error);
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error al cargar usuarios: ${error.message}</td></tr>`;
        }
    }

    /**
     * Eliminar usuario (DELETE a la API usando authorizedFetch).
     */
    window.eliminarUsuario = async function (idUsuario) {
        if (!idUsuario) {
            alert('ID de usuario inválido.');
            return;
        }

        if (!confirm('¿Seguro que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
            return;
        }

        const filaId = `fila-usuario-${idUsuario}`;
        const fila   = document.getElementById(filaId);
        let btn      = null;

        try {
            if (fila) {
                btn = fila.querySelector('.btn-eliminar-usuario');
                if (btn) {
                    btn.disabled = true;
                    btn.dataset._oldHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                }
            }

            await authReady;

            const response = await authorizedFetch(`http://localhost:3000/api/usuarios/${idUsuario}`, {
                method: 'DELETE',
            });

            let data = {};
            try { data = await response.json(); } catch (_) {}

            if (!response.ok) {
                const msg = data.error || data.details || `Error HTTP ${response.status}`;
                throw new Error(msg);
            }

            if (fila) fila.remove();

            alert('🗑 Usuario eliminado correctamente.');
        } catch (err) {
            console.error('Error al eliminar usuario:', err);
            alert('❌ No se pudo eliminar el usuario: ' + err.message);
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = btn.dataset._oldHtml || '<i class="fas fa-trash"></i>';
                delete btn.dataset._oldHtml;
            }
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        // Cargar tabla
        cargarUsuarios();

        // Mostrar/ocultar botón "Nuevo Usuario" según rol
        const userRole = localStorage.getItem('userRole');
        const btnCrear = document.getElementById('btnCrearUsuario');
        if (btnCrear) {
            btnCrear.style.display = (userRole === 'Admin') ? 'inline-block' : 'none';
        }
    });
</script>
@endpush
