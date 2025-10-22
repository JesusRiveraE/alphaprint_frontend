@extends('adminlte::page')

@section('title', 'Detalle Cliente')

@section('content_header')
    <h1>Detalle del Cliente</h1>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body">
        <p><strong>ID:</strong> {{ $cliente['id_cliente'] }}</p>
        <p><strong>Nombre:</strong> {{ $cliente['nombre'] }}</p>
        <p><strong>Teléfono:</strong> {{ $cliente['telefono'] ?? '—' }}</p>
        <p><strong>Correo:</strong> {{ $cliente['correo'] ?? '—' }}</p>

        <div class="mt-4">
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="{{ route('clientes.reporte', $cliente['id_cliente']) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Generar PDF
            </a>
        </div>
    </div>
</div>
@stop
