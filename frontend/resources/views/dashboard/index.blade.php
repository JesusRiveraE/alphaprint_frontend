@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard General</h1>
@stop

@section('content')
<div class="row">
    <!-- Tarjetas de métricas -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalPedidos }}</h3>
                <p>Pedidos</p>
            </div>
            <div class="icon"><i class="fas fa-box"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalClientes }}</h3>
                <p>Clientes</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalUsuarios }}</h3>
                <p>Usuarios</p>
            </div>
            <div class="icon"><i class="fas fa-user"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalEmpleados }}</h3>
                <p>Empleados</p>
            </div>
            <div class="icon"><i class="fas fa-user-tie"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Valoraciones -->
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Valoraciones</h3>
            </div>
            <div class="card-body">
                <h4>Total: {{ $totalValoraciones }}</h4>
            </div>
        </div>
    </div>

    <!-- Notificaciones -->
    <div class="col-md-4">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Notificaciones</h3>
            </div>
            <div class="card-body">
                <h4>Total: {{ $totalNotificaciones }}</h4>
            </div>
        </div>
    </div>

    <!-- Pedidos por estado -->
    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Pedidos por Estado</h3>
            </div>
            <div class="card-body">
                <p>Pendientes: {{ $pedidosPendientes }}</p>
                <p>En Progreso: {{ $pedidosProgreso }}</p>
                <p>Completados: {{ $pedidosCompletados }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Últimos registros en Bitácora -->
    <div class="col-md-12">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Últimos movimientos (Bitácora)</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($bitacora, 0, 5) as $registro)
                            <tr>
                                <td>{{ $registro['id_bitacora'] ?? '' }}</td>
                                <td>{{ $registro['modulo'] ?? '' }}</td>
                                <td>{{ $registro['accion'] ?? '' }}</td>
                                <td>{{ $registro['nombre_usuario'] ?? '---' }}</td>
                                <td>{{ $registro['fecha'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
