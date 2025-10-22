@extends('adminlte::page')

@section('title', 'Detalles del Pedido')

@section('content_header')
    <h1>Detalles del Pedido #{{ $pedido['id_pedido'] }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Cliente</dt>
                <dd class="col-sm-9">{{ $pedido['cliente_nombre'] ?? '—' }}</dd>

                <dt class="col-sm-3">Descripción</dt>
                <dd class="col-sm-9">{{ $pedido['descripcion'] ?? '—' }}</dd>

                <dt class="col-sm-3">Total</dt>
                <dd class="col-sm-9">L. {{ number_format($pedido['total'] ?? 0, 2) }}</dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    <span class="badge 
                        {{ $pedido['estado'] === 'Completado' ? 'bg-success' : 
                           ($pedido['estado'] === 'En Progreso' ? 'bg-warning' : 'bg-secondary') }}">
                        {{ $pedido['estado'] }}
                    </span>
                </dd>

                <dt class="col-sm-3">Fecha de creación</dt>
                <dd class="col-sm-9">{{ \Carbon\Carbon::parse($pedido['fecha_creacion'])->format('d/m/Y H:i') }}</dd>

                <dt class="col-sm-3">Fecha de entrega</dt>
                <dd class="col-sm-9">
                    {{ $pedido['fecha_entrega'] ? \Carbon\Carbon::parse($pedido['fecha_entrega'])->format('d/m/Y') : '—' }}
                </dd>
            </dl>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('pedidos.reporte', $pedido['id_pedido']) }}" target="_blank" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Generar PDF
                </a>
            </div>
        </div>
    </div>
@stop
