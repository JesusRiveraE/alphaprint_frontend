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
        {{-- 🔰 1. SE AÑADIÓ EL ID AQUÍ 🔰 --}}
        <tbody id="tabla-usuarios-body">
            {{-- (El controlador ahora pasa [], así que esto se ignora) --}}
            @if(isset($usuarios) && count($usuarios) > 0)
            @else
                <tr>
                    <td colspan="7" class="text-center">Cargando usuarios...</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- 🔰 MODAL PARA CREAR USUARIO (CON VALIDACIÓN MEJORADA) --}}
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
                            <input type="text" class="form-control" id="nombre-crear" required 
                                   minlength="4" maxlength="30">
                            <small class="form-text text-muted">
                                Debe tener entre 4 y 30 caracteres.
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="email-crear">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email-crear" required>
                        </div>
                        <div class="form-group">
                            <label for="password-crear">Contraseña</label>
                            <input type="password" class="form-control" id="password-crear" required 
                                   minlength="6">
                            <small class="form-text text-muted">
                                Debe tener al menos 6 caracteres.
                            </small>
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

    {{-- Modal para Actualizar Usuario (CON VALIDACIÓN MEJORADA) --}}
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
                            <input type="text" class="form-control" id="nombre-actualizar" required 
                                   minlength="4" maxlength="30">
                            <small class="form-text text-muted">
                                Debe tener entre 4 y 30 caracteres.
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="email-actualizar">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email-actualizar" required>
                        </div>
                         <div class="form-group">
                            <label for="password-actualizar">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                            <input type="password" class="form-control" id="password-actualizar" placeholder="********" 
                                   minlength="6">
                            <small class="form-text text-muted">
                                Debe tener al menos 6 caracteres (si se cambia).
                            </small>
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
    // Importamos firebaseAuth (lo necesitas para obtener el token)
    import { firebaseAuth } from "{{ asset('js/firebase.js') }}";

    // 🔰🔰 2. SE AÑADIÓ ESTA FUNCIÓN (LA QUE TE FALTABA) 🔰🔰
    /**
     * Carga los usuarios desde la API y los dibuja en la tabla.
     */
    async function cargarUsuarios() {
        // Busca el ID que acabamos de añadir al <tbody>
        const tbody = document.getElementById('tabla-usuarios-body'); 
        if (!tbody) return;
        
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Cargando usuarios...</td></tr>';
        
        try {
            await window.authReady; 
            
            // Usa el fetch autorizado (que ya incluye el token)
            const response = await window.authorizedFetch("http://localhost:3000/api/usuarios");
            
            if (!response.ok) {
                const errData = await response.json();
                throw new Error(errData.error || 'No se pudo cargar la lista de usuarios.');
            }
            
            const usuarios = await response.json();
            
            if (!usuarios || usuarios.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay usuarios para mostrar.</td></tr>';
                return;
            }
            
            tbody.innerHTML = ''; // Limpiar el "Cargando..."
            const userRole = localStorage.getItem('userRole');

            usuarios.forEach(item => {
                const tr = document.createElement('tr');
                tr.id = `fila-usuario-${item.id_usuario}`;
                
                const estado = item.activo ? 'Sí' : 'No';
                const fecha = item.fecha_creacion ? new Date(item.fecha_creacion).toLocaleDateString() : '';
                
                let botones = '';
                // Solo muestra botones de acción si eres Admin
                if (userRole === 'Admin') {
                    botones = `
                        <button class="btn btn-warning btn-sm btn-actualizar-usuario" 
                                data-id="${item.id_usuario}"
                                data-nombre="${item.nombre_usuario}"
                                data-correo="${item.correo}"
                                data-rol="${item.rol}"
                                data-activo="${item.activo}"
                                data-toggle="modal" 
                                data-target="#modalActualizarUsuario">
                            Actualizar
                        </button>
                        <button class="btn btn-danger btn-sm btn-eliminar-usuario" onclick="eliminarUsuario(${item.id_usuario})">
                            Eliminar
                        </button>
                    `;
                }

                tr.innerHTML = `
                    <td>${item.id_usuario ?? ''}</td>
                    <td>${item.nombre_usuario ?? ''}</td>
                    <td>${item.correo ?? ''}</td>
                    <td>${item.rol ?? ''}</td>
                    <td><span class="badge ${item.activo ? 'badge-success' : 'badge-danger'}">${estado}</span></td>
                    <td>${fecha}</td>
                    <td>${botones}</td>
                `;
                tbody.appendChild(tr);
            });

        } catch (error) {
            console.error("Error cargando usuarios:", error);
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error al cargar usuarios: ${error.message}</td></tr>`;
        }
    }


    document.addEventListener('DOMContentLoaded', () => {

        // 🔰 EJECUTA LA FUNCIÓN DE CARGA
        cargarUsuarios();

        // --- LÓGICA DE ROLES ---
        const userRole = localStorage.getItem('userRole');
        if (userRole === 'Admin') {
            document.getElementById('btnCrearUsuario').style.display = 'block';
        }
        // (La lógica de ocultar botones ya la hace cargarUsuarios)

        // --- LÓGICA PARA CREAR USUARIO (CON MANEJO DE ERROR MEJORADO) ---
        const formCrearUsuario = document.getElementById('formCrearUsuario');
        if(formCrearUsuario) {
            formCrearUsuario.addEventListener('submit', async (e) => {
                e.preventDefault();
                const nombreUsuario = document.getElementById('nombre-crear').value;
                const email = document.getElementById('email-crear').value;
                const password = document.getElementById('password-crear').value;
                const rol = document.getElementById('rol-crear').value;
                
                try {
                    // Usamos window.authorizedFetch (ya tiene el token)
                    const response = await window.authorizedFetch("http://localhost:3000/api/usuarios", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ nombreUsuario, email, password, rol })
                    });
                    
                    const data = await response.json(); 

                    if (!response.ok) {
                        let errorMsg = data.msg || "Error del servidor.";
                        if (data.errores && Array.isArray(data.errores)) {
                            errorMsg += "\n\nDetalles:\n" + data.errores.map(e => `• ${e.msg}`).join("\n");
                        }
                        throw new Error(errorMsg);
                    }
                    
                    alert("✅ ¡Usuario creado exitosamente!");
                    $('#modalCrearUsuario').modal('hide');
                    
                    cargarUsuarios(); // Recarga solo la tabla
                } catch (err) {
                    alert("❌ Error al crear el usuario: \n\n".concat(err.message));
                }
            });
        }

        // --- LÓGICA PARA ACTUALIZAR USUARIO (CON MANEJO DE ERROR MEJORADO) ---
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
                    const response = await window.authorizedFetch(`http://localhost:3000/api/usuarios/${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(updateData)
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        let errorMsg = data.msg || "Error del servidor.";
                        if (data.errores && Array.isArray(data.errores)) {
                            errorMsg += "\n\nDetalles:\n" + data.errores.map(e => `• ${e.msg}`).join("\n");
                        }
                        throw new Error(errorMsg);
                    }
                    
                    alert('✅ Usuario actualizado con éxito');
                    $('#modalActualizarUsuario').modal('hide');
                    
                    cargarUsuarios(); // Recarga solo la tabla
                } catch (err) {
                    alert('❌ Error al actualizar el usuario: \n\n'.concat(err.message));
                }
            });
        }
    });

    // --- LÓGICA PARA ELIMINAR USUARIO ---
    window.eliminarUsuario = async function(idUsuario) {
        if (!confirm(`¿Estás seguro de que quieres eliminar al usuario con ID: ${idUsuario}?`)) return;
        try {
            const response = await window.authorizedFetch(`http://localhost:3000/api/usuarios/${idUsuario}`, {
                method: 'DELETE'
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'No se pudo completar la eliminación.');
            
            alert(data.message);
            cargarUsuarios(); // Recarga solo la tabla
        } catch (error) {
            console.error('Error al eliminar usuario:', error);
            alert(`Error: ${error.message}`);
        }
    }
</script>
@endpush