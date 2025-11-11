@extends('adminlte::page')

@section('title','Detalle de Cliente')

@section('content_header')
<h1 class="m-0" style="font-size: 1.7rem;">
    <i class="fas fa-id-card-alt mr-2 brand-text"></i> Detalle de Cliente
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand">
        <strong class="brand-text" style="font-size: 1.25rem;">Información General</strong>
    </div>
    <div class="card-body" style="font-size: 1.1rem; line-height: 1.6;">
        <div class="row">
            <div class="col-md-6 mb-4">
                <span class="text-muted d-block" style="font-size: 1rem;">ID Cliente</span>
                <strong style="font-size: 1.2rem;">{{ $cliente['id_cliente'] ?? '' }}</strong>
            </div>

            <div class="col-md-6 mb-4">
                <span class="text-muted d-block" style="font-size: 1rem;">Fecha Creación</span>
                <span class="badge badge-chip" style="font-size: 1rem;">
                    {{ \Carbon\Carbon::parse($cliente['fecha_creacion'])
                        ->timezone('America/Tegucigalpa')
                        ->format('d/m/Y H:i:s') }}
                </span>
            </div>

            <div class="col-md-6 mb-4">
                <span class="text-muted d-block" style="font-size: 1rem;">Nombre</span>
                <strong style="font-size: 1.2rem;">{{ $cliente['nombre'] ?? '' }}</strong>
            </div>

            <div class="col-md-6 mb-4">
                <span class="text-muted d-block" style="font-size: 1rem;">Correo</span>
                <strong style="font-size: 1.2rem;">{{ $cliente['correo'] ?? '' }}</strong>
            </div>

            <div class="col-md-6 mb-4">
                <span class="text-muted d-block" style="font-size: 1rem;">Teléfono</span>
                <strong style="font-size: 1.2rem;">{{ $cliente['telefono'] ?? '' }}</strong>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('clientes.reporte', $cliente['id_cliente']) }}" 
               class="btn btn-lg btn-brand-outline mr-2" style="font-size: 1rem;">
                <i class="fas fa-file-pdf mr-1"></i> Reporte
            </a>
            <a href="{{ route('clientes.index') }}" 
               class="btn btn-lg btn-outline-secondary" style="font-size: 1rem;">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
:root {
    --brand: #e24e60;
    --brand-100: #fde5e9;
}
.brand-text {
    color: var(--brand);
}
.card-soft {
    border: 1px solid #eff1f5;
    border-radius: .6rem;
    transition: all 0.2s ease-in-out;
}
.card-soft:hover {
    box-shadow: 0 0 15px rgba(226, 78, 96, 0.15);
}
.border-brand {
    border-left: 4px solid var(--brand);
    background: #fff;
}
.badge-chip {
    background: var(--brand-100);
    color: var(--brand);
    font-weight: 600;
    border-radius: 999px;
    padding: .45rem .8rem;
}
.btn-brand-outline {
    border: 2px solid var(--brand);
    color: var(--brand);
    background: #fff;
    transition: all 0.2s;
}
.btn-brand-outline:hover {
    background: #fde5e9;
    color: var(--brand);
}
</style>
@stop