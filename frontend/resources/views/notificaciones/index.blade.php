@extends('adminlte::page')

@section('title', 'Notificaciones')

@section('content_header')
<h1 class="m-0" style="font-size:1.7rem;">
    <i class="fas fa-bell mr-2 brand-text"></i> Notificaciones del Sistema
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">

    {{-- HEADER CON TÍTULO A LA IZQUIERDA Y BOTÓN A LA DERECHA --}}
    <div class="card-header border-brand d-flex justify-content-between align-items-center">
        <strong class="brand-text" style="font-size:1.25rem;">Listado de Notificaciones</strong>

        <button type="button"
                class="btn btn-sm btn-brand-outline"
                data-toggle="modal"
                data-target="#modalBusquedaNotificaciones"
                style="margin-left:auto;">
            <i class="fas fa-search mr-1"></i> Buscar notificaciones
        </button>
    </div>

    <div class="card-body">

        {{-- SELECTOR: CUÁNTAS NOTIFICACIONES VER POR PÁGINA --}}
        <div class="d-flex justify-content-start align-items-center mb-3">
            <label class="mr-2 font-weight-bold">Mostrar:</label>

            <select id="selectFilasPorPagina"
                    class="form-control form-control-sm"
                    style="width: 90px;">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="9999">Todas</option>
            </select>

            <span class="ml-2 text-muted">notificaciones por página</span>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="tabla-notificaciones">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Pedido</th>
                        <th>Mensaje</th>
                        <th>Leído</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notificaciones as $item)
                        <tr id="row-{{ $item['id_notificacion'] }}">
                            <td>{{ $item['id_notificacion'] ?? '' }}</td>
                            <td>{{ $item['id_pedido'] ?? '' }}</td>
                            <td>{{ $item['mensaje'] ?? '' }}</td>

                            <td class="text-center" id="leido-cell-{{ $item['id_notificacion'] }}">
                                @if(!($item['leido'] ?? false))
                                    <button class="btn btn-xs btn-warning btn-toggle-leido"
                                            data-id="{{ $item['id_notificacion'] }}">
                                        No
                                    </button>
                                @else
                                    <span class="badge badge-success">Sí</span>
                                @endif
                            </td>

                            <td>
                                @if(!empty($item['fecha']))
                                    <span class="badge badge-chip">
                                        {{ \Carbon\Carbon::parse($item['fecha'])
                                            ->timezone('America/Tegucigalpa')
                                            ->format('d/m/Y H:i:s') }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(empty($notificaciones) || count($notificaciones)===0)
            <div class="text-center text-muted py-3">Sin notificaciones</div>
        @endif

        {{-- PAGINACIÓN --}}
        <div class="d-flex justify-content-between align-items-center mt-3" id="paginacion-notificaciones-container">
            <small class="text-muted" id="paginacion-info"></small>
            <div>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary mr-1"
                        id="btnPaginaAnterior"
                        disabled>
                    Anterior
                </button>

                <button type="button"
                        class="btn btn-sm btn-brand"
                        id="btnPaginaSiguiente"
                        disabled>
                    Siguiente
                </button>
            </div>
        </div>

    </div>
</div>

{{-- MODAL DE BÚSQUEDA --}}
<div class="modal fade" id="modalBusquedaNotificaciones" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formBusquedaNotificaciones">
                <div class="modal-header">
                    <h5 class="modal-title brand-text">
                        <i class="fas fa-search mr-1"></i> Buscar notificaciones
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>ID notificación</label>
                            <input type="number" class="form-control" id="busqueda_id">
                        </div>

                        <div class="form-group col-md-3">
                            <label>ID pedido</label>
                            <input type="number" class="form-control" id="busqueda_pedido">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Mensaje / texto</label>
                            <input type="text"
                                   class="form-control"
                                   id="busqueda_texto"
                                   placeholder="Ej: nuevo pedido, archivo…">
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="form-group col-md-4">
                            <label>Fecha (texto parcial)</label>
                            <input type="text"
                                   class="form-control"
                                   id="busqueda_fecha"
                                   placeholder="Ej: 13/11/2025">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="btnLimpiarBusqueda">
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
}
.brand-text{ color:var(--brand); }
.card-soft{ border:1px solid #eff1f5; border-radius:.6rem; }
.border-brand{ border-left:4px solid var(--brand); background:#fff; }
.badge-chip{
    background:var(--brand-100);
    color:var(--brand);
    font-weight:600;
    border-radius:999px;
    padding:.35rem .6rem;
}
.btn-brand-outline{
    border:1px solid var(--brand);
    color:var(--brand);
    background:#fff;
}
.btn-brand-outline:hover{
    background:var(--brand);
    color:#fff;
}
.btn-brand{
    background:var(--brand);
    border-color:var(--brand);
    color:#fff;
}
.btn-brand:hover{
    background:#c23c4e;
    border-color:#c23c4e;
}
</style>
@stop



@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ============================
     * MARCAR COMO LEÍDO
     * ============================ */
    document.querySelectorAll('.btn-toggle-leido').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;

            try {
                const res = await fetch(`{{ url('/notificaciones') }}/${id}/leido`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                });

                const json = await res.json();

                if (json.ok) {
                    const cell = document.getElementById(`leido-cell-${id}`);
                    cell.innerHTML = '<span class="badge badge-success">Sí</span>';
                } else {
                    alert(json.error || 'No se pudo marcar como leído');
                }

            } catch (e) {
                console.error(e);
                alert('Error de red.');
            }
        });
    });

    /* ============================
     * PAGINACIÓN + SELECTOR DE FILAS
     * ============================ */

    const filasNotificaciones = Array.from(
        document.querySelectorAll('#tabla-notificaciones tbody tr')
    );

    let filasFiltradas = filasNotificaciones.slice();
    let paginaActual = 1;
    let filasPorPagina = 10;

    const selectorFilas = document.getElementById('selectFilasPorPagina');

    if (selectorFilas) {
        selectorFilas.addEventListener('change', function () {
            filasPorPagina = parseInt(this.value);
            paginaActual = 1;
            renderPagina();
        });
    }

    const infoPaginacion = document.getElementById('paginacion-info');
    const btnAnterior = document.getElementById('btnPaginaAnterior');
    const btnSiguiente = document.getElementById('btnPaginaSiguiente');

    function renderPagina() {
        const total = filasFiltradas.length;
        const paginasTotales = Math.max(1, Math.ceil(total / filasPorPagina));

        filasNotificaciones.forEach(f => f.style.display = 'none');

        if (total === 0) {
            infoPaginacion.textContent = '0 resultados';
            btnAnterior.disabled = true;
            btnSiguiente.disabled = true;
            return;
        }

        if (paginaActual > paginasTotales) paginaActual = paginasTotales;

        const inicio = (paginaActual - 1) * filasPorPagina;
        const fin = inicio + filasPorPagina;

        filasFiltradas.slice(inicio, fin).forEach(f => {
            f.style.display = '';
        });

        infoPaginacion.textContent =
            `Mostrando ${inicio + 1}–${Math.min(fin, total)} de ${total} notificaciones. Página ${paginaActual}/${paginasTotales}`;

        btnAnterior.disabled = (paginaActual <= 1);
        btnSiguiente.disabled = (paginaActual >= paginasTotales);
    }

    /* ============================
     * BÚSQUEDA
     * ============================ */
    function aplicarFiltrosNotificaciones() {

        const idVal = (document.getElementById('busqueda_id')?.value || '').trim();
        const pedidoVal = (document.getElementById('busqueda_pedido')?.value || '').trim();
        const textoVal = (document.getElementById('busqueda_texto')?.value || '').trim().toLowerCase();
        const fechaVal = (document.getElementById('busqueda_fecha')?.value || '').trim().toLowerCase();

        filasFiltradas = [];

        filasNotificaciones.forEach(fila => {

            const celdas = fila.querySelectorAll('td');
            if (celdas.length < 5) return;

            const idTexto = celdas[0].textContent.trim();
            const pedidoTexto = celdas[1].textContent.trim();
            const mensajeTexto = celdas[2].textContent.trim().toLowerCase();
            const fechaTexto = celdas[4].textContent.trim().toLowerCase();

            let coincide = true;

            if (idVal && idTexto !== idVal) coincide = false;
            if (coincide && pedidoVal && pedidoTexto !== pedidoVal) coincide = false;
            if (coincide && textoVal && !mensajeTexto.includes(textoVal)) coincide = false;
            if (coincide && fechaVal && !fechaTexto.includes(fechaVal)) coincide = false;

            if (coincide) filasFiltradas.push(fila);
        });

        paginaActual = 1;
        renderPagina();
    }

    document.getElementById('formBusquedaNotificaciones')?.addEventListener('submit', function (e) {
        e.preventDefault();
        aplicarFiltrosNotificaciones();

        if (window.$) $('#modalBusquedaNotificaciones').modal('hide');
    });

    document.getElementById('btnLimpiarBusqueda')?.addEventListener('click', function () {
        document.getElementById('formBusquedaNotificaciones').reset();
        filasFiltradas = filasNotificaciones.slice();
        paginaActual = 1;
        renderPagina();
    });

    btnAnterior?.addEventListener('click', function () {
        if (paginaActual > 1) {
            paginaActual--;
            renderPagina();
        }
    });

    btnSiguiente?.addEventListener('click', function () {
        paginaActual++;
        renderPagina();
    });

    renderPagina();
});
</script>
@stop
