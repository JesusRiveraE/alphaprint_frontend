@extends('adminlte::page')

@section('title','Detalle de Pedido')

@section('content_header')
<h1 class="m-0" style="font-size:1.7rem;">
    <i class="fas fa-box mr-2 brand-text"></i> Detalle de Pedido
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex justify-content-between align-items-center">
        <strong class="brand-text" style="font-size:1.25rem;">Información General</strong>
        <div class="ml-auto text-right">
            <a href="{{ route('pedidos.edit', $pedido['id_pedido']) }}" class="btn btn-sm btn-outline-primary mr-2">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('pedidos.reporte', $pedido['id_pedido']) }}" class="btn btn-sm btn-brand-outline">
                <i class="fas fa-file-pdf"></i> Reporte
            </a>
        </div>
    </div>

    <div class="card-body" style="font-size:1.1rem; line-height:1.6;">

        @php
            // Normalizamos y formateamos entrega si viene en ISO/UTC o solo fecha
            $entregaRaw = $pedido['fecha_entrega'] ?? null;
            $entregaFmt = $entregaRaw
                ? \Carbon\Carbon::parse($entregaRaw, 'UTC')
                    ->timezone('America/Tegucigalpa')
                    ->format('d/m/Y H:i:s')
                : null;
        @endphp

        <div class="row">
            <div class="col-md-4 mb-4">
                <span class="text-muted d-block" style="font-size:1rem;">ID Pedido</span>
                <strong style="font-size:1.2rem;">{{ $pedido['id_pedido'] }}</strong>
            </div>
            <div class="col-md-4 mb-4">
                <span class="text-muted d-block" style="font-size:1rem;">Cliente</span>
                <strong style="font-size:1.2rem;">{{ $pedido['cliente_nombre'] ?? $pedido['id_cliente'] }}</strong>
            </div>
            <div class="col-md-4 mb-4">
                <span class="text-muted d-block" style="font-size:1rem;">Estado</span>
                <span class="badge
                    @if(($pedido['estado'] ?? '')==='Pendiente') bg-warning
                    @elseif(($pedido['estado'] ?? '')==='En Progreso') bg-info
                    @else bg-success @endif" style="font-size:1rem;">
                    {{ $pedido['estado'] ?? '—' }}
                </span>
            </div>

            <div class="col-md-8 mb-4">
                <span class="text-muted d-block" style="font-size:1rem;">Descripción</span>
                <strong style="font-size:1.2rem;">{{ $pedido['descripcion'] ?? '—' }}</strong>
            </div>
            <div class="col-md-4 mb-4">
                <span class="text-muted d-block" style="font-size:1rem;">Total (Lps)</span>
                <strong style="font-size:1.2rem;">{{ number_format($pedido['total'] ?? 0, 2) }}</strong>
            </div>

            <div class="col-md-6 mb-4">
                <span class="text-muted d-block" style="font-size:1rem;">Fecha de Creación</span>
                <span class="badge badge-chip" style="font-size:1rem;">
                    {{ \Carbon\Carbon::parse($pedido['fecha_creacion'])->timezone('America/Tegucigalpa')->format('d/m/Y H:i:s') }}
                </span>
            </div>
            <div class="col-md-6 mb-4">
                <span class="text-muted d-block" style="font-size:1rem;">Fecha de Entrega</span>
                @if($entregaFmt)
                    <span class="badge badge-chip" style="font-size:1rem;">{{ $entregaFmt }}</span>
                @else
                    <strong style="font-size:1.2rem;">—</strong>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('pedidos.index') }}" class="btn btn-lg btn-outline-secondary" style="font-size:1rem;">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
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
.badge-chip{ background:var(--brand-100); color:var(--brand); font-weight:600; border-radius:999px; padding:.45rem .8rem; }
.btn-brand-outline{ border:2px solid var(--brand); color:var(--brand); background:#fff; }
.btn-brand-outline:hover{ background:#fde5e9; color:var(--brand); }
</style>
@stop
