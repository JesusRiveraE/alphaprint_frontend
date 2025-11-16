@extends('adminlte::page')

@section('title', 'Archivos')

@section('content_header')
    <h1 class="m-0 page-title">
        <i class="fas fa-folder-open mr-2 brand-text"></i> Archivos
    </h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <!-- Encabezado con línea roja y botones alineados -->
    <div class="card-header header-accent d-flex align-items-center justify-content-between">
        <strong class="card-title text-brand mb-0">
            <i class="fas fa-folder-open mr-2"></i> Listado de Archivos
        </strong>
        <div class="ml-auto d-flex align-items-center">
            {{-- Botón de búsqueda --}}
            <button type="button"
                    class="btn btn-sm btn-brand-outline mr-2"
                    data-toggle="modal"
                    data-target="#modalBusquedaArchivos">
                <i class="fas fa-search mr-1"></i> Buscar archivos
            </button>

            {{-- Botón agregar --}}
            <a href="{{ route('archivos.create') }}" class="btn btn-sm btn-brand-outline">
                <i class="fas fa-plus mr-1"></i> Agregar Archivo
            </a>
        </div>
    </div>

    <div class="card-body p-2">

        {{-- Selector de filas por página --}}
        <div class="d-flex justify-content-start align-items-center mb-3">
            <label class="mr-2 font-weight-bold">Mostrar:</label>

            <select id="selectFilasPorPaginaArchivos"
                    class="form-control form-control-sm"
                    style="width: 90px;">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="9999">Todas</option>
            </select>

            <span class="ml-2 text-muted">archivos por página</span>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover mb-0" id="tabla-archivos">
                <thead class="thead-brand">
                    <tr>
                        <th>ID</th>
                        <th>Pedido</th>
                        <th>Descripción Pedido</th>
                        <th>Estado Pedido</th>
                        <th>URL</th>
                        <th>Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archivos as $a)
                        <tr>
                            <td>{{ $a['id_archivo'] ?? '' }}</td>
                            <td>{{ $a['id_pedido'] ?? '' }}</td>
                            <td>{{ $a['pedido_descripcion'] ?? '-' }}</td>
                            <td>
                                @php $estado = $a['pedido_estado'] ?? '-'; @endphp
                                <span class="badge
                                    @if($estado==='Pendiente') bg-warning
                                    @elseif($estado==='En Progreso') bg-info
                                    @elseif($estado==='Completado') bg-success
                                    @else bg-secondary @endif">
                                    {{ $estado }}
                                </span>
                            </td>
                            <td>
                                @if(!empty($a['url']))
                                    <a href="{{ $a['url'] }}" target="_blank" class="text-brand">Abrir</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $a['comentario'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                <i class="far fa-folder-open mr-1"></i> Sin archivos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación frontend --}}
        <div class="d-flex justify-content-between align-items-center mt-3" id="paginacion-archivos-container">
            <small class="text-muted" id="archivos-paginacion-info"></small>
            <div>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary mr-1"
                        id="archivos-btn-anterior"
                        disabled>
                    Anterior
                </button>
                <button type="button"
                        class="btn btn-sm btn-brand"
                        id="archivos-btn-siguiente"
                        disabled>
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE BÚSQUEDA DE ARCHIVOS --}}
<div class="modal fade" id="modalBusquedaArchivos" tabindex="-1" role="dialog" aria-labelledby="modalBusquedaArchivosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formBusquedaArchivos">
                <div class="modal-header">
                    <h5 class="modal-title brand-text" id="modalBusquedaArchivosLabel">
                        <i class="fas fa-search mr-1"></i> Buscar archivos
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="busqueda_archivo_id">ID Archivo</label>
                            <input type="number" class="form-control" id="busqueda_archivo_id" placeholder="Ej: 5">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="busqueda_pedido_id">ID Pedido</label>
                            <input type="number" class="form-control" id="busqueda_pedido_id" placeholder="Ej: 15">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="busqueda_descripcion_pedido">Descripción Pedido</label>
                            <input type="text" class="form-control" id="busqueda_descripcion_pedido"
                                   placeholder="Parte de la descripción del pedido">
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="form-group col-md-4">
                            <label for="busqueda_estado_pedido">Estado del Pedido</label>
                            <select class="form-control" id="busqueda_estado_pedido">
                                <option value="">Todos</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En Progreso">En Progreso</option>
                                <option value="Completado">Completado</option>
                            </select>
                        </div>

                        <div class="form-group col-md-8">
                            <label for="busqueda_comentario">Comentario</label>
                            <input type="text" class="form-control" id="busqueda_comentario"
                                   placeholder="Texto dentro del comentario">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="btnLimpiarBusquedaArchivos">
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
/* 🎨 Paleta local (solo esta vista) */
:root{ --brand:#e24e60; --brand-100:#fde5e9; --ink:#2b2f33; }

/* 🔖 Título principal */
.page-title{ font-size:1.7rem; color:#1f2937; }
.brand-text{ color: var(--brand); }

/* 🧾 Card con línea roja */
.card-soft{ border:1px solid #f0f1f5; border-radius:.6rem; }
.card-soft.shadow-sm:hover{ box-shadow:0 0 15px rgba(226,78,96,.08); }

.header-accent{
    border-left:4px solid var(--brand);
    background:#fff;
    padding:.6rem 1rem;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.text-brand{ color:var(--brand); }

/* ➕ Botones */
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

/* Botón primario relleno (rojo) */
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

/* 📋 Tabla */
.thead-brand th{
    border-bottom:2px solid var(--brand) !important;
    color:#4b5563;
    font-weight:700;
}

/* 🔗 Enlaces */
.text-brand:hover{
    text-decoration: underline;
    color: var(--brand);
}

/* 🏷️ Badges coherentes con marca */
.badge.bg-warning{ color:#8a6d1d; background:#ffefc2; }
.badge.bg-info{ color:#0b647a; background:#dff3f8; }
.badge.bg-success{ color:#1e7b39; background:#e6f6ea; }

/* 🎨 Estilo del modal de búsqueda (alineado con notificaciones/pedidos) */
#modalBusquedaArchivos .modal-content{
    border-radius:.6rem;
}
#modalBusquedaArchivos .modal-header{
    border-bottom:1px solid #f3f4f6;
}
#modalBusquedaArchivos .modal-footer{
    border-top:1px solid #f3f4f6;
}
#modalBusquedaArchivos .modal-title{
    font-weight:600;
}
#modalBusquedaArchivos label{
    font-size:.85rem;
    color:#6b7280;
    font-weight:600;
}
#modalBusquedaArchivos .form-control{
    font-size:.9rem;
}
#modalBusquedaArchivos .form-control::placeholder{
    color:#9ca3af;
    font-size:.85rem;
}
#modalBusquedaArchivos .form-text{
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
    const filasArchivos = Array.from(
        document.querySelectorAll('#tabla-archivos tbody tr')
    );
    let filasFiltradas = filasArchivos.slice();
    let filasPorPagina = 10;
    let paginaActual = 1;

    const infoPaginacion = document.getElementById('archivos-paginacion-info');
    const btnAnterior = document.getElementById('archivos-btn-anterior');
    const btnSiguiente = document.getElementById('archivos-btn-siguiente');
    const selectorFilas = document.getElementById('selectFilasPorPaginaArchivos');

    function renderPaginaArchivos() {
        const total = filasFiltradas.length;
        const paginasTotales = Math.max(1, Math.ceil(total / filasPorPagina));

        filasArchivos.forEach(f => f.style.display = 'none');

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
                `Mostrando ${inicio + 1} - ${Math.min(fin, total)} de ${total} archivos (página ${paginaActual} de ${paginasTotales})`;
        }

        if (btnAnterior) btnAnterior.disabled = (paginaActual <= 1);
        if (btnSiguiente) btnSiguiente.disabled = (paginaActual >= paginasTotales);
    }

    if (selectorFilas) {
        selectorFilas.addEventListener('change', function () {
            filasPorPagina = parseInt(this.value);
            paginaActual = 1;
            renderPaginaArchivos();
        });
    }

    if (btnAnterior) {
        btnAnterior.addEventListener('click', function () {
            if (paginaActual > 1) {
                paginaActual--;
                renderPaginaArchivos();
            }
        });
    }

    if (btnSiguiente) {
        btnSiguiente.addEventListener('click', function () {
            paginaActual++;
            renderPaginaArchivos();
        });
    }

    /* ============================
     * BÚSQUEDA EN TABLA DE ARCHIVOS
     * ============================ */
    function aplicarFiltrosArchivos() {
        const idArchivoVal = (document.getElementById('busqueda_archivo_id')?.value || '').trim();
        const idPedidoVal = (document.getElementById('busqueda_pedido_id')?.value || '').trim();
        const descPedidoVal = (document.getElementById('busqueda_descripcion_pedido')?.value || '').trim().toLowerCase();
        const estadoVal = (document.getElementById('busqueda_estado_pedido')?.value || '').trim();
        const comentarioVal = (document.getElementById('busqueda_comentario')?.value || '').trim().toLowerCase();

        filasFiltradas = [];

        filasArchivos.forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length < 6) return;

            const idArchivoTexto = celdas[0].textContent.trim();
            const idPedidoTexto = celdas[1].textContent.trim();
            const descPedidoTexto = celdas[2].textContent.trim().toLowerCase();

            // Estado está dentro del span.badge
            const badge = celdas[3].querySelector('.badge');
            const estadoTexto = badge ? badge.textContent.trim() : '';

            const comentarioTexto = celdas[5].textContent.trim().toLowerCase();

            let coincide = true;

            if (idArchivoVal && idArchivoTexto !== idArchivoVal) coincide = false;
            if (coincide && idPedidoVal && idPedidoTexto !== idPedidoVal) coincide = false;
            if (coincide && descPedidoVal && !descPedidoTexto.includes(descPedidoVal)) coincide = false;
            if (coincide && estadoVal && estadoTexto !== estadoVal) coincide = false;
            if (coincide && comentarioVal && !comentarioTexto.includes(comentarioVal)) coincide = false;

            if (coincide) filasFiltradas.push(fila);
        });

        paginaActual = 1;
        renderPaginaArchivos();
    }

    const formBusqueda = document.getElementById('formBusquedaArchivos');
    const btnLimpiar = document.getElementById('btnLimpiarBusquedaArchivos');

    if (formBusqueda) {
        formBusqueda.addEventListener('submit', function(e) {
            e.preventDefault();
            aplicarFiltrosArchivos();

            if (window.$) {
                $('#modalBusquedaArchivos').modal('hide');
            }
        });
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            if (formBusqueda) formBusqueda.reset();
            filasFiltradas = filasArchivos.slice();
            paginaActual = 1;
            renderPaginaArchivos();
        });
    }

    // Primera renderización
    renderPaginaArchivos();

})();
</script>
@stop
