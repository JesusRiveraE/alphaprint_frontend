@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content_header')
<h1 class="m-0" style="font-size:1.7rem;">
    <i class="fas fa-box-open mr-2 brand-text"></i> Pedidos
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex justify-content-between align-items-center">
        <strong class="brand-text" style="font-size:1.25rem;">Listado de Pedidos</strong>
        <div class="ml-auto d-flex align-items-center">

            {{-- BOTÓN DE BÚSQUEDA DE PEDIDOS --}}
            <button type="button"
                    class="btn btn-sm btn-brand-outline mr-2"
                    data-toggle="modal"
                    data-target="#modalBusquedaPedidos">
                <i class="fas fa-search mr-1"></i> Buscar pedidos
            </button>

            {{-- BOTÓN NUEVO PEDIDO --}}
            <a href="{{ route('pedidos.create') }}" class="btn btn-sm btn-brand-outline">
                <i class="fas fa-plus mr-1"></i> Nuevo Pedido
            </a>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        {{-- SELECTOR: CUÁNTOS PEDIDOS VER POR PÁGINA --}}
        <div class="d-flex justify-content-start align-items-center mb-3">
            <label class="mr-2 font-weight-bold">Mostrar:</label>

            <select id="selectFilasPorPaginaPedidos"
                    class="form-control form-control-sm"
                    style="width: 90px;">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="9999">Todas</option>
            </select>

            <span class="ml-2 text-muted">pedidos por página</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped text-sm align-middle" id="tabla-pedidos">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Descripción</th>
                        <th>Total (Lps)</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Entrega</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pedidos as $item)
                    @php
                        $estado = $item['estado'] ?? '—';
                        $btnClass = $estado === 'Pendiente' ? 'btn-warning'
                                  : ($estado === 'En Progreso' ? 'btn-info' : 'btn-success');
                    @endphp
                    <tr>
                        <td>{{ $item['id_pedido'] }}</td>
                        <td>{{ $item['cliente_nombre'] ?? $item['id_cliente'] }}</td>
                        <td>{{ $item['descripcion'] ?? '—' }}</td>
                        <td>{{ number_format($item['total'] ?? 0, 2) }}</td>

                        {{-- Estado con dropdown para cambiar en vivo --}}
                        <td id="estado-cell-{{ $item['id_pedido'] }}">
                            <div class="dropdown">
                                <button class="btn btn-xs dropdown-toggle {{ $btnClass }}" type="button" data-toggle="dropdown" aria-expanded="false">
                                    {{ $estado }}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right p-0">
                                    <button class="dropdown-item status-opt" data-id="{{ $item['id_pedido'] }}" data-estado="Pendiente">
                                        <i class="fas fa-hourglass-half text-warning mr-2"></i> Pendiente
                                    </button>
                                    <button class="dropdown-item status-opt" data-id="{{ $item['id_pedido'] }}" data-estado="En Progreso">
                                        <i class="fas fa-spinner text-info mr-2"></i> En Progreso
                                    </button>
                                    <button class="dropdown-item status-opt" data-id="{{ $item['id_pedido'] }}" data-estado="Completado">
                                        <i class="fas fa-check-circle text-success mr-2"></i> Completado
                                    </button>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge badge-chip">
                                {{ \Carbon\Carbon::parse($item['fecha_creacion'])->timezone('America/Tegucigalpa')->format('d/m/Y H:i:s') }}
                            </span>
                        </td>

                        {{-- Entrega con fecha + hora si existe --}}
                        <td>
                            @if(!empty($item['fecha_entrega']))
                                <span class="badge badge-chip">
                                    {{ \Carbon\Carbon::parse($item['fecha_entrega'])->timezone('America/Tegucigalpa')->format('d/m/Y H:i:s') }}
                                </span>
                            @else
                                —
                            @endif
                        </td>

                        <td class="text-right">
                            <a class="btn btn-xs btn-outline-secondary" href="{{ route('pedidos.show', $item['id_pedido']) }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a class="btn btn-xs btn-outline-primary" href="{{ route('pedidos.edit', $item['id_pedido']) }}">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Eliminar con confirmación mediante modal --}}
                            <form action="{{ route('pedidos.destroy', $item['id_pedido']) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-xs btn-outline-danger btn-delete"
                                        data-id="{{ $item['id_pedido'] }}"
                                        data-cliente="{{ $item['cliente_nombre'] ?? $item['id_cliente'] }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                            <a class="btn btn-xs btn-brand-outline" href="{{ route('pedidos.reporte', $item['id_pedido']) }}">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No hay pedidos registrados</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN FRONTEND --}}
        <div class="d-flex justify-content-between align-items-center mt-3" id="paginacion-pedidos-container">
            <small class="text-muted" id="pedidos-paginacion-info"></small>
            <div>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary mr-1"
                        id="pedidos-btn-anterior"
                        disabled>
                    Anterior
                </button>
                <button type="button"
                        class="btn btn-sm btn-brand"
                        id="pedidos-btn-siguiente"
                        disabled>
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE BÚSQUEDA DE PEDIDOS --}}
<div class="modal fade" id="modalBusquedaPedidos" tabindex="-1" role="dialog" aria-labelledby="modalBusquedaPedidosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formBusquedaPedidos">
                <div class="modal-header">
                    <h5 class="modal-title brand-text" id="modalBusquedaPedidosLabel">
                        <i class="fas fa-search mr-1"></i> Buscar pedidos
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="busqueda_pedido_id">ID Pedido</label>
                            <input type="number" class="form-control" id="busqueda_pedido_id" placeholder="Ej: 10">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="busqueda_cliente">Cliente</label>
                            <input type="text" class="form-control" id="busqueda_cliente" placeholder="Nombre o ID de cliente">
                        </div>

                        <div class="form-group col-md-5">
                            <label for="busqueda_descripcion">Descripción</label>
                            <input type="text" class="form-control" id="busqueda_descripcion" placeholder="Texto en la descripción">
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="form-group col-md-4">
                            <label for="busqueda_estado">Estado</label>
                            <select class="form-control" id="busqueda_estado">
                                <option value="">Todos</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En Progreso">En Progreso</option>
                                <option value="Completado">Completado</option>
                            </select>
                        </div>

                        <div class="form-group col-md-5">
                            <label for="busqueda_fecha">Fecha (creación o entrega)</label>
                            <input type="text" class="form-control" id="busqueda_fecha"
                                   placeholder="Ej: 13/11/2025 ó 2025-11">
                            <small class="form-text text-muted">
                                Se busca dentro del texto de las columnas de fecha.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="btnLimpiarBusquedaPedidos">
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

{{-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN --}}
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <i class="fas fa-trash-alt"></i>
                </div>

                <p class="mb-1">
                    ¿Seguro que deseas eliminar el pedido
                    <strong>#<span id="modal-pedido-id"></span></strong>?
                </p>
                <p class="mb-2">
                    Cliente:
                    <strong><span id="modal-pedido-cliente"></span></strong>
                </p>
                <p class="text-muted small mb-0">
                    Esta acción es permanente y no podrás recuperar el pedido después de eliminarlo.
                </p>
            </div>

            <div class="modal-footer border-0 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger-brand" id="btnConfirmDelete">
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
}

/* --- ESTILOS GENERALES --- */
.brand-text{
    color:var(--brand);
}
.card-soft{
    border:1px solid #eff1f5;
    border-radius:.6rem;
}
.card-soft:hover{
    box-shadow:0 0 15px rgba(226,78,96,.08);
}
.border-brand{
    border-left:4px solid var(--brand);
    background:#fff;
}
.badge-chip{
    background:var(--brand-100);
    color:var(--brand);
    font-weight:600;
    border-radius:999px;
    padding:.35rem .6rem;
}

/* --- BOTONES PRIMARIO Y OUTLINE --- */
.btn-brand-outline{
    border:1px solid var(--brand);
    color:var(--brand);
    background:#fff;
}
.btn-brand-outline:hover{
    background:#fde5e9;
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

.dropdown-item{
    font-size:.9rem;
}

/* --- MODAL BÚSQUEDA --- */
#modalBusquedaPedidos .modal-content{
    border-radius:.6rem;
}
#modalBusquedaPedidos .modal-header{
    border-bottom:1px solid #f3f4f6;
}
#modalBusquedaPedidos .modal-footer{
    border-top:1px solid #f3f4f6;
}
#modalBusquedaPedidos .modal-title{
    font-weight:600;
}
#modalBusquedaPedidos label{
    font-size:.85rem;
    color:#6b7280;
    font-weight:600;
}
#modalBusquedaPedidos .form-control{
    font-size:.9rem;
}
#modalBusquedaPedidos .form-control::placeholder{
    color:#9ca3af;
    font-size:.85rem;
}
#modalBusquedaPedidos .form-text{
    font-size:.75rem;
    color:#9ca3af;
}

/* --- MODAL CONFIRMACIÓN ELIMINACIÓN --- */
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


@section('js')
<script>
(function(){
    const csrf = '{{ csrf_token() }}';

    /* ============================
     * CAMBIAR ESTADO CON AJAX
     * ============================ */
    function estadoBtnClass(estado){
        return estado === 'Pendiente' ? 'btn-warning'
             : (estado === 'En Progreso' ? 'btn-info' : 'btn-success');
    }

    document.querySelectorAll('.status-opt').forEach(btn=>{
        btn.addEventListener('click', async ()=>{
            const id = btn.dataset.id;
            const estado = btn.dataset.estado;
            const url = "{{ route('pedidos.estado', '__ID__') }}".replace('__ID__', id);

            try{
                const res = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ estado })
                });
                const json = await res.json();

                if(json.ok){
                    const cell = document.getElementById(`estado-cell-${id}`);
                    const btnEl = cell.querySelector('button.dropdown-toggle');
                    btnEl.textContent = estado;
                    btnEl.className = `btn btn-xs dropdown-toggle ${estadoBtnClass(estado)}`;
                }else{
                    alert(json.error || 'No se pudo cambiar el estado.');
                }
            }catch(err){
                console.error(err);
                alert('Error de red al cambiar el estado.');
            }
        });
    });

    /* ============================
     * CONFIRMAR ELIMINACIÓN (MODAL)
     * ============================ */
    let formToSubmit = null;
    const modalEl      = document.getElementById('modalConfirmDelete');
    const spanId       = document.getElementById('modal-pedido-id');
    const spanCliente  = document.getElementById('modal-pedido-cliente');
    const btnConfirm   = document.getElementById('btnConfirmDelete');

    // Al hacer clic en el botón de eliminar → abrir modal
    document.querySelectorAll('.btn-delete').forEach(btn=>{
        btn.addEventListener('click', function(e){
            e.preventDefault();

            formToSubmit = this.closest('form');

            const id      = this.dataset.id || '';
            const cliente = this.dataset.cliente || '';

            if (spanId)      spanId.textContent = id;
            if (spanCliente) spanCliente.textContent = cliente;

            if (window.$) {
                $('#modalConfirmDelete').modal('show');
            } else if (modalEl) {
                modalEl.style.display = 'block';
            }
        });
    });

    // Botón "Eliminar" del modal → envía el formulario real
    if (btnConfirm) {
        btnConfirm.addEventListener('click', function(){
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });
    }

    /* ============================
     * PAGINACIÓN + SELECTOR FILAS
     * ============================ */
    const filasPedidos = Array.from(
        document.querySelectorAll('#tabla-pedidos tbody tr')
    );
    let filasFiltradas = filasPedidos.slice();
    let filasPorPagina = 10;
    let paginaActual = 1;

    const infoPaginacion = document.getElementById('pedidos-paginacion-info');
    const btnAnterior = document.getElementById('pedidos-btn-anterior');
    const btnSiguiente = document.getElementById('pedidos-btn-siguiente');
    const selectorFilas = document.getElementById('selectFilasPorPaginaPedidos');

    function renderPaginaPedidos() {
        const total = filasFiltradas.length;
        const paginasTotales = Math.max(1, Math.ceil(total / filasPorPagina));

        filasPedidos.forEach(f => f.style.display = 'none');

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
                `Mostrando ${inicio + 1} - ${Math.min(fin, total)} de ${total} pedidos (página ${paginaActual} de ${paginasTotales})`;
        }

        if (btnAnterior) btnAnterior.disabled = (paginaActual <= 1);
        if (btnSiguiente) btnSiguiente.disabled = (paginaActual >= paginasTotales);
    }

    if (selectorFilas) {
        selectorFilas.addEventListener('change', function () {
            filasPorPagina = parseInt(this.value);
            paginaActual = 1;
            renderPaginaPedidos();
        });
    }

    if (btnAnterior) {
        btnAnterior.addEventListener('click', function () {
            if (paginaActual > 1) {
                paginaActual--;
                renderPaginaPedidos();
            }
        });
    }

    if (btnSiguiente) {
        btnSiguiente.addEventListener('click', function () {
            paginaActual++;
            renderPaginaPedidos();
        });
    }

    /* ============================
     * BÚSQUEDA EN TABLA DE PEDIDOS
     * ============================ */
    function aplicarFiltrosPedidos() {
        const idVal = (document.getElementById('busqueda_pedido_id')?.value || '').trim();
        const clienteVal = (document.getElementById('busqueda_cliente')?.value || '').trim().toLowerCase();
        const descVal = (document.getElementById('busqueda_descripcion')?.value || '').trim().toLowerCase();
        const estadoVal = (document.getElementById('busqueda_estado')?.value || '').trim();
        const fechaVal = (document.getElementById('busqueda_fecha')?.value || '').trim().toLowerCase();

        filasFiltradas = [];

        filasPedidos.forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length < 8) return;

            const idTexto = celdas[0].textContent.trim();
            const clienteTexto = celdas[1].textContent.trim().toLowerCase();
            const descTexto = celdas[2].textContent.trim().toLowerCase();

            const estadoBtn = celdas[4].querySelector('button.dropdown-toggle');
            const estadoTexto = estadoBtn ? estadoBtn.textContent.trim() : '';

            const creadoTexto = celdas[5].textContent.trim().toLowerCase();
            const entregaTexto = celdas[6].textContent.trim().toLowerCase();

            let coincide = true;

            if (idVal && idTexto !== idVal) coincide = false;
            if (coincide && clienteVal && !clienteTexto.includes(clienteVal)) coincide = false;
            if (coincide && descVal && !descTexto.includes(descVal)) coincide = false;
            if (coincide && estadoVal && estadoTexto !== estadoVal) coincide = false;

            if (coincide && fechaVal) {
                const textoFechas = (creadoTexto + ' ' + entregaTexto).toLowerCase();
                if (!textoFechas.includes(fechaVal)) coincide = false;
            }

            if (coincide) filasFiltradas.push(fila);
        });

        paginaActual = 1;
        renderPaginaPedidos();
    }

    const formBusqueda = document.getElementById('formBusquedaPedidos');
    const btnLimpiar = document.getElementById('btnLimpiarBusquedaPedidos');

    if (formBusqueda) {
        formBusqueda.addEventListener('submit', function(e) {
            e.preventDefault();
            aplicarFiltrosPedidos();

            if (window.$) {
                $('#modalBusquedaPedidos').modal('hide');
            }
        });
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            if (formBusqueda) formBusqueda.reset();
            filasFiltradas = filasPedidos.slice();
            paginaActual = 1;
            renderPaginaPedidos();
        });
    }

    // Primera renderización
    renderPaginaPedidos();

})();
</script>
@stop
