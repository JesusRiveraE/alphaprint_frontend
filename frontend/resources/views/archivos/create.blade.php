@extends('adminlte::page')

@section('title', 'Agregar Archivo')

@section('content_header')
    <h1>Agregar Archivo</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card card-outline card-primary">
            <div class="card-body">
                <form action="{{ route('archivos.store') }}" method="POST">
                    @csrf

                    <!-- Pedido al que se ancla -->
                    <div class="form-group">
                        <label for="id_pedido">Pedido</label>
                        <select name="id_pedido" id="id_pedido" class="form-control" required>
                            <option value="" disabled selected>Seleccione un pedido...</option>
                            @foreach($pedidos as $p)
                                <option value="{{ $p['id_pedido'] }}">
                                    #{{ $p['id_pedido'] }} — {{ $p['descripcion'] ?? 'Sin descripción' }}
                                    @if(!empty($p['cliente_nombre'])) ({{ $p['cliente_nombre'] }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- URL del archivo (Drive, S3, etc.) -->
                    <div class="form-group">
                        <label for="url">URL del Archivo</label>
                        <input type="url" name="url" id="url" class="form-control" placeholder="https://..." required>
                        <small class="form-text text-muted">Puedes pegar el enlace de Google Drive/OneDrive/etc.</small>
                    </div>

                    <!-- Comentario opcional -->
                    <div class="form-group">
                        <label for="comentario">Comentario (opcional)</label>
                        <textarea name="comentario" id="comentario" class="form-control" rows="2" placeholder="Notas o detalles del archivo..."></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('archivos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
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