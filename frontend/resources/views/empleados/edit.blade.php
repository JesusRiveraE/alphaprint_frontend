@extends('adminlte::page')

@section('title','Editar Empleado')

@section('content_header')
<h1 class="m-0 page-title">
    <i class="fas fa-user-edit mr-2 brand-text"></i> Editar Empleado
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex align-items-center justify-content-between">
        <strong class="brand-text mb-0">
            <i class="fas fa-id-badge mr-2"></i> Actualizar Datos
        </strong>

        <a href="{{ route('empleados.index') }}" class="btn btn-sm btn-brand-outline">
            <i class="fas fa-list mr-1"></i> Ver listado
        </a>
    </div>

    <div class="card-body">
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

        @php
            $oldNombre     = old('nombre');
            $oldTelefono   = old('telefono');
            $oldArea       = old('area');
            $oldIdUsuario  = old('id_usuario');
        @endphp

        <form action="{{ route('empleados.update', $empleadoId) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Token que rellenamos desde JS --}}
            <input type="hidden" name="firebase_token" id="firebase_token">

            {{-- ID del empleado para que el JS sepa qué consultar --}}
            <input type="hidden" id="empleado-id" value="{{ $empleadoId }}">

            <div class="form-row">
                {{-- NOMBRE --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        value="{{ $oldNombre }}"
                        class="form-control"
                        required
                    >
                </div>

                {{-- TELEFONO --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Teléfono</label>
                    <input
                        type="text"
                        name="telefono"
                        value="{{ $oldTelefono }}"
                        class="form-control"
                    >
                </div>

                {{-- AREA --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Área</label>
                    @php
                        $areasEnum = [
                            'Diseño gráfico',
                            'Sala de ventas',
                            'Taller de encuadernación',
                            'Taller corte CNC',
                            'Taller de corte y soldadura',
                            'Taller de acabados',
                            'Otro',
                        ];
                    @endphp
                    <select name="area" class="form-control custom-select" required>
                        <option value="">Seleccione un área...</option>
                        @foreach($areasEnum as $area)
                            <option value="{{ $area }}"
                                {{ (string)$oldArea === (string)$area ? 'selected' : '' }}>
                                {{ $area }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- USUARIO ASOCIADO --}}
                <div class="form-group col-md-6">
                    <label class="label-brand">Usuario asociado</label>
                    <select
                        id="id_usuario-select"
                        name="id_usuario"
                        class="form-control custom-select"
                        required
                        data-old="{{ $oldIdUsuario }}"
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
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-sm btn-brand">
                    <i class="fas fa-save mr-1"></i> Guardar Cambios
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
@stop

@push('js')
<script type="module">
    import { authReady, authorizedFetch, getIdToken } from "{{ asset('js/firebase.js') }}";

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

    async function cargarUsuarios(selectedFromEmpleado = null) {
        const select = document.getElementById('id_usuario-select');
        if (!select) return;

        const oldSelected = select.dataset.old; // valor de old('id_usuario'), si existe
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

                const debeSeleccionarse =
                    (oldSelected && String(id) === String(oldSelected)) ||
                    (!oldSelected && selectedFromEmpleado && String(id) === String(selectedFromEmpleado));

                if (debeSeleccionarse) {
                    option.selected = true;
                }

                select.appendChild(option);
            });
        } catch (err) {
            console.error('Error cargando usuarios en select (edición):', err);
            select.innerHTML = '<option value="">Error al cargar usuarios</option>';
        }
    }

    async function cargarEmpleado() {
        const empleadoIdInput = document.getElementById('empleado-id');
        if (!empleadoIdInput) return;

        const empleadoId = empleadoIdInput.value;
        if (!empleadoId) return;

        const nombreInput   = document.querySelector('input[name="nombre"]');
        const telefonoInput = document.querySelector('input[name="telefono"]');
        const areaSelect    = document.querySelector('select[name="area"]');

        try {
            await authReady;
            const response = await authorizedFetch(`http://localhost:3000/api/empleados/${empleadoId}`);
            const data = await response.json();

            if (!response.ok) {
                console.error('Error al obtener empleado:', data);
                await cargarUsuarios(null); // al menos cargamos el listado
                return;
            }

            // Solo rellenamos si el campo está vacío (para respetar old())
            if (nombreInput && !nombreInput.value) {
                nombreInput.value = data.nombre ?? '';
            }

            if (telefonoInput && !telefonoInput.value) {
                telefonoInput.value = data.telefono ?? '';
            }

            if (areaSelect && !areaSelect.value && data.area) {
                areaSelect.value = data.area;
            }

            const selectedUserFromEmpleado = data.id_usuario ?? null;
            await cargarUsuarios(selectedUserFromEmpleado);

        } catch (err) {
            console.error('Error cargando empleado:', err);
            await cargarUsuarios(null);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        rellenarTokenHidden();
        cargarEmpleado();
    });
</script>
@endpush