@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1>Listado de Usuarios</h1>
@stop

@section('content')
    {{-- Botón para Crear Usuario --}}
    <div class="mb-3">
        <button id="btnCrearUsuario" class="btn btn-success" data-toggle="modal" data-target="#modalCrearUsuario" style="display: none;">
            <i class="fas fa-plus"></i> Crear Nuevo Usuario
        </button>
    </div>

    {{-- Tabla de Usuarios --}}
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Activo</th>
                <th>Fecha de Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($usuarios) && count($usuarios) > 0)
                @foreach($usuarios as $item)
                    <tr id="fila-usuario-{{ $item['id_usuario'] ?? '' }}">
                        <td>{{ $item['id_usuario'] ?? '' }}</td>
                        <td>{{ $item['nombre_usuario'] ?? '' }}</td>
                        <td>{{ $item['correo'] ?? '' }}</td>
                        <td>{{ $item['rol'] ?? '' }}</td>
                        <td>{{ $item['activo'] ? 'Sí' : 'No' }}</td>
                        <td>{{ $item['fecha_creacion'] ?? '' }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-actualizar-usuario" 
                                    data-id="{{ $item['id_usuario'] }}"
                                    data-nombre="{{ $item['nombre_usuario'] }}"
                                    data-correo="{{ $item['correo'] }}"
                                    data-rol="{{ $item['rol'] }}"
                                    data-activo="{{ $item['activo'] }}"
                                    data-toggle="modal" 
                                    data-target="#modalActualizarUsuario">
                                Actualizar
                            </button>
                            <button class="btn btn-danger btn-sm btn-eliminar-usuario" onclick="eliminarUsuario({{ $item['id_usuario'] ?? '' }})">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">No hay usuarios para mostrar.</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- 🔰 MODAL PARA CREAR USUARIO (CONTENIDO RESTAURADO) --}}
    <div class="modal fade" id="modalCrearUsuario" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nuevo Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formCrearUsuario">
                        <div class="form-group">
                            <label for="nombre-crear">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombre-crear" required>
                        </div>
                        <div class="form-group">
                            <label for="email-crear">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email-crear" required>
                        </div>
                        <div class="form-group">
                            <label for="password-crear">Contraseña</label>
                            <input type="password" class="form-control" id="password-crear" required>
                        </div>
                        <div class="form-group">
                            <label for="rol-crear">Rol</label>
                            <select class="form-control" id="rol-crear" required>
                                <option value="Empleado">Empleado</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" form="formCrearUsuario">Guardar Usuario</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para Actualizar Usuario (contenido completo) --}}
    <div class="modal fade" id="modalActualizarUsuario" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Actualizar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formActualizarUsuario">
                        <input type="hidden" id="id-actualizar">
                        <div class="form-group">
                            <label for="nombre-actualizar">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombre-actualizar" required>
                        </div>
                        <div class="form-group">
                            <label for="email-actualizar">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email-actualizar" required>
                        </div>
                         <div class="form-group">
                            <label for="password-actualizar">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                            <input type="password" class="form-control" id="password-actualizar" placeholder="********">
                        </div>
                        <div class="form-group">
                            <label for="rol-actualizar">Rol</label>
                            <select class="form-control" id="rol-actualizar" required>
                                <option value="Empleado">Empleado</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="activo-actualizar">Estado</label>
                            <select class="form-control" id="activo-actualizar" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" form="formActualizarUsuario">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script type="module">
    // El script completo y final que ya teníamos es correcto y no necesita cambios.
    // Lo incluyo aquí para que tengas el archivo 100% completo.
    import { firebaseAuth } from "{{ asset('js/firebase.js') }}";

    document.addEventListener('DOMContentLoaded', () => {

        // --- LÓGICA DE ROLES ---
        const userRole = localStorage.getItem('userRole');
        if (userRole === 'Admin') {
            document.getElementById('btnCrearUsuario').style.display = 'block';
        }
        if (userRole !== 'Admin') {
            document.querySelectorAll('.btn-eliminar-usuario, .btn-actualizar-usuario').forEach(btn => {
                btn.style.display = 'none';
            });
        }

        // --- LÓGICA PARA CREAR USUARIO ---
        const formCrearUsuario = document.getElementById('formCrearUsuario');
        if(formCrearUsuario) {
            formCrearUsuario.addEventListener('submit', async (e) => {
                e.preventDefault();
                const nombreUsuario = document.getElementById('nombre-crear').value;
                const email = document.getElementById('email-crear').value;
                const password = document.getElementById('password-crear').value;
                const rol = document.getElementById('rol-crear').value;
                try {
                    const adminActual = firebaseAuth.currentUser;
                    if (!adminActual) return alert('Debes iniciar sesión como administrador.');
                    const token = await adminActual.getIdToken();
                    const response = await fetch("http://localhost:3000/api/usuarios", {
                        method: "POST",
                        headers: { "Content-Type": "application/json", "Authorization": `Bearer ${token}` },
                        body: JSON.stringify({ nombreUsuario, email, password, rol })
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.details || "Error del servidor.");
                    alert("✅ ¡Usuario creado exitosamente!");
                    $('#modalCrearUsuario').modal('hide');
                    location.reload();
                } catch (err) {
                    alert("❌ Error al crear el usuario: ".concat(err.message));
                }
            });
        }

        // --- LÓGICA PARA ACTUALIZAR USUARIO ---
        $('#modalActualizarUsuario').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            document.getElementById('id-actualizar').value = button.data('id');
            document.getElementById('nombre-actualizar').value = button.data('nombre');
            document.getElementById('email-actualizar').value = button.data('correo');
            document.getElementById('rol-actualizar').value = button.data('rol');
            document.getElementById('activo-actualizar').value = button.data('activo');
            document.getElementById('password-actualizar').value = '';
        });

        const formActualizarUsuario = document.getElementById('formActualizarUsuario');
        if(formActualizarUsuario) {
            formActualizarUsuario.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = document.getElementById('id-actualizar').value;
                const updateData = {
                    nombre_usuario: document.getElementById('nombre-actualizar').value,
                    email: document.getElementById('email-actualizar').value,
                    rol: document.getElementById('rol-actualizar').value,
                    activo: parseInt(document.getElementById('activo-actualizar').value, 10)
                };
                const password = document.getElementById('password-actualizar').value;
                if (password) updateData.password = password;
                try {
                    const adminActual = firebaseAuth.currentUser;
                    const token = await adminActual.getIdToken();
                    const response = await fetch(`http://localhost:3000/api/usuarios/${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                        body: JSON.stringify(updateData)
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.details || 'Error del servidor');
                    alert('✅ Usuario actualizado con éxito');
                    $('#modalActualizarUsuario').modal('hide');
                    location.reload();
                } catch (err) {
                    alert('❌ Error al actualizar el usuario: '.concat(err.message));
                }
            });
        }
    });

    // --- LÓGICA PARA ELIMINAR USUARIO ---
    window.eliminarUsuario = async function(idUsuario) {
        if (!confirm(`¿Estás seguro de que quieres eliminar al usuario con ID: ${idUsuario}?`)) return;
        try {
            const adminActual = firebaseAuth.currentUser;
            if (!adminActual) return alert('Debes iniciar sesión como administrador.');
            const token = await adminActual.getIdToken();
            const response = await fetch(`http://localhost:3000/api/usuarios/${idUsuario}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'No se pudo completar la eliminación.');
            alert(data.message);
            document.getElementById(`fila-usuario-${idUsuario}`).remove();
        } catch (error) {
            console.error('Error al eliminar usuario:', error);
            alert(`Error: ${error.message}`);
        }
    }
</script>
@endpush