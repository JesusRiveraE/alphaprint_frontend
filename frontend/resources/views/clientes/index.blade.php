@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1 class="m-0 page-title">
        <i class="fas fa-user-friends mr-2 brand-text"></i> Clientes
    </h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header header-accent d-flex align-items-center justify-content-between">
        <strong class="card-title text-brand mb-0">
            <i class="fas fa-user-friends mr-2"></i> Listado de Clientes
        </strong>

        <div class="ml-auto d-flex align-items-center">
            {{-- Botón buscar --}}
            <button type="button"
                    class="btn btn-sm btn-brand-outline mr-2"
                    data-toggle="modal"
                    data-target="#modalBusquedaClientes">
                <i class="fas fa-search mr-1"></i> Buscar clientes
            </button>

            {{-- Botón nuevo cliente (ajusta la ruta si tu proyecto usa otra) --}}
            <a href="{{ route('clientes.create') }}"
               class="btn btn-sm btn-brand-outline">
                <i class="fas fa-user-plus mr-1"></i> Nuevo Cliente
            </a>
        </div>
    </div>

    <div class="card-body">

        {{-- Selector filas por página --}}
        <div class="d-flex justify-content-start align-items-center mb-3">
            <label class="mr-2 font-weight-bold">Mostrar:</label>

            <select id="selectFilasClientes"
                    class="form-control form-control-sm"
                    style="width: 90px;">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="9999">Todos</option>
            </select>

            <span class="ml-2 text-muted">clientes por página</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabla-clientes">
                <thead class="thead-brand">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Fecha de Creación</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes ?? [] as $c)
                        <tr>
                            <td class="text-muted">{{ $c['id_cliente'] ?? $c->id_cliente ?? '' }}</td>
                            <td><strong>{{ $c['nombre'] ?? $c->nombre ?? '' }}</strong></td>
                            <td>{{ $c['telefono'] ?? $c->telefono ?? '—' }}</td>
                            <td>{{ $c['correo'] ?? $c->correo ?? '—' }}</td>
                            <td>
                               @php
                                  $fechaRaw = $c['fecha_creacion'] ?? $c->fecha_creacion ?? null;
                               @endphp

                               {{ $fechaRaw ?? '—' }}

                            </td>
                            <td class="text-right">
                                {{-- Ajusta rutas según tu proyecto --}}
                                <a href="{{ route('clientes.show', $c['id_cliente'] ?? $c->id_cliente ?? 0) }}"
                                   class="btn btn-xs btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('clientes.edit', $c['id_cliente'] ?? $c->id_cliente ?? 0) }}"
                                   class="btn btn-xs btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('clientes.destroy', $c['id_cliente'] ?? $c->id_cliente ?? 0) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="far fa-folder-open mr-1"></i> No hay clientes registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación frontend --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted" id="clientesInfo"></small>
            <div>
                <button class="btn btn-sm btn-outline-secondary mr-1" id="clientesPrev" disabled>
                    Anterior
                </button>
                <button class="btn btn-sm btn-brand" id="clientesNext" disabled>
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BÚSQUEDA CLIENTES --}}
<div class="modal fade" id="modalBusquedaClientes" tabindex="-1" role="dialog" aria-labelledby="modalBusquedaClientesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formBusquedaClientes">
                <div class="modal-header">
                    <h5 class="modal-title brand-text" id="modalBusquedaClientesLabel">
                        <i class="fas fa-search mr-1"></i> Buscar clientes
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="c_bus_id">ID</label>
                            <input type="number" class="form-control" id="c_bus_id" placeholder="Ej: 15">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="c_bus_nombre">Nombre</label>
                            <input type="text" class="form-control" id="c_bus_nombre"
                                   placeholder="Nombre o parte del nombre">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="c_bus_telefono">Teléfono</label>
                            <input type="text" class="form-control" id="c_bus_telefono"
                                   placeholder="Texto en el teléfono">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="c_bus_correo">Correo</label>
                            <input type="text" class="form-control" id="c_bus_correo"
                                   placeholder="Texto en el correo">
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="form-group col-md-6">
                            <label for="c_bus_fecha">Fecha de creación (texto)</label>
                            <input type="text" class="form-control" id="c_bus_fecha"
                                   placeholder="Ej: 13/11/2025 o 2025-11">
                            <small class="form-text text-muted">
                                Se busca dentro del texto mostrado en la columna de fecha.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            id="c_btn_limpiar">
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

/* Modal clientes */
#modalBusquedaClientes .modal-content{
    border-radius:.6rem;
}
#modalBusquedaClientes .modal-header{
    border-bottom:1px solid #f3f4f6;
}
#modalBusquedaClientes .modal-footer{
    border-top:1px solid #f3f4f6;
}
#modalBusquedaClientes label{
    font-size:.85rem;
    color:#6b7280;
    font-weight:600;
}
#modalBusquedaClientes .form-control{
    font-size:.9rem;
}
#modalBusquedaClientes .form-text{
    font-size:.75rem;
    color:#9ca3af;
}
</style>
@stop

@section('js')
<script>
(function(){

    const filasOriginal = Array.from(document.querySelectorAll('#tabla-clientes tbody tr'));
    let filasFiltradas = filasOriginal.slice();
    let filasPorPagina = 10;
    let pagina = 1;

    const info   = document.getElementById('clientesInfo');
    const btnPrev = document.getElementById('clientesPrev');
    const btnNext = document.getElementById('clientesNext');
    const selector = document.getElementById('selectFilasClientes');

    function render(){
        const total = filasFiltradas.length;
        const paginas = Math.max(1, Math.ceil(total / filasPorPagina));

        filasOriginal.forEach(f => f.style.display = 'none');

        if (total === 0){
            if (info) info.textContent = '0 resultados';
            if (btnPrev) btnPrev.disabled = true;
            if (btnNext) btnNext.disabled = true;
            return;
        }

        if (pagina > paginas) pagina = paginas;

        const inicio = (pagina - 1) * filasPorPagina;
        const fin    = inicio + filasPorPagina;

        filasFiltradas.slice(inicio, fin).forEach(f => f.style.display = '');

        if (info){
            info.textContent = `Mostrando ${inicio + 1} - ${Math.min(fin, total)} de ${total} clientes (página ${pagina} de ${paginas})`;
        }

        if (btnPrev) btnPrev.disabled = (pagina <= 1);
        if (btnNext) btnNext.disabled = (pagina >= paginas);
    }

    if (selector){
        selector.addEventListener('change', () => {
            filasPorPagina = parseInt(selector.value);
            pagina = 1;
            render();
        });
    }

    if (btnPrev){
        btnPrev.addEventListener('click', () => {
            if (pagina > 1){
                pagina--;
                render();
            }
        });
    }

    if (btnNext){
        btnNext.addEventListener('click', () => {
            pagina++;
            render();
        });
    }

    function aplicarFiltros(){
        const idVal    = (document.getElementById('c_bus_id')?.value || '').trim();
        const nomVal   = (document.getElementById('c_bus_nombre')?.value || '').trim().toLowerCase();
        const telVal   = (document.getElementById('c_bus_telefono')?.value || '').trim().toLowerCase();
        const mailVal  = (document.getElementById('c_bus_correo')?.value || '').trim().toLowerCase();
        const fechaVal = (document.getElementById('c_bus_fecha')?.value || '').trim().toLowerCase();

        filasFiltradas = [];

        filasOriginal.forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length < 6) return;

            const idTxt   = celdas[0].textContent.trim();
            const nomTxt  = celdas[1].textContent.trim().toLowerCase();
            const telTxt  = celdas[2].textContent.trim().toLowerCase();
            const mailTxt = celdas[3].textContent.trim().toLowerCase();
            const fechaTxt= celdas[4].textContent.trim().toLowerCase();

            let ok = true;

            if (idVal && idTxt !== idVal) ok = false;
            if (ok && nomVal && !nomTxt.includes(nomVal)) ok = false;
            if (ok && telVal && !telTxt.includes(telVal)) ok = false;
            if (ok && mailVal && !mailTxt.includes(mailVal)) ok = false;
            if (ok && fechaVal && !fechaTxt.includes(fechaVal)) ok = false;

            if (ok) filasFiltradas.push(fila);
        });

        pagina = 1;
        render();
    }

    const formBusqueda = document.getElementById('formBusquedaClientes');
    const btnLimpiar   = document.getElementById('c_btn_limpiar');

    if (formBusqueda){
        formBusqueda.addEventListener('submit', e => {
            e.preventDefault();
            aplicarFiltros();
            if (window.$){
                $('#modalBusquedaClientes').modal('hide');
            }
        });
    }

    if (btnLimpiar){
        btnLimpiar.addEventListener('click', e => {
            e.preventDefault();
            if (formBusqueda) formBusqueda.reset();
            filasFiltradas = filasOriginal.slice();
            pagina = 1;
            render();
        });
    }

    // Render inicial
    render();

})();
</script>
@stop
