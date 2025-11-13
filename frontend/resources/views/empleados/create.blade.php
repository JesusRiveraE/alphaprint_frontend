@extends('adminlte::page')

@section('title','Nuevo Empleado')

@section('content_header')
<h1 class="m-0 page-title">
    <i class="fas fa-user-plus mr-2 brand-text"></i> Nuevo Empleado
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex align-items-center justify-content-between">
        <strong class="brand-text mb-0">
            <i class="fas fa-id-badge mr-2"></i> Datos del Empleado
        </strong>

        {{-- 🔹 Botón para volver a la tabla de empleados --}}
        <a href="{{ route('empleados.index') }}" class="btn btn-sm btn-brand-outline">
            <i class="fas fa-list mr-1"></i> Ver listado
        </a>
    </div>

    <div class="card-body">
        {{-- (Opcional) Mensajes de error de validación --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <hr>
        @endif

        <form action="{{ route('empleados.store') }}" method="POST">
            @csrf
            <input type="hidden" name="firebase_token" id="firebase_token">


            <div class="form-row">
                {{-- NOMBRE (PERSONAL.nombre) --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        value="{{ old('nombre') }}"
                        class="form-control"
                        required
                    >
                </div>

                {{-- TELEFONO (PERSONAL.telefono) --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Teléfono</label>
                    <input
                        type="text"
                        name="telefono"
                        value="{{ old('telefono') }}"
                        class="form-control"
                    >
                </div>

                {{-- AREA (ENUM de PERSONAL.area) --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Área</label>
                    @php
                        // Deben coincidir EXACTAMENTE con el ENUM y con M4_* (área válida) 
                        $areasEnum = [
                            'Diseño gráfico',
                            'Sala de ventas',
                            'Taller de encuadernación',
                            'Taller corte CNC',
                            'Taller de corte y soldadura',
                            'Taller de acabados',
                            'Otro',
                        ];
                        $areaActual = old('area');
                    @endphp
                    <select name="area" class="form-control custom-select" required>
                        <option value="">Seleccione un área...</option>
                        @foreach($areasEnum as $area)
                            <option value="{{ $area }}"
                                {{ $areaActual === $area ? 'selected' : '' }}>
                                {{ $area }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- USUARIO ASOCIADO (PERSONAL.id_usuario -> USUARIOS.id_usuario) --}}
<div class="form-group col-md-6">
    <label class="label-brand">Usuario asociado</label>

    <select
        id="id_usuario-select"
        name="id_usuario"
        class="form-control custom-select"
        required
    >
        <option value="">Cargando usuarios...</option>
    </select>

    <small class="form-text text-muted">
        Los usuarios se crean en el módulo USUARIOS. Aquí solo se asocian al empleado.
    </small>
</div>

            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('empleados.index') }}" class="btn btn-sm btn-outline-secondary mr-2">
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
:root{
    --brand:#e24e60;
    --brand-100:#fde5e9;
    --brand-600:#cc4656;
    --ink:#2b2f33;
}
.page-title{
    font-size:1.7rem;
    color:#1f2937;
}
.brand-text{
    color: var(--brand);
}
.card-soft{
    border:1px solid #eff1f5;
    border-radius:.6rem;
}
.card-soft.shadow-sm:hover{
    box-shadow:0 0 15px rgba(226,78,96,.08);
}
.border-brand{
    border-left:4px solid var(--brand);
    background:#fff;
}
.label-brand{
    font-weight:600;
    color:#4b5563;
}
.btn-brand{
    background:var(--brand);
    border-color:var(--brand);
    color:#fff;
    font-weight:600;
}
.btn-brand:hover{
    background:var(--brand-600);
    border-color:var(--brand-600);
}
.btn-brand-outline{
    border:1px solid var(--brand);
    color:var(--brand);
    background:#fff;
    font-weight:600;
}
.btn-brand-outline:hover{
    background:var(--brand-100);
    color:var(--brand);
}
</style>
@push('js')
<script type="module">
    import { authReady, authorizedFetch, getIdToken } from "{{ asset('js/firebase.js') }}";

    async function cargarUsuariosEnFormulario() {
        const select = document.getElementById('id_usuario-select');
        if (!select) return;

        select.innerHTML = '<option value="">Cargando usuarios...</option>';

        try {
            await authReady;
            const response = await authorizedFetch('http://localhost:3000/api/usuarios');
            const data = await response.json();

            if (!response.ok) {
                console.error('Error al obtener usuarios:', data);
                select.innerHTML = '<option value="">Error al cargar usuarios</option>';
                return;
            }

            const usuarios = Array.isArray(data) ? data : [];

            if (!usuarios.length) {
                select.innerHTML = '<option value="">No hay usuarios disponibles</option>';
                return;
            }

            select.innerHTML = '<option value="">Seleccione un usuario...</option>';

            usuarios.forEach((u) => {
                const id = u.id_usuario ?? u.id;
                const nombre = u.nombre_usuario ?? u.nombre ?? u.correo ?? 'Usuario sin nombre';
                const correo = u.correo ?? '';
                const rol = u.rol ?? '';

                if (!id) return;

                const option = document.createElement('option');
                option.value = id;
                option.textContent =
                    nombre +
                    (correo ? ' - ' + correo : '') +
                    (rol ? ' (' + rol + ')' : '');

                select.appendChild(option);
            });
        } catch (err) {
            console.error('Error cargando usuarios en select:', err);
            select.innerHTML = '<option value="">Error al cargar usuarios</option>';
        }
    }

    async function rellenarTokenHidden() {
        const inputToken = document.getElementById('firebase_token');
        if (!inputToken) return;

        try {
            const token = await getIdToken();
            inputToken.value = token;
        } catch (err) {
            console.error('No se pudo obtener el token de Firebase:', err);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Cargamos usuarios y rellenamos el token al cargar la página
        rellenarTokenHidden();
        cargarUsuariosEnFormulario();
    });
</script>
@endpush



@stop