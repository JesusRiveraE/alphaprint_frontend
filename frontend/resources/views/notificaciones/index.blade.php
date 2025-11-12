@extends('adminlte::page')

@section('title', 'Notificaciones')

@section('content_header')
<h1 class="m-0" style="font-size:1.7rem;">
    <i class="fas fa-bell mr-2 brand-text"></i> Notificaciones del Sistema
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex justify-content-between align-items-center">
        <strong class="brand-text" style="font-size:1.25rem;">Listado de Notificaciones</strong>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
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
                                        {{ \Carbon\Carbon::parse($item['fecha'])->timezone('America/Tegucigalpa')->format('d/m/Y H:i:s') }}
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
</style>
@stop

@section('js')
<script>
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
                body: JSON.stringify({}) // tu backend no requiere body, pero dejamos JSON válido
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
</script>
@stop
