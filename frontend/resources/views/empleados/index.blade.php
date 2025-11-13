@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    <h1 class="m-0 page-title">
        <i class="fas fa-user-tie mr-2 brand-text"></i> Empleados
    </h1>
@stop

@section('content')

<div class="card card-soft shadow-sm">
    
    <div class="card-header header-accent d-flex align-items-center justify-content-between">
    <strong class="card-title text-brand mb-0">
        <i class="fas fa-user-tie mr-2"></i> Listado de Empleados
    </strong>

    <div class="ml-auto">
        <a
            id="btnCrearEmpleado"
            href="{{ route('empleados.create') }}"
            class="btn btn-sm btn-brand-outline"
            style="display: none;"
        >
            <i class="fas fa-user-plus mr-1"></i> Nuevo Empleado
        </a>
    </div>
</div>


    <div class="card-body p-0">
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
    </div>
</div>

{{-- Modal para Crear Empleado (sin cambios) --}}
<div class="modal fade" id="modalCrearEmpleado" ...>
    ... (Tu modal de crear va aquí sin cambios) ...
</div>

{{-- Modal para Actualizar Empleado (sin cambios) --}}
<div class="modal fade" id="modalActualizarEmpleado" ...>
    ... (Tu modal de actualizar va aquí sin cambios) ...
</div>
@stop

@section('css')
<style>
/* 🎨 Paleta de color local (sin afectar el resto del proyecto) */
:root{ --brand:#e24e60; --brand-100:#fde5e9; --ink:#2b2f33; }
.page-title{ font-size:1.7rem; color:#1f2937; }
.brand-text{ color: var(--brand); }
.card-soft{ border:1px solid #f0f1f5; border-radius:.6rem; }
.card-soft.shadow-sm:hover{ box-shadow:0 0 15px rgba(226,78,96,.08); }
.header-accent{ border-left:4px solid var(--brand); background:#fff; padding-top:.6rem; padding-bottom:.6rem; display:flex; justify-content:space-between; align-items:center; }
.text-brand{ color: var(--brand); }
.btn-brand-outline{ border:1px solid var(--brand); color:var(--brand); background:#fff; font-weight:600; }
.btn-brand-outline:hover{ background:var(--brand-100); color:var(--brand); }
.thead-brand th{ border-bottom:2px solid var(--brand) !important; color:#4b5563; font-weight:700; }
.badge-chip{ background:var(--brand-100); color:var(--brand); font-weight:600; border-radius:999px; padding:.35rem .6rem; }
</style>
@stop

@push('js')
<script type="module">
    import { authReady, authorizedFetch } from "{{ asset('js/firebase.js') }}";

    /**
     * Carga los empleados desde la API y los dibuja en la tabla.
     */
    async function cargarEmpleados() {
        const tbody   = document.getElementById('tabla-empleados-body');
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
                            class="btn btn-xs btn-outline-primary"
                            title="Editar"
                        >
                            <i class="fas fa-edit"></i>
                        </a>
                        <button
                            type="button"
                            class="btn btn-xs btn-outline-danger btn-eliminar-empleado"
                            title="Eliminar"
                            onclick="eliminarEmpleado(${item.id_personal})"
                        >
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

        } catch (error) {
            console.error('Error cargando empleados:', error);
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error al cargar empleados: ${error.message}</td></tr>`;
        }
    }

    /**
     * Eliminar empleado (usa authorizedFetch con token de Firebase).
     */
    window.eliminarEmpleado = async function (idPersonal) {
        if (!idPersonal) {
            alert('ID de empleado inválido.');
            return;
        }

        if (!confirm('¿Seguro que deseas eliminar este empleado? Esta acción no se puede deshacer.')) {
            return;
        }

        const filaId = `fila-empleado-${idPersonal}`;
        const fila   = document.getElementById(filaId);
        let btn      = null;

        try {
            if (fila) {
                btn = fila.querySelector('.btn-eliminar-empleado');
                if (btn) {
                    btn.disabled = true;
                    btn.dataset._oldHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                }
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

            alert('🗑 Empleado eliminado correctamente.');
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
    };

    document.addEventListener('DOMContentLoaded', () => {
        // Cargar tabla
        cargarEmpleados();

        // Mostrar u ocultar botón "Nuevo Empleado" según rol
        const userRole = localStorage.getItem('userRole');
        const btnCrear = document.getElementById('btnCrearEmpleado');
        if (btnCrear) {
            btnCrear.style.display = (userRole === 'Admin') ? 'inline-block' : 'none';
        }
    });
</script>
@endpush