@extends('adminlte::page')

@section('title', 'Eliminar Pedido')

@section('content_header')
    <h1>Eliminar Pedido #{{ $pedido['id_pedido'] ?? '' }}</h1>
@stop

@section('content')
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        ¿Seguro que deseas eliminar este pedido? Esta acción no se puede deshacer.
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Cliente</dt>
                <dd class="col-sm-9">{{ $pedido['cliente_nombre'] ?? ('ID ' . ($pedido['id_cliente'] ?? '')) }}</dd>

                <dt class="col-sm-3">Descripción</dt>
                <dd class="col-sm-9">{{ $pedido['descripcion'] ?? '' }}</dd>

                <dt class="col-sm-3">Total</dt>
                <dd class="col-sm-9">L. {{ number_format($pedido['total'] ?? 0, 2) }}</dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">{{ $pedido['estado'] ?? '' }}</dd>

                <dt class="col-sm-3">Fecha entrega</dt>
                <dd class="col-sm-9">{{ $pedido['fecha_entrega'] ?? '—' }}</dd>
            </dl>

            <form method="POST" action="{{ route('pedidos.destroy', $pedido['id_pedido']) }}">
                @csrf
                @method('DELETE')

                <div class="d-flex gap-2">
                    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop
