@extends('adminlte::page')

@section('title', 'Registrar Cliente')

@section('content_header')
    <h1>Nuevo Cliente</h1>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body">
        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label><strong>Nombre</strong></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label><strong>Teléfono</strong></label>
                <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control">
            </div>
            <div class="form-group mb-3">
                <label><strong>Correo</strong></label>
                <input type="email" name="correo" value="{{ old('correo') }}" class="form-control">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
@stop
