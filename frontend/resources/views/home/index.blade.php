@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <h1 class="m-0 page-title">
        <i class="fas fa-home mr-2 brand-text"></i> Inicio
    </h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand">
        <strong class="brand-text">Módulos principales</strong>
    </div>
    <div class="card-body">
        <div class="row">

            {{-- Notificaciones --}}
            <div class="col-md-4 mb-3">
                <a href="{{ route('notificaciones.index') }}" class="btn btn-block btn-brand-outline py-3">
                    <i class="fas fa-bell fa-2x mb-2 d-block"></i>
                    Notificaciones
                </a>
            </div>

            {{-- Pedidos --}}
            <div class="col-md-4 mb-3">
                <a href="{{ route('pedidos.index') }}" class="btn btn-block btn-brand-outline py-3">
                    <i class="fas fa-box fa-2x mb-2 d-block"></i>
                    Pedidos
                </a>
            </div>

            {{-- Archivos --}}
            <div class="col-md-4 mb-3">
                <a href="{{ route('archivos.index') }}" class="btn btn-block btn-brand-outline py-3">
                    <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                    Archivos
                </a>
            </div>

            {{-- Calendario --}}
            <div class="col-md-6 mb-3">
                <a href="{{ route('calendario.index') }}" class="btn btn-block btn-brand-outline py-3">
                    <i class="fas fa-calendar-alt fa-2x mb-2 d-block"></i>
                    Calendario
                </a>
            </div>

            {{-- Historial de estados de pedidos --}}
            <div class="col-md-6 mb-3">
                <a href="{{ route('historial.index') }}" class="btn btn-block btn-brand-outline py-3">
                    <i class="fas fa-history fa-2x mb-2 d-block"></i>
                    Historial de estados
                </a>
            </div>

        </div>
    </div>
</div>
@stop

@section('css')
<style>
:root{
    --brand:#e24e60;
    --brand-100:#fde5e9;
    --ink:#2b2f33;
}
.page-title{
    color:var(--ink);
    font-weight:700;
}
.brand-text{ color:var(--brand); }

.card-soft{
    border:1px solid #eff1f5;
    border-radius:.6rem;
}
.card-soft:hover{
    box-shadow:0 0 15px rgba(226,78,96,.08);
}
.border-brand{
    border-left:4px solid var(--brand);
}
.btn-brand-outline{
    border:1px solid var(--brand);
    color:var(--brand);
    background:#fff;
    border-radius:.75rem;
    font-weight:600;
}
.btn-brand-outline:hover{
    background:var(--brand-100);
    color:var(--brand);
}
</style>
@stop
