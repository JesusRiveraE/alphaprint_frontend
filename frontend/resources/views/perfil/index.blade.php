@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content_header')
    <h1 class="m-0">
        <i class="fas fa-user-circle text-primary"></i> Mi Perfil
    </h1>
@stop

@section('content')

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Información del Usuario</h3>
    </div>

    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-3 text-center">
                <i class="fas fa-user-circle fa-5x text-primary"></i>
            </div>

            <div class="col-md-9">
                <p><strong>Nombre:</strong> {{ $usuario['displayName'] ?? 'No disponible' }}</p>
                <p><strong>Email:</strong> {{ $usuario['email'] ?? 'Sin correo' }}</p>
                <p><strong>UID (Firebase):</strong> {{ $usuario['uid'] ?? 'No disponible' }}</p>
            </div>
        </div>

        <hr>

        <h5 class="text-primary">Sesión</h5>
        <p>Sesión iniciada: <strong>{{ now()->format('d/m/Y h:i A') }}</strong></p>

    </div>
</div>

@stop
