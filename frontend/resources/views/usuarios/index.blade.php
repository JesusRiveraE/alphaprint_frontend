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

        <div class="ml-auto d-flex align-items-center">
            {{-- Botón buscar --}}
            <button type="button"
                    class="btn btn-sm btn-brand-outline mr-2"
                    data-toggle="modal"
                    data-target="#modalBusquedaUsuarios">
                <i class="fas fa-search mr-1"></i> Buscar usuarios
            </button>

            {{-- Botón crear (controlado por rol en JS) --}}
            <a href="{{ route('usuarios.create') }}"
               id="btnCrearUsuario"
               class="btn btn-sm btn-brand-outline"
               style="display:none;">
                <i class="fas fa-user-plus mr-1"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    <div class="card-body">

        {{-- Selector de filas por página --}}
        <div class="d-flex justify-content-start align-items-center mb-3">
            <label class="mr-2 font-weight-bold">Mostrar:</label>

            <select id="selectFilasUsuarios"
                    class="form-control form-control-sm"
                    style="width: 90px;">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="9999">Todos</option>
            </select>

            <span class="ml-2 text-muted">usuarios por página</span>
        </div>

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

        {{-- Paginación frontend --}}
        <div class="d-flex justify-content-between align-items-center mt-3" id="paginacion-usuarios-container">
            <small class="text-muted" id="usuariosInfo"></small>
            <div>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary mr-1"
                        id="usuariosPrev"
                        disabled>
                    Anterior
                </button>
                <button type="button"
                        class="btn btn-sm btn-brand"
                        id="usuariosNext"
                        disabled>
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BÚSQUEDA USUARIOS --}}
<div class="modal fade" id="modalBusquedaUsuarios" tabindex="-1" role="dialog" aria-labelledby="modalBusquedaUsuariosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formBusquedaUsuarios">
                <div class="modal-header">
                    <h5 class="modal-title brand-text" id="modalBusquedaUsuariosLabel">
                        <i class="fas fa-search mr-1"></i> Buscar usuarios
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="u_bus_id">ID</label>
                            <input type="number" class="form-control" id="u_bus_id" placeholder="Ej: 3">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="u_bus_usuario">Usuario</label>
                            <input type="text" class="form-control" id="u_bus_usuario"
                                   placeholder="Nombre de usuario">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="u_bus_correo">Correo</label>
                            <input type="text" class="form-control" id="u_bus_correo"
                                   placeholder="Texto en el correo">
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="form-group col-md-4">
                            <label for="u_bus_rol">Rol</label>
                            <select class="form-control" id="u_bus_rol">
                                <option value="">Todos</option>
                                <option value="Admin">Admin</option>
                                <option value="Empleado">Empleado</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="u_bus_activo">Activo</label>
                            <select class="form-control" id="u_bus_activo">
                                <option value="">Todos</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="u_bus_fecha">Fecha de creación (texto)</label>
                            <input type="text" class="form-control" id="u_bus_fecha"
                                   placeholder="Ej: 13/11/2025 o 2025-11">
                            <small class="form-text text-muted">
                                Se busca dentro del texto de la fecha.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            id="u_btn_limpiar">
                        Limpiar filtros
                    </button>
                    <button type="submit" class="btn btn-brand">
                        <i class="fas fa-filter mr-1"></i> Aplicar búsqueda
                    </button>
                </div>
            </form>
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

/* Botones */
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
.btn-brand{
    background: var(--brand);
    border-color: var(--brand);
    color:#fff;
}
.btn-brand:hover{
    background:#c23c4e;
    border-color:#c23c4e;
    color:#fff;
}

/* Tabla */
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

/* Modal búsqueda usuarios */
#modalBusquedaUsuarios .modal-content{
    border-radius:.6rem;
}
#modalBusquedaUsuarios .modal-header{
    border-bottom:1px solid #f3f4f6;
}
#modalBusquedaUsuarios .modal-footer{
    border-top:1px solid #f3f4f6;
}
#modalBusquedaUsuarios label{
    font-size:.85rem;
    color:#6b7280;
    font-weight:600;
}
#modalBusquedaUsuarios .form-control{
    font-size:.9rem;
}
#modalBusquedaUsuarios .form-text{
    font-size:.75rem;
    color:#9ca3af;
}
</style>
@stop

@push('js')
<script type="module">
    import { authReady, authorizedFetch } from "{{ asset('js/firebase.js') }}";

    // Variables para paginación/búsqueda
    let filasOriginal = [];
    let filasFiltradas = [];
    let filasPorPagina = 10;
    let paginaActual = 1;

    const getTbody = () => document.getElementById('tabla-usuarios-body');
    const info      = () => document.getElementById('usuariosInfo');
    const btnPrev   = () => document.getElementById('usuariosPrev');
    const btnNext   = () => document.getElementById('usuariosNext');
    const selector  = () => document.getElementById('selectFilasUsuarios');

    function inicializarPaginacionYBusquedaUsuarios(){
        const tbody = getTbody();
        if (!tbody) return;

        filasOriginal = Array.from(tbody.querySelectorAll('tr'));
        filasFiltradas = filasOriginal.slice();
        paginaActual = 1;
        renderUsuariosPaginados();
    }

    function renderUsuariosPaginados(){
        const tbodyEl = getTbody();
        const infoEl  = info();
        const prevEl  = btnPrev();
        const nextEl  = btnNext();

        if (!tbodyEl || !infoEl || !prevEl || !nextEl) return;

        const total = filasFiltradas.length;
        const paginas = Math.max(1, Math.ceil(total / filasPorPagina));

        // Ocultar todas
        filasOriginal.forEach(f => f.style.display = 'none');

        if (total === 0){
            infoEl.textContent = '0 resultados';
            prevEl.disabled = true;
            nextEl.disabled = true;
            return;
        }

        if (paginaActual > paginas) paginaActual = paginas;

        const inicio = (paginaActual - 1) * filasPorPagina;
        const fin = inicio + filasPorPagina;

        filasFiltradas.slice(inicio, fin).forEach(f => f.style.display = '');

        infoEl.textContent = `Mostrando ${inicio + 1} - ${Math.min(fin, total)} de ${total} usuarios (página ${paginaActual} de ${paginas})`;

        prevEl.disabled = (paginaActual <= 1);
        nextEl.disabled = (paginaActual >= paginas);
    }

    function aplicarFiltrosUsuarios(){
        const tbody = getTbody();
        if (!tbody) return;

        const filas = filasOriginal.length ? filasOriginal : Array.from(tbody.querySelectorAll('tr'));

        const idVal    = (document.getElementById('u_bus_id')?.value || '').trim();
        const userVal  = (document.getElementById('u_bus_usuario')?.value || '').trim().toLowerCase();
        const mailVal  = (document.getElementById('u_bus_correo')?.value || '').trim().toLowerCase();
        const rolVal   = (document.getElementById('u_bus_rol')?.value || '').trim();
        const actVal   = (document.getElementById('u_bus_activo')?.value || '').trim();
        const fechaVal = (document.getElementById('u_bus_fecha')?.value || '').trim().toLowerCase();

        filasFiltradas = [];

        filas.forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length < 7) return;

            const idTxt   = celdas[0].textContent.trim();
            const userTxt = celdas[1].textContent.trim().toLowerCase();
            const mailTxt = celdas[2].textContent.trim().toLowerCase();

            const badgeRol  = celdas[3].querySelector('.badge-chip');
            const rolTxt    = badgeRol ? badgeRol.textContent.trim() : '';

            const badgeAct  = celdas[4].querySelector('span');
            const actTxt    = badgeAct ? badgeAct.textContent.trim() : '';

            const fechaTxt  = celdas[5].textContent.trim().toLowerCase();

            let ok = true;

            if (idVal && idTxt !== idVal) ok = false;
            if (ok && userVal && !userTxt.includes(userVal)) ok = false;
            if (ok && mailVal && !mailTxt.includes(mailVal)) ok = false;
            if (ok && rolVal && rolTxt !== rolVal) ok = false;
            if (ok && actVal && actTxt !== actVal) ok = false;
            if (ok && fechaVal && !fechaTxt.includes(fechaVal)) ok = false;

            if (ok) filasFiltradas.push(fila);
        });

        paginaActual = 1;
        renderUsuariosPaginados();
    }

    /**
     * Carga los usuarios desde la API y los dibuja en la tabla.
     */
    async function cargarUsuarios() {
        const tbody   = getTbody();
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
                inicializarPaginacionYBusquedaUsuarios();
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

            // Inicializar paginación + filtros
            inicializarPaginacionYBusquedaUsuarios();

        } catch (error) {
            console.error('Error cargando usuarios:', error);
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error al cargar usuarios: ${error.message}</td></tr>`;
            inicializarPaginacionYBusquedaUsuarios();
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

            // Recalcular paginación
            inicializarPaginacionYBusquedaUsuarios();
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

        // Eventos de paginación
        const sel = selector();
        if (sel) {
            sel.addEventListener('change', () => {
                filasPorPagina = parseInt(sel.value);
                paginaActual = 1;
                renderUsuariosPaginados();
            });
        }

        const prev = btnPrev();
        const next = btnNext();
        if (prev) {
            prev.addEventListener('click', () => {
                if (paginaActual > 1) {
                    paginaActual--;
                    renderUsuariosPaginados();
                }
            });
        }
        if (next) {
            next.addEventListener('click', () => {
                paginaActual++;
                renderUsuariosPaginados();
            });
        }

        // Búsqueda
        const form = document.getElementById('formBusquedaUsuarios');
        const btnLimpiar = document.getElementById('u_btn_limpiar');

        if (form) {
            form.addEventListener('submit', e => {
                e.preventDefault();
                aplicarFiltrosUsuarios();
                if (window.$) {
                    $('#modalBusquedaUsuarios').modal('hide');
                }
            });
        }

        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', e => {
                e.preventDefault();
                if (form) form.reset();
                filasFiltradas = filasOriginal.slice();
                paginaActual = 1;
                renderUsuariosPaginados();
            });
        }
    });
</script>
@endpush
