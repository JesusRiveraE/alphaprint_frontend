const pool = require("../db");
const admin = require('../config/firebase-config');

// Listar todos los usuarios (Sin cambios)
async function list(req, res) {
  try {
    const [results] = await pool.query("CALL M1_LISTAR_USUARIOS()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Obtener un usuario por ID (Sin cambios)
async function getById(req, res) {
  try {
    const [results] = await pool.query("CALL M1_OBTENER_USUARIO(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// ==========================================================
// ➕ CREAR USUARIO (LÓGICA ACTUALIZADA Y SINCRONIZADA)
// Esta función ahora es llamada por un admin para crear otros usuarios.
// ==========================================================
async function create(req, res) {
    // Los datos vienen del formulario del modal
    const { nombreUsuario, email, password, rol } = req.body;
    let connection;
    let firebaseUid; // Variable para guardar el UID si Firebase tiene éxito

    try {
        // TAREA 1: Crear el usuario en Firebase Authentication
        console.log(`[CONTROLLER] Creando usuario en Firebase con email: ${email}`);
        const userRecord = await admin.auth().createUser({
            email: email,
            password: password,
            displayName: nombreUsuario
        });
        firebaseUid = userRecord.uid;
        console.log(`[CONTROLLER] Usuario creado en Firebase con UID: ${firebaseUid}`);

        // TAREA 2: Guardar el usuario en la base de datos MySQL
        console.log(`[CONTROLLER] Guardando usuario en MySQL...`);
        connection = await pool.getConnection();
        // Usamos el procedimiento almacenado que ya tienes
        await connection.query("CALL M1_CREAR_USUARIO(?,?,?,?)", [
            firebaseUid,
            nombreUsuario,
            email,
            rol,
        ]);
        console.log(`[CONTROLLER] Usuario guardado en MySQL.`);
        
        res.status(201).json({ message: "Usuario creado exitosamente en ambos sistemas", uid: firebaseUid });

    } catch (error) {
        console.error('💥 Error en el proceso de creación:', error.message);
        
        // Lógica de Rollback: Si el usuario se creó en Firebase pero falló en MySQL, lo borramos de Firebase.
        if (firebaseUid) {
            await admin.auth().deleteUser(firebaseUid);
            console.log(`[CONTROLLER] ROLLBACK: Se eliminó el usuario ${firebaseUid} de Firebase por un error en la base de datos.`);
        }
        
        res.status(500).json({ error: 'Error al crear el usuario.', details: error.message });
    } finally {
        if (connection) {
            connection.release();
        }
    }
}

// Actualizar usuario (Sin cambios)
async function update(req, res) {
  try {
    const { uid_firebase, nombre_usuario, correo, rol, activo } = req.body;
    await pool.query("CALL M1_ACTUALIZAR_USUARIO(?,?,?,?,?,?)", [
      req.params.id,
      uid_firebase,
      nombre_usuario,
      correo,
      rol,
      activo,
    ]);
    res.json({ message: "Usuario actualizado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}
// ... (imports y otras funciones sin cambios) ...

// ==========================================================
// 🔄 ACTUALIZAR USUARIO (LÓGICA ACTUALIZADA Y SINCRONIZADA)
// ==========================================================
async function update(req, res) {
    const { id } = req.params; // ID de MySQL
    const { nombre_usuario, rol, activo, email, password } = req.body;
    let connection;

    try {
        console.log(`[CONTROLLER] Iniciando actualización para usuario ID: ${id}`);
        connection = await pool.getConnection();
        await connection.beginTransaction();

        // 1. Obtenemos el UID de Firebase del usuario
        const [rows] = await connection.execute('SELECT uid_firebase FROM USUARIOS WHERE id_usuario = ?', [id]);
        if (rows.length === 0) {
            throw new Error('Usuario no encontrado en la base de datos.');
        }
        const uidFirebase = rows[0].uid_firebase;

        // 2. Preparamos y ejecutamos la actualización en Firebase
        const updatePayload = {};
        if (email) updatePayload.email = email;
        if (password) updatePayload.password = password; // Solo si se provee una nueva contraseña
        if (nombre_usuario) updatePayload.displayName = nombre_usuario;

        if (Object.keys(updatePayload).length > 0) {
            console.log(`[CONTROLLER] Actualizando usuario ${uidFirebase} en Firebase...`);
            await admin.auth().updateUser(uidFirebase, updatePayload);
            console.log(`[CONTROLLER] ✅ Usuario actualizado en Firebase.`);
        }

        // 3. Actualizamos los datos en MySQL usando tu procedimiento almacenado
        console.log(`[CONTROLLER] Actualizando usuario ${id} en MySQL...`);
        // Nota: Asegúrate de que M1_ACTUALIZAR_USUARIO acepte los parámetros en este orden.
        // Si tu procedimiento no actualiza el correo, puedes quitarlo de aquí.
        const currentData = rows[0]; // Usamos los datos actuales como base
        await connection.query("CALL M1_ACTUALIZAR_USUARIO(?,?,?,?,?,?)", [
            id,
            uidFirebase,
            nombre_usuario || currentData.nombre_usuario,
            email || currentData.correo, // Si M1_ACTUALIZAR_USUARIO actualiza el correo
            rol || currentData.rol,
            activo === undefined ? currentData.activo : activo,
        ]);
        console.log(`[CONTROLLER] ✅ Usuario actualizado en MySQL.`);

        await connection.commit();
        res.status(200).json({ message: 'Usuario actualizado con éxito en ambos sistemas.' });

    } catch (error) {
        if (connection) await connection.rollback();
        console.error(`💥 Error al actualizar usuario con ID ${id}:`, error.message);
        res.status(500).json({ error: 'Error en el servidor al actualizar el usuario.', details: error.message });
    } finally {
        if (connection) connection.release();
    }
}

// ... (resto de funciones sin cambios) ...

module.exports = { list, getById, create, update, deactivate, remove };
// Desactivar usuario (Sin cambios)
async function deactivate(req, res) {
  try {
    await pool.query("CALL M1_DESACTIVAR_USUARIO(?)", [req.params.id]);
    res.json({ message: "Usuario desactivado correctamente" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Eliminar usuario (Lógica ya actualizada previamente, sin cambios)
async function remove(req, res) {
    // ... (El código de 'remove' que ya teníamos está correcto)
    const { id } = req.params;
    let connection;
    try {
        connection = await pool.getConnection();
        await connection.beginTransaction();
        const [rows] = await connection.execute('SELECT uid_firebase FROM USUARIOS WHERE id_usuario = ?', [id]);
        if (rows.length === 0) {
            await connection.rollback();
            connection.release();
            return res.status(404).json({ error: 'Usuario no encontrado.' });
        }
        const uidFirebase = rows[0].uid_firebase;
        if (uidFirebase) {
            await admin.auth().deleteUser(uidFirebase);
        }
        await connection.query("CALL M1_ELIMINAR_USUARIO(?)", [id]);
        await connection.commit();
        res.json({ message: "Usuario eliminado exitosamente de ambos sistemas." });
    } catch (error) {
        if (connection) await connection.rollback();
        res.status(500).json({ error: 'Error en el servidor.', details: error.message });
    } finally {
        if (connection) connection.release();
    }
}

// Exportamos todas las funciones
module.exports = { list, getById, create, update, deactivate, remove };