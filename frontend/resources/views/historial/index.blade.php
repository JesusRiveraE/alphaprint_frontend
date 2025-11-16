@extends('adminlte::page')

@section('title', 'Historial de Estados')

@section('content_header')
    <h1 class="m-0" style="font-size:1.7rem;">
        <i class="fas fa-history mr-2 brand-text"></i> Historial de Estado de Pedidos
    </h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex justify-content-between align-items-center">
    <strong class="brand-text" style="font-size:1.25rem;">
        Listado de Historial de Estados
    </strong>

    {{-- Botón a la derecha --}}
    <button type="button"
            class="btn btn-sm btn-brand-outline"
            data-toggle="modal"
            data-target="#modalBusquedaHistorial"
            style="margin-left: auto;">
        <i class="fas fa-search mr-1"></i> Buscar historial
    </button>
</div>


    <div class="card-body">

        {{-- Selector filas por página --}}
        <div class="d-flex justify-content-start align-items-center mb-3">
            <label class="mr-2 font-weight-bold">Mostrar:</label>

            <select id="selectFilasPorPaginaHistorial"
                    class="form-control form-control-sm"
                    style="width: 90px;">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="9999">Todas</option>
            </select>

            <span class="ml-2 text-muted">registros por página</span>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tabla-historial">
                <thead class="table-light">
                    <tr>
                        <th>ID Historial</th>
                        <th>ID Pedido</th>
                        <th>Descripción del Pedido</th>
                        <th>Estado del Pedido</th>
                        <th>Estado Anterior</th>
                        <th>Estado Nuevo</th>
                        <th>Fecha del Cambio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $item)
                        @php
                            $estadoActual   = $item['estado'] ?? '';
                            $estadoAnterior = $item['estado_anterior'] ?? '';
                            $estadoNuevo    = $item['estado_nuevo'] ?? '';

                            $cls = function($e){
                                if ($e === 'Pendiente')    return 'estado-pendiente';
                                if ($e === 'En Progreso')  return 'estado-progreso';
                                if ($e === 'Completado')   return 'estado-completado';
                                if ($e === 'Cancelado')    return 'estado-cancelado';
                                return 'estado-otro';
                            };

                            // ✅ Formato de fecha con zona horaria -6 (Tegucigalpa)
                            $fechaCambio = isset($item['fecha'])
                                ? \Carbon\Carbon::parse($item['fecha'])->timezone('America/Tegucigalpa')->format('d/m/Y H:i:s')
                                : '—';
                        @endphp
                        <tr>
                            <td>{{ $item['id_historial'] ?? '' }}</td>
                            <td>{{ $item['id_pedido'] ?? '' }}</td>
                            <td>{{ $item['descripcion'] ?? '—' }}</td>

                            <td>
                                <span class="badge badge-estado {{ $cls($estadoActual) }}">
                                    {{ $estadoActual }}
                                </span>
                            </td>

                            <td>
                                <span class="badge badge-estado {{ $cls($estadoAnterior) }}">
                                    {{ $estadoAnterior }}
                                </span>
                            </td>

                            <td>
                                <span class="badge badge-estado {{ $cls($estadoNuevo) }}">
                                    {{ $estadoNuevo }}
                                </span>
                            </td>

                            <td>{{ $fechaCambio }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación frontend --}}
        <div class="d-flex justify-content-between align-items-center mt-3" id="paginacion-historial-container">
            <small class="text-muted" id="historial-paginacion-info"></small>
            <div>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary mr-1"
                        id="historial-btn-anterior"
                        disabled>
                    Anterior
                </button>
                <button type="button"
                        class="btn btn-sm btn-brand"
                        id="historial-btn-siguiente"
                        disabled>
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE BÚSQUEDA DE HISTORIAL --}}
<div class="modal fade" id="modalBusquedaHistorial" tabindex="-1" role="dialog" aria-labelledby="modalBusquedaHistorialLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formBusquedaHistorial">
                <div class="modal-header">
                    <h5 class="modal-title brand-text" id="modalBusquedaHistorialLabel">
                        <i class="fas fa-search mr-1"></i> Buscar historial
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="busqueda_historial_id">ID Historial</label>
                            <input type="number" class="form-control" id="busqueda_historial_id" placeholder="Ej: 10">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="busqueda_pedido_id">ID Pedido</label>
                            <input type="number" class="form-control" id="busqueda_pedido_id" placeholder="Ej: 15">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="busqueda_descripcion">Descripción del Pedido</label>
                            <input type="text" class="form-control" id="busqueda_descripcion"
                                   placeholder="Texto en la descripción del pedido">
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="form-group col-md-6">
                            <label for="busqueda_estado_anterior">Estado anterior</label>
                            <select class="form-control" id="busqueda_estado_anterior">
                                <option value="">Todos</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En Progreso">En Progreso</option>
                                <option value="Completado">Completado</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="busqueda_estado_nuevo">Estado nuevo</label>
                            <select class="form-control" id="busqueda_estado_nuevo">
                                <option value="">Todos</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En Progreso">En Progreso</option>
                                <option value="Completado">Completado</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="form-group col-md-6">
                            <label for="busqueda_fecha">Fecha del cambio (texto parcial)</label>
                            <input type="text" class="form-control" id="busqueda_fecha"
                                   placeholder="Ej: 13/11/2025 o 2025-11">
                            <small class="form-text text-muted">
                                Se busca dentro del texto mostrado en la columna de fecha.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="btnLimpiarBusquedaHistorial">
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
:root { --brand:#e24e60; --brand-100:#fde5e9; }

/* === Marca y estructura === */
.brand-text { color: var(--brand); }
.card-soft { border:1px solid #eff1f5; border-radius:.6rem; }
.card-soft:hover { box-shadow:0 0 15px rgba(226,78,96,.08); }
.border-brand { border-left:4px solid var(--brand); background:#fff; }

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
    color: #fff;
}
.btn-brand:hover{
    background: #c23c4e;
    border-color: #c23c4e;
    color: #fff;
}

/* === Badges de estado con los tonos exactos de tu imagen === */
.badge-estado{
    border-radius:.5rem;
    font-weight:700;
    padding:.35rem .6rem;
    letter-spacing:.2px;
}
.estado-pendiente   { background:#ffc107; color:#1f2937; } /* Amarillo */
.estado-progreso    { background:#17a2b8; color:#fff; }    /* Turquesa */
.estado-completado  { background:#28a745; color:#fff; }    /* Verde */
.estado-cancelado   { background:#dc3545; color:#fff; }    /* Rojo */
.estado-otro        { background:#6c757d; color:#fff; }    /* Gris */

/* 🎨 Modal de búsqueda */
#modalBusquedaHistorial .modal-content{
    border-radius:.6rem;
}
#modalBusquedaHistorial .modal-header{
    border-bottom:1px solid #f3f4f6;
}
#modalBusquedaHistorial .modal-footer{
    border-top:1px solid #f3f4f6;
}
#modalBusquedaHistorial .modal-title{
    font-weight:600;
}
#modalBusquedaHistorial label{
    font-size:.85rem;
    color:#6b7280;
    font-weight:600;
}
#modalBusquedaHistorial .form-control{
    font-size:.9rem;
}
#modalBusquedaHistorial .form-control::placeholder{
    color:#9ca3af;
    font-size:.85rem;
}
#modalBusquedaHistorial .form-text{
    font-size:.75rem;
    color:#9ca3af;
}
</style>
@stop

@section('js')
<script>
(function(){

    /* ============================
     * PAGINACIÓN + SELECTOR FILAS
     * ============================ */
    const filasHistorial = Array.from(
        document.querySelectorAll('#tabla-historial tbody tr')
    );
    let filasFiltradas = filasHistorial.slice();
    let filasPorPagina = 10;
    let paginaActual = 1;

    const infoPaginacion = document.getElementById('historial-paginacion-info');
    const btnAnterior = document.getElementById('historial-btn-anterior');
    const btnSiguiente = document.getElementById('historial-btn-siguiente');
    const selectorFilas = document.getElementById('selectFilasPorPaginaHistorial');

    function renderPaginaHistorial() {
        const total = filasFiltradas.length;
        const paginasTotales = Math.max(1, Math.ceil(total / filasPorPagina));

        filasHistorial.forEach(f => f.style.display = 'none');

        if (total === 0) {
            if (infoPaginacion) infoPaginacion.textContent = '0 resultados';
            if (btnAnterior) btnAnterior.disabled = true;
            if (btnSiguiente) btnSiguiente.disabled = true;
            return;
        }

        if (paginaActual > paginasTotales) paginaActual = paginasTotales;

        const inicio = (paginaActual - 1) * filasPorPagina;
        const fin = inicio + filasPorPagina;

        filasFiltradas.slice(inicio, fin).forEach(f => {
            f.style.display = '';
        });

        if (infoPaginacion) {
            infoPaginacion.textContent =
                `Mostrando ${inicio + 1} - ${Math.min(fin, total)} de ${total} registros (página ${paginaActual} de ${paginasTotales})`;
        }

        if (btnAnterior) btnAnterior.disabled = (paginaActual <= 1);
        if (btnSiguiente) btnSiguiente.disabled = (paginaActual >= paginasTotales);
    }

    if (selectorFilas) {
        selectorFilas.addEventListener('change', function () {
            filasPorPagina = parseInt(this.value);
            paginaActual = 1;
            renderPaginaHistorial();
        });
    }

    if (btnAnterior) {
        btnAnterior.addEventListener('click', function () {
            if (paginaActual > 1) {
                paginaActual--;
                renderPaginaHistorial();
            }
        });
    }

    if (btnSiguiente) {
        btnSiguiente.addEventListener('click', function () {
            paginaActual++;
            renderPaginaHistorial();
        });
    }

    /* ============================
     * BÚSQUEDA EN TABLA DE HISTORIAL
     * ============================ */
    function aplicarFiltrosHistorial() {
        const idHistVal   = (document.getElementById('busqueda_historial_id')?.value || '').trim();
        const idPedVal    = (document.getElementById('busqueda_pedido_id')?.value || '').trim();
        const descVal     = (document.getElementById('busqueda_descripcion')?.value || '').trim().toLowerCase();
        const estAntVal   = (document.getElementById('busqueda_estado_anterior')?.value || '').trim();
        const estNuevoVal = (document.getElementById('busqueda_estado_nuevo')?.value || '').trim();
        const fechaVal    = (document.getElementById('busqueda_fecha')?.value || '').trim().toLowerCase();

        filasFiltradas = [];

        filasHistorial.forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length < 7) return;

            const idHistTexto = celdas[0].textContent.trim();
            const idPedTexto  = celdas[1].textContent.trim();
            const descTexto   = celdas[2].textContent.trim().toLowerCase();

            // Estado anterior y nuevo (badges)
            const badgeAnt = celdas[4].querySelector('.badge-estado');
            const badgeNue = celdas[5].querySelector('.badge-estado');

            const estAntTexto = badgeAnt ? badgeAnt.textContent.trim() : '';
            const estNueTexto = badgeNue ? badgeNue.textContent.trim() : '';

            const fechaTexto = celdas[6].textContent.trim().toLowerCase();

            let coincide = true;

            if (idHistVal && idHistTexto !== idHistVal) coincide = false;
            if (coincide && idPedVal && idPedTexto !== idPedVal) coincide = false;
            if (coincide && descVal && !descTexto.includes(descVal)) coincide = false;

            if (coincide && estAntVal && estAntTexto !== estAntVal) coincide = false;
            if (coincide && estNuevoVal && estNueTexto !== estNuevoVal) coincide = false;

            if (coincide && fechaVal && !fechaTexto.includes(fechaVal)) coincide = false;

            if (coincide) filasFiltradas.push(fila);
        });

        paginaActual = 1;
        renderPaginaHistorial();
    }

    const formBusqueda = document.getElementById('formBusquedaHistorial');
    const btnLimpiar   = document.getElementById('btnLimpiarBusquedaHistorial');

    if (formBusqueda) {
        formBusqueda.addEventListener('submit', function(e) {
            e.preventDefault();
            aplicarFiltrosHistorial();

            if (window.$) {
                $('#modalBusquedaHistorial').modal('hide');
            }
        });
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            if (formBusqueda) formBusqueda.reset();
            filasFiltradas = filasHistorial.slice();
            paginaActual = 1;
            renderPaginaHistorial();
        });
    }

    // Primera renderización
    renderPaginaHistorial();

})();
</script>
@stop
