@extends('adminlte::page')

@section('title', 'Valoraciones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Listado de Valoraciones</h1>
        <div>
            <a href="{{ route('valoraciones.create') }}" class="btn btn-success me-2">
                <i class="fas fa-plus-circle"></i> Nueva Valoración
            </a>
            <a href="{{ route('valoraciones.reporte') }}" target="_blank" class="btn btn-secondary">
                <i class="fas fa-file-pdf"></i> Generar Reporte
            </a>
        </div>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Puntuación</th>
                        <th>Comentario</th>
                        <th>Fecha de Creación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($valoraciones as $valoracion)
                        <tr>
                            <td>{{ $valoracion['id_valoracion'] ?? '—' }}</td>
                            <td>
                                @php
                                    $score = $valoracion['puntuacion'] ?? 0;
                                @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $score ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </td>
                            <td>{{ $valoracion['comentario'] ?? '—' }}</td>
                            <td>
                                @php
                                    // Intentamos detectar el nombre real del campo de fecha
                                    $fecha = $valoracion['fecha_creacion']
                                        ?? $valoracion['fecha']
                                        ?? $valoracion['fecha_valoracion']
                                        ?? null;
                                @endphp

                                {{ $fecha
                                    ? \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i')
                                    : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-star-half-alt fa-2x mb-2"></i>
                                <p class="mb-0">No hay valoraciones registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
