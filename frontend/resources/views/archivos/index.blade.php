@extends('adminlte::page')

@section('title', 'Archivos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Listado de Archivos</h1>
        <a href="{{ route('archivos.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Agregar Archivo
        </a>
    </div>
@stop

@section('content')
<div class="card card-outline card-primary">
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Pedido</th>
                        <th>Descripción Pedido</th>
                        <th>Estado Pedido</th>
                        <th>URL</th>
                        <th>Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archivos as $a)
                        <tr>
                            <td>{{ $a['id_archivo'] ?? '' }}</td>
                            <td>{{ $a['id_pedido'] ?? '' }}</td>
                            <td>{{ $a['pedido_descripcion'] ?? '-' }}</td>
                            <td>
                                @php $estado = $a['pedido_estado'] ?? '-'; @endphp
                                <span class="badge
                                    @if($estado==='Pendiente') bg-warning
                                    @elseif($estado==='En Progreso') bg-info
                                    @elseif($estado==='Completado') bg-success
                                    @else bg-secondary @endif">
                                    {{ $estado }}
                                </span>
                            </td>
                            <td>
                                @if(!empty($a['url']))
                                    <a href="{{ $a['url'] }}" target="_blank">Abrir</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $a['comentario'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Sin archivos registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
@section('css')
<style>
    /* Cambia el color principal (bordes y encabezados de cards primary) */
    .card-primary:not(.card-outline) > .card-header {
        background-color: #e24e60 !important;
        border-bottom-color: #e24e60 !important;
    }

    .card-primary.card-outline {
        border-top: 3px solid #e24e60 !important;
    }

    .card-primary.card-outline .card-header {
        border-color: #e24e60 !important;
        color: #e24e60 !important;
    }

    /* También aplica a botones o cajas si quieres mantener coherencia */
    .btn-primary, .bg-primary, .badge-primary {
        background-color: #e24e60 !important;
        border-color: #e24e60 !important;
    }
    .text-primary {
        color: #e24e60 !important;
    }

    /* Gráficos, íconos y acentos del dashboard */
    .card-outline.card-primary h3, 
    .card-outline.card-primary .card-title i {
        color: #e24e60 !important;
    }
</style>
@stop