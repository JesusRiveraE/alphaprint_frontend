@extends('adminlte::page')

@section('title','Nuevo Cliente')

@section('content_header')
<h1 class="m-0"><i class="fas fa-user-plus mr-2 brand-text"></i> Nuevo Cliente</h1>
@stop

@section('content')
<div class="card card-soft">
    <div class="card-header border-brand">
        <strong class="brand-text">Datos del Cliente</strong>
    </div>
    <div class="card-body">
        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="label-brand">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label class="label-brand">Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label class="label-brand">Correo</label>
                    <input type="email" name="correo" class="form-control">
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-secondary mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
                <button type="submit" class="btn btn-sm btn-brand">
                    <i class="fas fa-save mr-1"></i> Guardar
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
