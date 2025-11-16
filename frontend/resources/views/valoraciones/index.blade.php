@extends('adminlte::page')

@section('title', 'Valoraciones')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        <i class="fas fa-star mr-2 brand-text"></i> Valoraciones de Clientes
    </h1>

    <div class="d-flex">
        <a href="{{ route('valoraciones.create') }}" class="btn btn-sm btn-brand-outline mr-2">
            <i class="fas fa-plus mr-1"></i> Nueva Valoración
        </a>

        <a href="{{ route('valoraciones.reporte') }}" class="btn btn-sm btn-brand-outline mr-2">
            <i class="fas fa-file-pdf mr-1"></i> Reporte PDF
        </a>

       
    </div>
</div>
@stop

@section('content')
<div class="card card-soft">
   <div class="card-header border-brand d-flex justify-content-between align-items-center">
    <strong class="brand-text">Listado de Valoraciones</strong>

    {{-- Botón de búsqueda alineado a la derecha --}}
    <button type="button"
            class="btn btn-sm btn-brand-outline"
            data-toggle="modal"
            data-target="#modalBusquedaValoraciones"
            style="margin-left: auto;">
        <i class="fas fa-search mr-1"></i> Buscar valoraciones
    </button>
</div>


    <div class="card-body">

        {{-- SELECTOR DE FILAS --}}
        <div class="d-flex justify-content-start align-items-center mb-3">
            <label class="mr-2 font-weight-bold">Mostrar:</label>

            <select id="selectFilasValoraciones" class="form-control form-control-sm" style="width: 90px;">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="9999">Todas</option>
            </select>

            <span class="ml-2 text-muted">valoraciones por página</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabla-valoraciones">
                <thead class="thead-brand">
                    <tr>
                        <th>ID</th>
                        <th>Puntuación</th>
                        <th>Comentario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($valoraciones as $item)
                        @php
                            $score = (int)($item['puntuacion'] ?? $item->puntuacion ?? 0);
                            $badgeClass = 'badge-secondary';
                            if ($score >= 4)      $badgeClass = 'badge-success';
                            elseif ($score == 3)  $badgeClass = 'badge-warning';
                            elseif ($score > 0)   $badgeClass = 'badge-danger';

                            $fechaRaw = $item['fecha'] ?? $item->fecha ?? null;
                        @endphp

                        <tr>
                            <td class="text-muted">
                                {{ $item['id_valoracion'] ?? $item->id_valoracion ?? '' }}
                            </td>

                            <td>
                                <span class="badge {{ $badgeClass }} px-3 py-1">
                                    {{ $score }} / 5
                                </span>
                            </td>

                            <td>{{ $item['comentario'] ?? $item->comentario ?? '—' }}</td>

                            <td>
                                <span class="badge badge-chip">
                                    {{ $fechaRaw
                                        ? \Carbon\Carbon::parse($fechaRaw, 'UTC')
                                            ->setTimezone('-06:00')
                                            ->format('d/m/Y H:i')
                                        : '—' }}
                                </span>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="far fa-folder-open mr-1"></i> No hay valoraciones registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN FRONTEND --}}
        <div class="d-flex justify-content-between align-items-center mt-3" id="pagValoracionesContainer">
            <small class="text-muted" id="valoracionesInfo"></small>
            <div>
                <button class="btn btn-sm btn-outline-secondary mr-1" id="valoracionesPrev" disabled>Anterior</button>
                <button class="btn btn-sm btn-brand" id="valoracionesNext" disabled>Siguiente</button>
            </div>
        </div>

    </div>
</div>

{{-- =============== MODAL DE BÚSQUEDA =============== --}}
<div class="modal fade" id="modalBusquedaValoraciones" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <form id="formBusquedaValoraciones">
                <div class="modal-header">
                    <h5 class="modal-title brand-text">
                        <i class="fas fa-search mr-1"></i> Buscar valoraciones
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>ID Valoración</label>
                            <input type="number" class="form-control" id="v_bus_id"
                                   placeholder="Ej: 12">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Puntuación</label>
                            <select class="form-control" id="v_bus_puntuacion">
                                <option value="">Todas</option>
                                <option value="5">5</option>
                                <option value="4">4</option>
                                <option value="3">3</option>
                                <option value="2">2</option>
                                <option value="1">1</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Comentario (texto)</label>
                            <input type="text" class="form-control" id="v_bus_comentario"
                                   placeholder="Ej: servicio, diseño, entrega...">
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="form-group col-md-6">
                            <label>Fecha (texto parcial)</label>
                            <input type="text" class="form-control" id="v_bus_fecha"
                                   placeholder="Ej: 2025-11 o 13/11/2025">
                            <small class="form-text text-muted">
                                Coincidencia dentro del texto mostrado.
                            </small>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            id="v_btn_limpiar">
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
    --ink:#2b2f33;
}

.brand-text{ color:var(--brand); }

/* Botón sólido */
.btn-brand{
    background: var(--brand);
    border-color: var(--brand);
    color: #fff;
}
.btn-brand:hover{
    background:#c23c4e;
    border-color:#c23c4e;
    color:#fff;
}

.btn-brand-outline{
    border:1px solid var(--brand);
    color:var(--brand);
    background:#fff;
}
.btn-brand-outline:hover{
    background:var(--brand-100);
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
    padding:.35rem .75rem;
}

/* Modal */
#modalBusquedaValoraciones .modal-content{ border-radius:.6rem; }
#modalBusquedaValoraciones .modal-header{ border-bottom:1px solid #f3f4f6; }
#modalBusquedaValoraciones .modal-footer{ border-top:1px solid #f3f4f6; }
#modalBusquedaValoraciones label{
    font-size:.85rem;
    color:#6b7280;
    font-weight:600;
}
#modalBusquedaValoraciones .form-control{
    font-size:.9rem;
}
#modalBusquedaValoraciones .form-text{
    color:#9ca3af;
    font-size:.75rem;
}
</style>
@stop

@section('js')
<script>
(function(){

    /* === PREPARAR DATOS === */
    const filasOriginal = Array.from(document.querySelectorAll('#tabla-valoraciones tbody tr'));
    let filasFiltradas = filasOriginal.slice();
    let filasPorPagina = 10;
    let pagina = 1;

    const info = document.getElementById('valoracionesInfo');
    const btnPrev = document.getElementById('valoracionesPrev');
    const btnNext = document.getElementById('valoracionesNext');
    const selector = document.getElementById('selectFilasValoraciones');

    /* === RENDER ========== */
    function render() {
        const total = filasFiltradas.length;
        const paginas = Math.max(1, Math.ceil(total / filasPorPagina));

        filasOriginal.forEach(f => f.style.display = 'none');

        if (total === 0){
            info.textContent = '0 resultados';
            btnPrev.disabled = true;
            btnNext.disabled = true;
            return;
        }

        if (pagina > paginas) pagina = paginas;

        const inicio = (pagina - 1) * filasPorPagina;
        const fin = inicio + filasPorPagina;

        filasFiltradas.slice(inicio, fin).forEach(f => f.style.display = '');

        info.textContent = `Mostrando ${inicio + 1} - ${Math.min(fin, total)} de ${total} registros (página ${pagina} de ${paginas})`;

        btnPrev.disabled = pagina <= 1;
        btnNext.disabled = pagina >= paginas;
    }

    /* === PAGINACIÓN === */
    selector.addEventListener('change', () => {
        filasPorPagina = parseInt(selector.value);
        pagina = 1;
        render();
    });

    btnPrev.addEventListener('click', () => {
        if (pagina > 1) { pagina--; render(); }
    });

    btnNext.addEventListener('click', () => {
        pagina++; render();
    });

    /* === FILTROS === */
    function aplicarFiltros(){
        const idV   = (document.getElementById('v_bus_id')?.value || '').trim();
        const punt  = (document.getElementById('v_bus_puntuacion')?.value || '').trim();
        const com   = (document.getElementById('v_bus_comentario')?.value || '').trim().toLowerCase();
        const fecha = (document.getElementById('v_bus_fecha')?.value || '').trim().toLowerCase();

        filasFiltradas = [];

        filasOriginal.forEach(fila => {
            const cols = fila.querySelectorAll('td');
            if (cols.length < 4) return;

            const idTxt    = cols[0].textContent.trim();
            const puntTxt  = cols[1].textContent.trim().substr(0,1); // "4 / 5"
            const comTxt   = cols[2].textContent.trim().toLowerCase();
            const fechaTxt = cols[3].textContent.trim().toLowerCase();

            let ok = true;

            if (idV && idTxt !== idV) ok = false;
            if (ok && punt && puntTxt !== punt) ok = false;
            if (ok && com && !comTxt.includes(com)) ok = false;
            if (ok && fecha && !fechaTxt.includes(fecha)) ok = false;

            if (ok) filasFiltradas.push(fila);
        });

        pagina = 1;
        render();
    }

    document.getElementById('formBusquedaValoraciones')
        .addEventListener('submit', e => {
            e.preventDefault();
            aplicarFiltros();

            if (window.$) $('#modalBusquedaValoraciones').modal('hide');
        });

    document.getElementById('v_btn_limpiar')
        .addEventListener('click', () => {
            document.getElementById('formBusquedaValoraciones').reset();
            filasFiltradas = filasOriginal.slice();
            pagina = 1;
            render();
        });

    /* Inicial */
    render();

})();
</script>
@stop
