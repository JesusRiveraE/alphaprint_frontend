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
        <div class="ml-auto">
            <a href="{{ route('pedidos.create') }}" class="btn btn-sm btn-brand-outline">
                <i class="fas fa-plus mr-1"></i> Nuevo Pedido
            </a>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-striped text-sm align-middle">
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

                        {{-- 🔽 Estado con dropdown para cambiar en vivo --}}
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

                            {{-- ✅ Eliminar seguro con formulario DELETE --}}
                            <form action="{{ route('pedidos.destroy', $item['id_pedido']) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline-danger btn-delete">
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
    </div>
</div>
@stop

@section('css')
<style>
:root{ --brand:#e24e60; --brand-100:#fde5e9; }
.brand-text{ color:var(--brand); }
.card-soft{ border:1px solid #eff1f5; border-radius:.6rem; }
.card-soft:hover{ box-shadow:0 0 15px rgba(226,78,96,.08); }
.border-brand{ border-left:4px solid var(--brand); background:#fff; }
.badge-chip{ background:var(--brand-100); color:var(--brand); font-weight:600; border-radius:999px; padding:.35rem .6rem; }
.btn-brand-outline{ border:1px solid var(--brand); color:var(--brand); background:#fff; }
.btn-brand-outline:hover{ background:#fde5e9; color:var(--brand); }
.dropdown-item{ font-size:.9rem; }
</style>
@stop

@section('js')
<script>
(function(){
    const csrf = '{{ csrf_token() }}';

    // Cambiar estado con AJAX
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

    // Confirmar eliminación
    document.querySelectorAll('.delete-form').forEach(f=>{
        f.addEventListener('submit', (e)=>{
            if(!confirm('¿Seguro que deseas eliminar este pedido?')) {
                e.preventDefault();
            }
        });
    });
})();
</script>
@stop
