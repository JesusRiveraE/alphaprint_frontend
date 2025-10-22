@extends('adminlte::page')

@section('title', 'Editar Pedido')

@section('content_header')
    <h1>Editar Pedido #{{ $pedido['id_pedido'] ?? '' }}</h1>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('pedidos.update', $pedido['id_pedido']) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="id_cliente">Cliente</label>
                    <select name="id_cliente" id="id_cliente" class="form-control" required>
                        @foreach ($clientes as $c)
                            @php
                                $cid = $c['id_cliente'] ?? $c['id'];
                                $cname = $c['nombre'] ?? ('Cliente #' . $cid);
                                $sel = old('id_cliente', $pedido['id_cliente']) == $cid ? 'selected' : '';
                            @endphp
                            <option value="{{ $cid }}" {{ $sel }}>{{ $cname }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mt-2">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" id="descripcion" class="form-control" maxlength="255"
                           value="{{ old('descripcion', $pedido['descripcion'] ?? '') }}">
                </div>

                <div class="form-group mt-2">
                    <label for="total">Total (Lps)</label>
                    <input type="number" name="total" id="total" step="0.01" min="0" class="form-control"
                           value="{{ old('total', $pedido['total'] ?? 0) }}" required>
                </div>

                <div class="form-group mt-2">
                    <label for="fecha_entrega">Fecha de Entrega</label>
                    <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control"
                           value="{{ old('fecha_entrega', $pedido['fecha_entrega'] ?? '') }}">
                </div>

                <div class="form-group mt-3">
                    <label for="estado">Estado</label>
                    @php
                        $estadoActual = old('estado', $pedido['estado'] ?? 'Pendiente');
                    @endphp
                    <select name="estado" id="estado" class="form-control">
                        <option value="Pendiente" {{ $estadoActual === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="En Progreso" {{ $estadoActual === 'En Progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="Completado" {{ $estadoActual === 'Completado' ? 'selected' : '' }}>Completado</option>
                    </select>
                    <small class="form-text text-muted">
                        Si cambias el estado, se registrará notificación e historial (según tus triggers).
                    </small>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop
