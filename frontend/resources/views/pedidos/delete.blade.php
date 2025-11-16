@extends('adminlte::page')

@section('title','Eliminar Pedido')

@section('content_header')
    <h1 class="m-0" style="font-size:1.7rem;">
        <i class="fas fa-trash-alt mr-2 brand-text"></i> Eliminar Pedido
    </h1>
@stop

@section('content')
<div class="modal-page-wrapper d-flex justify-content-center align-items-center">
    <div class="card card-soft shadow-lg modal-confirm-card">
        <div class="card-header border-brand d-flex align-items-center">
            <strong class="brand-text" style="font-size:1.1rem;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Confirmar eliminación
            </strong>
        </div>

        <div class="card-body text-center">

            {{-- Icono circular --}}
            <div class="icon-circle mb-3">
                <i class="fas fa-trash-alt"></i>
            </div>

            <p class="mb-1">
                ¿Seguro que deseas eliminar el pedido
                <strong>#{{ $pedido['id_pedido'] }}</strong>
                del cliente
                <strong>{{ $pedido['cliente_nombre'] ?? $pedido['id_cliente'] }}</strong>?
            </p>

            <p class="text-muted small mb-3">
                Esta acción es permanente y no podrás recuperar este pedido después de eliminarlo.
            </p>

            <div class="alert alert-warning alert-warning-soft mb-4">
                <i class="fas fa-info-circle mr-1"></i>
                Revisa que estás eliminando el pedido correcto antes de continuar.
            </div>

            <form method="POST" action="{{ route('pedidos.destroy', $pedido['id_pedido']) }}">
                @csrf
                @method('DELETE')

                <div class="d-flex justify-content-end">
                    <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary mr-2">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-danger-brand">
                        <i class="fas fa-trash mr-1"></i> Eliminar definitivamente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
:root{
    --brand:#e24e60;
    --brand-100:#fde5e9;
    --brand-600:#c23c4e;
    --ink:#2b2f33;
}

/* Texto de marca */
.brand-text{ color:var(--brand); }

/* Card suave */
.card-soft{
    border:1px solid #eff1f5;
    border-radius:.75rem;
    background:#ffffff;
}
.card-soft.shadow-lg{
    box-shadow:0 15px 35px rgba(15,23,42,0.18);
}

/* Borde lateral rojo en el header */
.border-brand{
    border-left:4px solid var(--brand);
    background:#fff;
}

/* Contenedor tipo modal centrado */
.modal-page-wrapper{
    min-height: calc(100vh - 130px); /* para centrar dentro del contenido */
}

/* Tamaño máximo de la tarjeta */
.modal-confirm-card{
    max-width:560px;
    width:100%;
}

/* Icono circular */
.icon-circle{
    width:72px;
    height:72px;
    border-radius:50%;
    background:var(--brand-100);
    color:var(--brand);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.8rem;
    margin:0 auto;
}

/* Alerta suave */
.alert-warning-soft{
    background:#fff7e6;
    border-color:#ffe0a6;
    color:#92400e;
    font-size:.9rem;
}

/* Botón rojo de marca */
.btn-danger-brand{
    background:var(--brand);
    border-color:var(--brand);
    color:#fff;
    font-weight:600;
    border-radius:.5rem;
    padding:.45rem 1.2rem;
    transition:
        background-color .2s ease,
        box-shadow .15s ease,
        transform .15s ease;
}
.btn-danger-brand:hover{
    background:var(--brand-600);
    border-color:var(--brand-600);
    color:#fff;
    box-shadow:0 6px 14px rgba(226,78,96,0.35);
    transform:translateY(-1px);
}
</style>
@stop
