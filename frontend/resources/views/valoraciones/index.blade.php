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
        <a href="{{ route('valoraciones.reporte') }}" class="btn btn-sm btn-brand-outline">
            <i class="fas fa-file-pdf mr-1"></i> Reporte PDF
        </a>
    </div>
</div>
@stop

@section('content')
<div class="card card-soft">
    <div class="card-header border-brand d-flex justify-content-between align-items-center">
        <strong class="brand-text">Listado de Valoraciones</strong>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
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
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $item['id_valoracion'] ?? $item->id_valoracion ?? '' }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }} px-3 py-1">
                                    {{ $score }} / 5
                                </span>
                            </td>
                            <td>{{ $item['comentario'] ?? $item->comentario ?? '—' }}</td>
                            <td>
                                @php
                                    $fechaRaw = $item['fecha'] ?? $item->fecha ?? null;
                                @endphp
                                <span class="badge badge-chip">
                                    {{ $fechaRaw ? \Carbon\Carbon::parse($fechaRaw, 'UTC')->setTimezone('-06:00')->format('d/m/Y H:i') : '—' }}
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
</style>
@stop
