@extends('adminlte::page')

@section('title','Eliminar Pedido')

@section('content_header')
<h1 class="m-0" style="font-size:1.7rem;">
    <i class="fas fa-trash-alt mr-2 brand-text"></i> Eliminar Pedido
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand">
        <strong class="brand-text" style="font-size:1.25rem;">Confirmar eliminación</strong>
    </div>
    <div class="card-body">
        <p>¿Seguro que deseas eliminar el pedido <strong>#{{ $pedido['id_pedido'] }}</strong> del cliente
            <strong>{{ $pedido['cliente_nombre'] ?? $pedido['id_cliente'] }}</strong>?</p>

        <form method="POST" action="{{ route('pedidos.destroy', $pedido['id_pedido']) }}">
            @csrf
            @method('DELETE')
            <div class="d-flex justify-content-end">
                <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary mr-2">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
:root{ --brand:#e24e60; }
.brand-text{ color:var(--brand); }
.card-soft{ border:1px solid #eff1f5; border-radius:.6rem; }
.border-brand{ border-left:4px solid var(--brand); background:#fff; }
</style>
@stop
