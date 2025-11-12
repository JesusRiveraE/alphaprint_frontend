@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    <h1>Listado de Empleados</h1>
@stop

@section('content')
    {{-- Botón Crear Empleado --}}
    <div class="mb-3">
        <button
            id="btnCrearEmpleado"
            class="btn btn-success"
            data-toggle="modal"
            data-target="#modalCrearEmpleado"
            style="display: none;"
        >
            <i class="fas fa-plus"></i> Registrar Nuevo Empleado
        </button>
    </div>

    {{-- Tabla de Empleados --}}
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
        {{-- El <tbody> se llena por JS --}}
        <tbody id="tabla-empleados-body">
            <tr>
                <td colspan="7" class="text-center">Cargando empleados...</td>
            </tr>
        </tbody>
    </table>

    {{-- Modal para Crear Empleado (sin cambios) --}}
    <div class="modal fade" id="modalCrearEmpleado" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Nuevo Empleado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                            <small class="form-text text-muted">
                                Selecciona el usuario que corresponderá a este empleado.
                            </small>
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

    {{-- Modal para Actualizar Empleado (sin cambios) --}}
    <div class="modal fade" id="modalActualizarEmpleado" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Actualizar Empleado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                            <small class="form-text text-muted">
                                Selecciona el usuario que corresponde a este empleado.
                            </small>
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
  // 🔰 1. IMPORTAMOS LAS FUNCIONES QUE NECESITAMOS
  // Esto fuerza al navegador a cargar 'firebase.js' PRIMERO.
  // Usamos asset() para obtener la ruta correcta.
  import { authReady, authorizedFetch } from "{{ asset('js/firebase.js') }}";

  /**
   * Carga los empleados desde la API y los dibuja en la tabla principal.
   */
  async function cargarEmpleados() {
    const tbody = document.getElementById('tabla-empleados-body');
    const userRole = localStorage.getItem('userRole') || 'Empleado';
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="7" class="text-center">Cargando empleados...</td></tr>';

    try {
      // 🔰 2. USAMOS LA FUNCIÓN IMPORTADA (sin 'window.')
      await authReady;
      const response = await authorizedFetch('http://localhost:3000/api/empleados');

      if (!response.ok) {
        const errData = await response.json();
        throw new Error(errData.error || 'No se pudo cargar la lista de empleados.');
      }

      const empleados = await response.json();

      if (!empleados || empleados.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay empleados para mostrar.</td></tr>';
        return;
      }

      tbody.innerHTML = ''; // Limpiar "Cargando..."

      empleados.forEach((item) => {
        const tr = document.createElement('tr');
        tr.id = `fila-empleado-${item.id_personal}`;

        let botonesAccion = '';
        if (userRole === 'Admin') {
          botonesAccion = `
            <button
              class="btn btn-warning btn-sm btn-actualizar-empleado"
              data-id="${item.id_personal}"
              data-nombre="${item.nombre}"
              data-telefono="${item.telefono || ''}"
              data-area="${item.area}"
              data-id_usuario="${item.id_usuario}"
              data-toggle="modal"
              data-target="#modalActualizarEmpleado"
            >
              Actualizar
            </button>
            <button
              class="btn btn-danger btn-sm btn-eliminar-empleado"
              onclick="eliminarEmpleado(${item.id_personal})"
            >
              Eliminar
            </button>
          `;
        }

        tr.innerHTML = `
          <td>${item.id_personal ?? ''}</td>
          <td>${item.nombre ?? ''}</td>
          <td>${item.telefono || ''}</td>
          <td>${item.area ?? ''}</td>
          <td>${item.nombre_usuario || '(Sin usuario asociado)'}</td>
          <td>${item.rol || '(N/A)'}</td>
          <td>${botonesAccion}</td>
        `;
        tbody.appendChild(tr);
      });
    } catch (error) {
      console.error('Error cargando empleados:', error);
      tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error al cargar empleados: ${error.message}</td></tr>`;
    }
  }

  // Cargar usuarios en los selects
  async function cargarUsuariosEnSelect(selectElementId, selectedUserId = null) {
    const selectElement = document.getElementById(selectElementId);
    if (!selectElement) return;

    selectElement.innerHTML = '<option value="">Cargando...</option>';

    try {
      // 🔰 3. USAMOS LAS FUNCIONES IMPORTADAS (sin 'window.')
      await authReady;
      const response = await authorizedFetch('http://localhost:3000/api/usuarios');
      if (!response.ok) throw new Error('No se pudo cargar la lista de usuarios.');

      const usuarios = await response.json();
      selectElement.innerHTML = '<option value="">-- Selecciona un Usuario --</option>';

      usuarios.forEach((usuario) => {
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
      console.error('Error cargando usuarios:', error);
      selectElement.innerHTML = '<option value="">Error al cargar</option>';
    }
  }

  // --- ELIMINAR EMPLEADO (Función separada) ---
  window.eliminarEmpleado = async function (idPersonal) {
    if (!idPersonal) return alert('ID inválido de empleado.');
    if (!confirm('¿Seguro que deseas eliminar este empleado? Esta acción no se puede deshacer.')) return;

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
      // 🔰 4. USAMOS LAS FUNCIONES IMPORTADAS (sin 'window.')
      await authReady;
      const response = await authorizedFetch(`http://localhost:3000/api/empleados/${idPersonal}`, {
        method: 'DELETE',
      });

      let data = {};
      try {
        data = await response.json();
      } catch (_) {}

      if (!response.ok) {
        const msg = data.error || data.details || `Error al eliminar (HTTP ${response.status})`;
        throw new Error(msg);
      }

      const fila = document.getElementById(`fila-empleado-${idPersonal}`);
      if (fila) fila.remove();

      alert('🗑️ Empleado eliminado correctamente.');
    } catch (err) {
      alert('❌ No se pudo eliminar el empleado: ' + err.message);
    } finally {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = btn.dataset._oldText || 'Eliminar';
        delete btn.dataset._oldText;
      }
    }
  };

  // --- LÓGICA DE EVENTOS (DOMContentLoaded, etc.) ---
  document.addEventListener('DOMContentLoaded', () => {
    // Llama a la función de carga principal
    cargarEmpleados();

    // --- LÓGICA DE ROLES
    const userRole = localStorage.getItem('userRole');
    if (userRole === 'Admin') {
      document.getElementById('btnCrearEmpleado').style.display = 'block';
    } else {
      document
        .querySelectorAll('.btn-crear-empleado, .btn-actualizar-empleado, .btn-eliminar-empleado')
        .forEach((btn) => {
          if (btn) btn.style.display = 'none';
        });
      document
        .querySelectorAll('tbody .btn-actualizar-empleado, tbody .btn-eliminar-empleado')
        .forEach((btn) => {
          btn.style.display = 'none';
        });
    }

    // --- CREAR EMPLEADO
    $('#modalCrearEmpleado').on('show.bs.modal', function () {
      cargarUsuariosEnSelect('id_usuario-crear');
      document.getElementById('formCrearEmpleado').reset();
    });

    const formCrearEmpleado = document.getElementById('formCrearEmpleado');
    if (formCrearEmpleado) {
      formCrearEmpleado.addEventListener('submit', async (e) => {
        e.preventDefault();
        const empleadoData = {
          nombre: document.getElementById('nombre-crear').value,
          telefono: document.getElementById('telefono-crear').value,
          area: document.getElementById('area-crear').value,
          id_usuario: parseInt(document.getElementById('id_usuario-crear').value, 10),
        };
        if (!empleadoData.id_usuario) {
          return alert('Error: El Usuario Asociado es obligatorio.');
        }
        try {
          // 🔰 5. USAMOS LAS FUNCIONES IMPORTADAS (sin 'window.')
          await authReady;
          const response = await authorizedFetch('http://localhost:3000/api/empleados', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(empleadoData),
          });
          const data = await response.json();
          if (!response.ok) throw new Error(data.error || data.details || 'Error del servidor.');
          alert('✅ ¡Empleado creado exitosamente!');
          $('#modalCrearEmpleado').modal('hide');
          location.reload();
        } catch (err) {
          alert('❌ Error al crear el empleado: ' + err.message);
        }
      });
    }

    // --- ACTUALIZAR EMPLEADO
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
    if (formActualizarEmpleado) {
      formActualizarEmpleado.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id_personal = document.getElementById('id_personal-actualizar').value;
        const empleadoData = {
          nombre: document.getElementById('nombre-actualizar').value,
          telefono: document.getElementById('telefono-actualizar').value,
          area: document.getElementById('area-actualizar').value,
          id_usuario: parseInt(document.getElementById('id_usuario-actualizar').value, 10),
        };
        if (!empleadoData.id_usuario) {
          return alert('Error: El Usuario Asociado es obligatorio.');
        }
        try {
          // 🔰 6. USAMOS LAS FUNCIONES IMPORTADAS (sin 'window.')
          await authReady;
          const response = await authorizedFetch(`http://localhost:3000/api/empleados/${id_personal}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(empleadoData),
          });
          const data = await response.json();
          if (!response.ok) throw new Error(data.error || data.details || 'Error del servidor');
          alert('✅ Empleado actualizado con éxito');
          $('#modalActualizarEmpleado').modal('hide');
          location.reload();
        } catch (err) {
          alert('❌ Error al actualizar el empleado: ' + err.message);
        }
      });
    }
  });
</script>
@endpush

