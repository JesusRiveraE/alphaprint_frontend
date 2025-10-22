@extends('adminlte::page')

@section('title', 'Crear Pedido')

@section('content_header')
    <h1>Crear Pedido</h1>
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
            <form method="POST" action="{{ route('pedidos.store') }}">
                @csrf

                <div class="form-group">
                    <label for="id_cliente">Cliente</label>
                    <select name="id_cliente" id="id_cliente" class="form-control" required>
                        <option value="">-- Selecciona un cliente --</option>
                        @foreach ($clientes as $c)
                            <option value="{{ $c['id_cliente'] ?? $c['id'] }}"
                                {{ old('id_cliente') == ($c['id_cliente'] ?? $c['id']) ? 'selected' : '' }}>
                                {{ $c['nombre'] ?? ('Cliente #' . ($c['id_cliente'] ?? $c['id'])) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mt-2">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" id="descripcion" class="form-control" maxlength="255"
                           value="{{ old('descripcion') }}">
                </div>

                <div class="form-group mt-2">
                    <label for="total">Total (Lps)</label>
                    <input type="number" name="total" id="total" step="0.01" min="0" class="form-control"
                           value="{{ old('total') }}" required>
                </div>

                <div class="form-group mt-2">
                    <label for="fecha_entrega">Fecha de Entrega (opcional)</label>
                    <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control"
                           value="{{ old('fecha_entrega') }}">
                </div>

                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop
