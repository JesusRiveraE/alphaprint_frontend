@extends('adminlte::page')

@section('title','Editar Cliente')

@section('content_header')
<h1 class="m-0"><i class="fas fa-user-edit mr-2 brand-text"></i> Editar Cliente</h1>
@stop

@section('content')
<div class="card card-soft">
    <div class="card-header border-brand">
        <strong class="brand-text">Actualizar Datos</strong>
    </div>
    <div class="card-body">
        <form action="{{ route('clientes.update', $cliente['id_cliente']) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="label-brand">Nombre</label>
                    <input type="text" name="nombre" value="{{ $cliente['nombre'] ?? '' }}" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label class="label-brand">Teléfono</label>
                    <input type="text" name="telefono" value="{{ $cliente['telefono'] ?? '' }}" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label class="label-brand">Correo</label>
                    <input type="email" name="correo" value="{{ $cliente['correo'] ?? '' }}" class="form-control">
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-secondary mr-2">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-sm btn-brand">
                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
:root{ --brand:#e24e60; --brand-600:#cc4656; }
.brand-text{ color: var(--brand); }
.card-soft{ border:1px solid #eff1f5; border-radius:.6rem; }
.border-brand{ border-left:4px solid var(--brand); background:#fff; }
.label-brand{ font-weight:600; color:#4b5563; }
.btn-brand{ background:var(--brand); border-color:var(--brand); color:#fff; }
.btn-brand:hover{ background:var(--brand-600); border-color:var(--brand-600); }
</style>
@stop
