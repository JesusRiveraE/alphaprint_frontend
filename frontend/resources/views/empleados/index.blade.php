@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    <h1>Listado de Empleados</h1>
@stop

@section('content')
    {{-- Botón Crear Empleado (sin cambios) --}}
    <div class="mb-3">
        <button id="btnCrearEmpleado" class="btn btn-success" data-toggle="modal" data-target="#modalCrearEmpleado" style="display: none;">
            <i class="fas fa-plus"></i> Registrar Nuevo Empleado
        </button>
    </div>

    {{-- Tabla de Empleados (sin cambios) --}}
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID Personal</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Área</th>
                <th>Usuario Asociado</th>
                <th>Rol Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($empleados) && count($empleados) > 0)
                @foreach($empleados as $item)
                    <tr id="fila-empleado-{{ $item['id_personal'] ?? '' }}">
                        <td>{{ $item['id_personal'] ?? '' }}</td>
                        <td>{{ $item['nombre'] ?? '' }}</td>
                        <td>{{ $item['telefono'] ?? '' }}</td>
                        <td>{{ $item['area'] ?? '' }}</td>
                        <td>{{ $item['nombre_usuario'] ?? '(Sin usuario asociado)' }}</td>
                        <td>{{ $item['rol'] ?? '(N/A)' }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-actualizar-empleado"
                                    data-id="{{ $item['id_personal'] }}"
                                    data-nombre="{{ $item['nombre'] }}"
                                    data-telefono="{{ $item['telefono'] ?? '' }}"
                                    data-area="{{ $item['area'] }}"
                                    data-id_usuario="{{ $item['id_usuario'] }}"
                                    data-toggle="modal"
                                    data-target="#modalActualizarEmpleado">
                                Actualizar
                            </button>
                            <button class="btn btn-danger btn-sm btn-eliminar-empleado" onclick="eliminarEmpleado({{ $item['id_personal'] ?? '' }})">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">No hay empleados para mostrar.</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Modal para Crear Empleado --}}
    <div class="modal fade" id="modalCrearEmpleado" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Nuevo Empleado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formCrearEmpleado">
                        <div class="form-group">
                            <label for="nombre-crear">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre-crear" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono-crear">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono-crear">
                        </div>
                        <div class="form-group">
                            <label for="area-crear">Área</label>
                            <select class="form-control" id="area-crear" required>
                                <option value="">-- Selecciona un área --</option>
                                <option value="Diseño gráfico">Diseño gráfico</option>
                                <option value="Sala de ventas">Sala de ventas</option>
                                <option value="Taller de encuadernación">Taller de encuadernación</option>
                                <option value="Taller corte CNC">Taller corte CNC</option>
                                <option value="Taller de corte y soldadura">Taller de corte y soldadura</option>
                                <option value="Taller de acabados">Taller de acabados</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_usuario-crear">Usuario Asociado</label>
                            <select class="form-control" id="id_usuario-crear" required>
                                <option value="">Cargando usuarios...</option>
                            </select>
                            <small class="form-text text-muted">Selecciona el usuario que corresponderá a este empleado.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" form="formCrearEmpleado">Guardar Empleado</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para Actualizar Empleado --}}
    <div class="modal fade" id="modalActualizarEmpleado" tabindex="-1" role="dialog">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Actualizar Empleado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formActualizarEmpleado">
                        <input type="hidden" id="id_personal-actualizar">
                        <div class="form-group">
                            <label for="nombre-actualizar">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre-actualizar" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono-actualizar">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono-actualizar">
                        </div>
                        <div class="form-group">
                            <label for="area-actualizar">Área</label>
                            <select class="form-control" id="area-actualizar" required>
                                <option value="">-- Selecciona un área --</option>
                                <option value="Diseño gráfico">Diseño gráfico</option>
                                <option value="Sala de ventas">Sala de ventas</option>
                                <option value="Taller de encuadernación">Taller de encuadernación</option>
                                <option value="Taller corte CNC">Taller corte CNC</option>
                                <option value="Taller de corte y soldadura">Taller de corte y soldadura</option>
                                <option value="Taller de acabados">Taller de acabados</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_usuario-actualizar">Usuario Asociado</label>
                            <select class="form-control" id="id_usuario-actualizar" required>
                                <option value="">Cargando usuarios...</option>
                            </select>
                            <small class="form-text text-muted">Selecciona el usuario que corresponde a este empleado.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" form="formActualizarEmpleado">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script type="module">
    import { firebaseAuth } from "{{ asset('js/firebase.js') }}";

    // Carga de usuarios para selects (sin cambios)
    async function cargarUsuariosEnSelect(selectElementId, selectedUserId = null) {
        const selectElement = document.getElementById(selectElementId);
        if (!selectElement) return;
        selectElement.innerHTML = '<option value="">Cargando...</option>';

        try {
            const response = await fetch("http://localhost:3000/api/usuarios");
            if (!response.ok) throw new Error('No se pudo cargar la lista de usuarios.');
            const usuarios = await response.json();

            selectElement.innerHTML = '<option value="">-- Selecciona un Usuario --</option>';

            usuarios.forEach(usuario => {
                if (usuario.activo && (usuario.rol === 'Empleado' || usuario.rol === 'Admin')) {
                    const option = document.createElement('option');
                    option.value = usuario.id_usuario;
                    option.textContent = `${usuario.nombre_usuario} (${usuario.correo})`;
                    if (selectedUserId && usuario.id_usuario == selectedUserId) {
                        option.selected = true;
                    }
                    selectElement.appendChild(option);
                }
            });
        } catch (error) {
            console.error("Error cargando usuarios:", error);
            selectElement.innerHTML = '<option value="">Error al cargar</option>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // --- LÓGICA DE ROLES (sin cambios) ---
        const userRole = localStorage.getItem('userRole');
        if (userRole === 'Admin') {
            document.getElementById('btnCrearEmpleado').style.display = 'block';
        } else {
            document.querySelectorAll('.btn-crear-empleado, .btn-actualizar-empleado, .btn-eliminar-empleado').forEach(btn => {
                if(btn) btn.style.display = 'none';
            });
            document.querySelectorAll('tbody .btn-actualizar-empleado, tbody .btn-eliminar-empleado').forEach(btn => {
                btn.style.display = 'none';
            });
        }

        // --- CREAR EMPLEADO (sin cambios) ---
        $('#modalCrearEmpleado').on('show.bs.modal', function () {
            cargarUsuariosEnSelect('id_usuario-crear');
            document.getElementById('formCrearEmpleado').reset();
        });

        const formCrearEmpleado = document.getElementById('formCrearEmpleado');
        if(formCrearEmpleado) {
            formCrearEmpleado.addEventListener('submit', async (e) => {
                e.preventDefault();
                const empleadoData = {
                    nombre: document.getElementById('nombre-crear').value,
                    telefono: document.getElementById('telefono-crear').value,
                    area: document.getElementById('area-crear').value,
                    id_usuario: parseInt(document.getElementById('id_usuario-crear').value, 10)
                };
                if (!empleadoData.id_usuario) {
                   return alert('Error: El Usuario Asociado es obligatorio.');
                }
                try {
                    const adminActual = firebaseAuth.currentUser;
                    const token = await adminActual.getIdToken();
                    const response = await fetch("http://localhost:3000/api/empleados", {
                        method: "POST",
                        headers: { "Content-Type": "application/json", "Authorization": `Bearer ${token}` },
                        body: JSON.stringify(empleadoData)
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.error || data.details || "Error del servidor.");
                    alert("✅ ¡Empleado creado exitosamente!");
                    $('#modalCrearEmpleado').modal('hide');
                    location.reload();
                } catch (err) {
                    alert("❌ Error al crear el empleado: ".concat(err.message));
                }
            });
        }

        // --- ACTUALIZAR EMPLEADO (sin cambios) ---
        $('#modalActualizarEmpleado').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const idUsuarioActual = button.data('id_usuario');

            document.getElementById('id_personal-actualizar').value = button.data('id');
            document.getElementById('nombre-actualizar').value = button.data('nombre');
            document.getElementById('telefono-actualizar').value = button.data('telefono');
            document.getElementById('area-actualizar').value = button.data('area');

            cargarUsuariosEnSelect('id_usuario-actualizar', idUsuarioActual);
        });

        const formActualizarEmpleado = document.getElementById('formActualizarEmpleado');
        if(formActualizarEmpleado) {
            formActualizarEmpleado.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id_personal = document.getElementById('id_personal-actualizar').value;
                const empleadoData = {
                    nombre: document.getElementById('nombre-actualizar').value,
                    telefono: document.getElementById('telefono-actualizar').value,
                    area: document.getElementById('area-actualizar').value,
                    id_usuario: parseInt(document.getElementById('id_usuario-actualizar').value, 10)
                };
                 if (!empleadoData.id_usuario) {
                   return alert('Error: El Usuario Asociado es obligatorio.');
                }
                try {
                    const adminActual = firebaseAuth.currentUser;
                    const token = await adminActual.getIdToken();
                    const response = await fetch(`http://localhost:3000/api/empleados/${id_personal}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                        body: JSON.stringify(empleadoData)
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.error || data.details || 'Error del servidor');
                    alert('✅ Empleado actualizado con éxito');
                    $('#modalActualizarEmpleado').modal('hide');
                    location.reload();
                } catch (err) {
                    alert('❌ Error al actualizar el empleado: '.concat(err.message));
                }
            });
        }
    });

    // --- ELIMINAR EMPLEADO (corregido) ---
    window.eliminarEmpleado = async function(idPersonal) {
        if (!idPersonal) return alert('ID inválido de empleado.');
        if (!confirm('¿Seguro que deseas eliminar este empleado? Esta acción no se puede deshacer.')) return;

        // Intentar deshabilitar temporalmente el botón que disparó la acción
        let btn;
        try {
            btn = document.querySelector(`#fila-empleado-${idPersonal} .btn-eliminar-empleado`);
            if (btn) {
                btn.disabled = true;
                btn.dataset._oldText = btn.innerHTML;
                btn.innerHTML = 'Eliminando...';
            }
        } catch (_) {}

        try {
            const adminActual = firebaseAuth.currentUser;
            if (!adminActual) throw new Error('No hay sesión válida. Inicia sesión nuevamente.');
            const token = await adminActual.getIdToken();

            const response = await fetch(`http://localhost:3000/api/empleados/${idPersonal}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            let data = {};
            try { data = await response.json(); } catch (_) {}

            if (!response.ok) {
                const msg = data.error || data.details || `Error al eliminar (HTTP ${response.status})`;
                throw new Error(msg);
            }

            // Remover fila sin recargar, y luego recargar para mantener consistencia si lo prefieres
            const fila = document.getElementById(`fila-empleado-${idPersonal}`);
            if (fila) fila.remove();

            alert('🗑️ Empleado eliminado correctamente.');
            // Si prefieres forzar recarga para refrescar paginación/contadores:
            // location.reload();
        } catch (err) {
            alert('❌ No se pudo eliminar el empleado: ' + err.message);
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = btn.dataset._oldText || 'Eliminar';
                delete btn.dataset._oldText;
            }
        }
    }
</script>
@endpush
