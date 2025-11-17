@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    <h1 class="m-0 page-title">
        <i class="fas fa-user-tie mr-2 brand-text"></i> Empleados
    </h1>
@stop

@section('content')

{{-- Mensaje de éxito al crear / actualizar / eliminar --}}
@if(request()->has('success'))
    <div class="alert alert-success mb-3">
        {{ request('success') === 'created'
            ? 'Empleado creado con éxito'
            : (request('success') === 'updated'
                ? 'Empleado actualizado con éxito'
                : (request('success') === 'deleted'
                    ? 'Empleado eliminado con éxito'
                    : 'Operación realizada correctamente')) }}
    </div>
@endif

<div class="card card-soft shadow-sm">
    
    <div class="card-header header-accent d-flex align-items-center justify-content-between">
        <strong class="card-title text-brand mb-0">
            <i class="fas fa-user-tie mr-2"></i> Listado de Empleados
        </strong>

        <div class="ml-auto d-flex align-items-center">
            {{-- 🔍 Botón buscar empleados --}}
            <button type="button"
                    class="btn btn-sm btn-brand-outline mr-2"
                    data-toggle="modal"
                    data-target="#modalBusquedaEmpleados">
                <i class="fas fa-search mr-1"></i> Buscar empleados
            </button>

            {{-- ➕ Botón nuevo empleado --}}
            <a
                id="btnCrearEmpleado"
                href="{{ route('empleados.create') }}"
                class="btn btn-sm btn-brand-outline"
                style="display: none;">
                <i class="fas fa-user-plus mr-1"></i> Nuevo Empleado
            </a>
        </div>
    </div>

    <div class="card-body">

        {{-- Selector de filas por página --}}
        <div class="d-flex justify-content-start align-items-center mb-3">
            <label class="mr-2 font-weight-bold">Mostrar:</label>

            <select id="selectFilasEmpleados"
                    class="form-control form-control-sm"
                    style="width: 90px;">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="9999">Todos</option>
            </select>

            <span class="ml-2 text-muted">empleados por página</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-brand">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Área</th>
                        <th>Usuario Asociado</th>
                        <th>Rol</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-empleados-body">
                    <tr>
                        <td colspan="7" class="text-center">Cargando empleados...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Paginación frontend --}}
        <div class="d-flex justify-content-between align-items-center mt-3" id="paginacion-empleados-container">
            <small class="text-muted" id="empleadosInfo"></small>
            <div>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary mr-1"
                        id="empleadosPrev"
                        disabled>
                    Anterior
                </button>
                <button type="button"
                        class="btn btn-sm btn-brand"
                        id="empleadosNext"
                        disabled>
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BÚSQUEDA EMPLEADOS --}}
<div class="modal fade" id="modalBusquedaEmpleados" tabindex="-1" role="dialog" aria-labelledby="modalBusquedaEmpleadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formBusquedaEmpleados">
                <div class="modal-header">
                    <h5 class="modal-title brand-text" id="modalBusquedaEmpleadosLabel">
                        <i class="fas fa-search mr-1"></i> Buscar empleados
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="e_bus_id">ID</label>
                            <input type="number" class="form-control" id="e_bus_id" placeholder="Ej: 3">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="e_bus_nombre">Nombre</label>
                            <input type="text" class="form-control" id="e_bus_nombre"
                                   placeholder="Nombre o parte del nombre">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="e_bus_telefono">Teléfono</label>
                            <input type="text" class="form-control" id="e_bus_telefono"
                                   placeholder="Texto en el teléfono">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="e_bus_area">Área</label>
                            <input type="text" class="form-control" id="e_bus_area"
                                   placeholder="Ej: Diseño, Ventas">
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="form-group col-md-4">
                            <label for="e_bus_usuario">Usuario asociado</label>
                            <input type="text" class="form-control" id="e_bus_usuario"
                                   placeholder="Nombre de usuario">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="e_bus_rol">Rol</label>
                            <input type="text" class="form-control" id="e_bus_rol"
                                   placeholder="Ej: Admin, Empleado">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            id="e_btn_limpiar">
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

{{-- MODAL CONFIRMACIÓN ELIMINACIÓN EMPLEADO --}}
<div class="modal fade" id="modalConfirmDeleteEmpleado" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-confirm-alpha">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title brand-text">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Confirmar eliminación
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">
                <div class="icon-circle mb-3">
                    <i class="fas fa-user-slash"></i>
                </div>

                <p class="mb-1">
                    ¿Seguro que deseas eliminar al empleado
                    <strong><span id="modal-emp-nombre"></span></strong>?
                </p>
                <p class="mb-2">
                    ID: <strong>#<span id="modal-emp-id"></span></strong>
                </p>
                <p class="text-muted small mb-0">
                    Esta acción es permanente y no podrás recuperarlo después.
                </p>
            </div>

            <div class="modal-footer border-0 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger-brand" id="btnConfirmDeleteEmpleado">
                    <i class="fas fa-trash mr-1"></i> Eliminar
                </button>
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
    --brand-600:#cc4656;
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

/* Modal búsqueda empleados */
#modalBusquedaEmpleados .modal-content{
    border-radius:.6rem;
}
#modalBusquedaEmpleados .modal-header{
    border-bottom:1px solid #f3f4f6;
}
#modalBusquedaEmpleados .modal-footer{
    border-top:1px solid #f3f4f6;
}
#modalBusquedaEmpleados label{
    font-size:.85rem;
    color:#6b7280;
    font-weight:600;
}
#modalBusquedaEmpleados .form-control{
    font-size:.9rem;
}
#modalBusquedaEmpleados .form-text{
    font-size:.75rem;
    color:#9ca3af;
}

/* Modal confirmación eliminación */
.modal-confirm-alpha{
    border-radius:.8rem;
    overflow:hidden;
    box-shadow:0 15px 35px rgba(15,23,42,0.2);
}
.modal-confirm-alpha .modal-body{
    padding-top:1rem;
    padding-bottom:1.25rem;
}
.icon-circle{
    width:72px;
    height:72px;
    border-radius:50%;
    background:var(--brand-100);
    color:var(--brand);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.8rem;
    margin:0 auto;
}
.btn-danger-brand{
    background:#e24e60;
    border-color:#e24e60;
    color:#fff;
    font-weight:600;
    border-radius:.5rem;
    padding:.45rem 1.2rem;
    transition:
        background-color .2s ease,
        box-shadow .15s ease,
        transform .15s ease;
}
.btn-danger-brand:hover{
    background:#c23c4e;
    border-color:#c23c4e;
    color:#fff;
    box-shadow:0 6px 14px rgba(226,78,96,0.35);
    transform:translateY(-1px);
}
</style>
@stop

@push('js')
<script type="module">
import { authReady, authorizedFetch } from "{{ asset('js/firebase.js') }}";

// Paginación / búsqueda
let filasOriginal = [];
let filasFiltradas = [];
let filasPorPagina = 10;
let paginaActual = 1;

// Eliminación
let empleadoAEliminarId = null;
let btnEliminarActual   = null;

const getTbody   = () => document.getElementById('tabla-empleados-body');
const info       = () => document.getElementById('empleadosInfo');
const btnPrev    = () => document.getElementById('empleadosPrev');
const btnNext    = () => document.getElementById('empleadosNext');
const selector   = () => document.getElementById('selectFilasEmpleados');

/* ===========================
   PAGINACIÓN
=========================== */
function inicializarPaginacionYBusquedaEmpleados(){
    const tbody = getTbody();
    if (!tbody) return;

    filasOriginal  = Array.from(tbody.querySelectorAll('tr'));
    filasFiltradas = filasOriginal.slice();
    paginaActual   = 1;
    renderEmpleadosPaginados();
}

function renderEmpleadosPaginados(){
    const tbodyEl = getTbody();
    const infoEl  = info();
    const prevEl  = btnPrev();
    const nextEl  = btnNext();

    if (!tbodyEl || !infoEl || !prevEl || !nextEl) return;

    const total   = filasFiltradas.length;
    const paginas = Math.max(1, Math.ceil(total / filasPorPagina));

    filasOriginal.forEach(f => f.style.display = 'none');

    if (total === 0){
        infoEl.textContent = '0 resultados';
        prevEl.disabled = true;
        nextEl.disabled = true;
        return;
    }

    if (paginaActual > paginas) paginaActual = paginas;

    const inicio = (paginaActual - 1) * filasPorPagina;
    const fin    = inicio + filasPorPagina;

    filasFiltradas.slice(inicio, fin).forEach(f => f.style.display = '');

    infoEl.textContent = `Mostrando ${inicio + 1} - ${Math.min(fin, total)} de ${total} empleados (página ${paginaActual} de ${paginas})`;

    prevEl.disabled = (paginaActual <= 1);
    nextEl.disabled = (paginaActual >= paginas);
}

/* ===========================
   FILTROS
=========================== */
function aplicarFiltrosEmpleados(){
    const tbody = getTbody();
    if (!tbody) return;

    const filas = filasOriginal.length ? filasOriginal : Array.from(tbody.querySelectorAll('tr'));

    const idVal      = (document.getElementById('e_bus_id')?.value || '').trim();
    const nombreVal  = (document.getElementById('e_bus_nombre')?.value || '').trim().toLowerCase();
    const telVal     = (document.getElementById('e_bus_telefono')?.value || '').trim().toLowerCase();
    const areaVal    = (document.getElementById('e_bus_area')?.value || '').trim().toLowerCase();
    const usuarioVal = (document.getElementById('e_bus_usuario')?.value || '').trim().toLowerCase();
    const rolVal     = (document.getElementById('e_bus_rol')?.value || '').trim().toLowerCase();

    filasFiltradas = [];

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length < 7) return;

        const idTxt      = celdas[0].textContent.trim();
        const nombreTxt  = celdas[1].textContent.trim().toLowerCase();
        const telTxt     = celdas[2].textContent.trim().toLowerCase();
        const areaTxt    = celdas[3].textContent.trim().toLowerCase();
        const usuarioTxt = celdas[4].textContent.trim().toLowerCase();
        const rolBadge   = celdas[5].querySelector('.badge-chip');
        const rolTxt     = rolBadge ? rolBadge.textContent.trim().toLowerCase() : '';

        let ok = true;

        if (idVal && idTxt !== idVal) ok = false;
        if (ok && nombreVal && !nombreTxt.includes(nombreVal)) ok = false;
        if (ok && telVal && !telTxt.includes(telVal)) ok = false;
        if (ok && areaVal && !areaTxt.includes(areaVal)) ok = false;
        if (ok && usuarioVal && !usuarioTxt.includes(usuarioVal)) ok = false;
        if (ok && rolVal && !rolTxt.includes(rolVal)) ok = false;

        if (ok) filasFiltradas.push(fila);
    });

    paginaActual = 1;
    renderEmpleadosPaginados();
}

/* ===========================
   CARGAR EMPLEADOS (API)
=========================== */
async function cargarEmpleados() {
    const tbody   = getTbody();
    const userRole = localStorage.getItem('userRole') || 'Empleado';

    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="7" class="text-center">Cargando empleados...</td></tr>';

    try {
        await authReady;

        const response = await authorizedFetch('http://localhost:3000/api/empleados');

        if (!response.ok) {
            let errMsg = 'No se pudo cargar la lista de empleados.';
            try {
                const errData = await response.json();
                errMsg = errData.error || errData.details || errMsg;
            } catch (_) {}
            throw new Error(errMsg);
        }

        const empleados = await response.json();

        if (!empleados || empleados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay empleados para mostrar.</td></tr>';
            inicializarPaginacionYBusquedaEmpleados();
            return;
        }

        tbody.innerHTML = '';

        empleados.forEach((item) => {
            const tr = document.createElement('tr');
            tr.id = `fila-empleado-${item.id_personal}`;

            let botonesAccion = '';
            if (userRole === 'Admin') {
                const editUrl = `/empleados/${item.id_personal}/edit`;

                botonesAccion = `
                    <a
                        href="${editUrl}"
                        class="btn btn-xs btn-outline-primary mr-1"
                        title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button
                        type="button"
                        class="btn btn-xs btn-outline-danger btn-eliminar-empleado"
                        title="Eliminar"
                        data-id="${item.id_personal}"
                        data-nombre="${item.nombre ?? ''}">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
            }

            tr.innerHTML = `
                <td class="text-muted">${item.id_personal ?? ''}</td>
                <td><strong>${item.nombre ?? ''}</strong></td>
                <td>${item.telefono || ''}</td>
                <td>${item.area ?? ''}</td>
                <td>${item.nombre_usuario || '(Sin usuario asociado)'}</td>
                <td>
                    <span class="badge badge-chip">${item.rol || '(N/A)'}</span>
                </td>
                <td class="text-right">
                    ${botonesAccion}
                </td>
            `;

            tbody.appendChild(tr);
        });

        // Paginación + filtros
        inicializarPaginacionYBusquedaEmpleados();
        // Eventos de eliminación
        instalarEventosEliminar();

    } catch (error) {
        console.error('Error cargando empleados:', error);
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error al cargar empleados: ${error.message}</td></tr>`;
        inicializarPaginacionYBusquedaEmpleados();
    }
}

/* ===========================
   ELIMINAR (MODAL)
=========================== */
function instalarEventosEliminar() {
    const botones = document.querySelectorAll('.btn-eliminar-empleado');
    const spanId      = document.getElementById('modal-emp-id');
    const spanNombre  = document.getElementById('modal-emp-nombre');

    botones.forEach(btn => {
        btn.addEventListener('click', () => {
            empleadoAEliminarId = btn.dataset.id || null;
            btnEliminarActual   = btn;

            if (spanId)     spanId.textContent     = empleadoAEliminarId || '';
            if (spanNombre) spanNombre.textContent = btn.dataset.nombre || '';

            if (window.$) {
                $('#modalConfirmDeleteEmpleado').modal('show');
            }
        });
    });
}

async function eliminarEmpleadoConfirmado() {
    const idPersonal = empleadoAEliminarId;
    if (!idPersonal) return;

    const filaId = `fila-empleado-${idPersonal}`;
    const fila   = document.getElementById(filaId);
    let btn      = btnEliminarActual;

    try {
        if (btn) {
            btn.disabled = true;
            btn.dataset._oldHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        await authReady;

        const response = await authorizedFetch(`http://localhost:3000/api/empleados/${idPersonal}`, {
            method: 'DELETE',
        });

        let data = {};
        try { data = await response.json(); } catch (_) {}

        if (!response.ok) {
            const msg = data.error || data.details || `Error HTTP ${response.status}`;
            throw new Error(msg);
        }

        if (fila) fila.remove();

        if (window.$) {
            $('#modalConfirmDeleteEmpleado').modal('hide');
        }

        // Recalcular paginación
        inicializarPaginacionYBusquedaEmpleados();
    } catch (err) {
        console.error('Error al eliminar empleado:', err);
        alert('❌ No se pudo eliminar el empleado: ' + err.message);
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = btn.dataset._oldHtml || '<i class="fas fa-trash"></i>';
            delete btn.dataset._oldHtml;
        }
    }
}

/* ===========================
   DOM READY
=========================== */
document.addEventListener('DOMContentLoaded', () => {
    // Cargar tabla
    cargarEmpleados();

    // Mostrar/ocultar botón "Nuevo Empleado" según rol
    const userRole = localStorage.getItem('userRole');
    const btnCrear = document.getElementById('btnCrearEmpleado');
    if (btnCrear) {
        btnCrear.style.display = (userRole === 'Admin') ? 'inline-block' : 'none';
    }

    // Paginación
    const sel = selector();
    if (sel) {
        sel.addEventListener('change', () => {
            filasPorPagina = parseInt(sel.value);
            paginaActual = 1;
            renderEmpleadosPaginados();
        });
    }

    const prev = btnPrev();
    const next = btnNext();
    if (prev) {
        prev.addEventListener('click', () => {
            if (paginaActual > 1) {
                paginaActual--;
                renderEmpleadosPaginados();
            }
        });
    }
    if (next) {
        next.addEventListener('click', () => {
            paginaActual++;
            renderEmpleadosPaginados();
        });
    }

    // Búsqueda
    const formBusqueda = document.getElementById('formBusquedaEmpleados');
    const btnLimpiar   = document.getElementById('e_btn_limpiar');

    if (formBusqueda) {
        formBusqueda.addEventListener('submit', e => {
            e.preventDefault();
            aplicarFiltrosEmpleados();
            if (window.$) {
                $('#modalBusquedaEmpleados').modal('hide');
            }
        });
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', e => {
            e.preventDefault();
            if (formBusqueda) formBusqueda.reset();
            filasFiltradas = filasOriginal.slice();
            paginaActual = 1;
            renderEmpleadosPaginados();
        });
    }

    // Botón confirmar del modal eliminar
    const btnConfirm = document.getElementById('btnConfirmDeleteEmpleado');
    if (btnConfirm) {
        btnConfirm.addEventListener('click', eliminarEmpleadoConfirmado);
    }
});
</script>
@endpush