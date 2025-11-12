@extends('adminlte::page')

@section('title', 'Bitácora')

@section('content_header')
    <h1 class="m-0" style="font-size:1.7rem;">
        <i class="fas fa-clipboard-list mr-2 brand-text"></i> Bitácora del Sistema
    </h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex justify-content-between align-items-center">
        <strong class="brand-text" style="font-size:1.25rem;">Últimos movimientos</strong>
        <small class="text-muted">Fuente: API</small>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-brand">
                    <tr>
                        <th style="width:90px;">ID</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th style="width:200px;">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bitacora as $it)
                        @php
                            // Formato estándar con zona horaria UTC-6
                            $fmt = '';
                            try {
                                if (!empty($it['fecha'])) {
                                    $fmt = \Carbon\Carbon::parse($it['fecha'], 'UTC')
                                        ->setTimezone('-06:00')
                                        ->format('d/m/Y H:i:s');
                                }
                            } catch (\Throwable $e) { $fmt = $it['fecha'] ?? ''; }
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $it['id_bitacora'] ?? '—' }}</td>
                            <td><strong>{{ $it['usuario'] ?? 'Sistema' }}</strong></td>
                            <td>{{ $it['modulo'] ?? '—' }}</td>
                            <td>{{ $it['accion'] ?? '—' }}</td>
                            <td>
                                <span class="badge badge-chip">{{ $fmt ?: '—' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">— Sin registros —</td>
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
:root{ --brand:#e24e60; --brand-100:#fde5e9; --ink:#2b2f33; }

.brand-text{ color:var(--brand); }
.card-soft{ border:1px solid #eff1f5; border-radius:.6rem; }
.card-soft:hover{ box-shadow:0 0 15px rgba(226,78,96,.08); }
.border-brand{ border-left:4px solid var(--brand); background:#fff; }
.thead-brand th{ border-bottom:2px solid var(--brand)!important; color:#4b5563; font-weight:700; }
.badge-chip{ background:var(--brand-100); color:var(--brand); font-weight:600; border-radius:999px; padding:.35rem .6rem; }
</style>
@stop
