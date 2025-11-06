const pool = require("../db");
const admin = require('../config/firebase-config');

// 1. IMPORTAMOS 'validationResult' DE EXPRESS-VALIDATOR
const { validationResult } = require('express-validator');

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
// (Esta función ya tiene su validación)
// ==========================================================
async function create(req, res) {
    
    // --- INICIO: CÓDIGO DE VALIDACIÓN AÑADIDO ---
    const errores = validationResult(req);
    if (!errores.isEmpty()) {
        return res.status(400).json({ 
            msg: 'Errores en la validación de datos', 
            errores: errores.array() 
        });
    }
    // --- FIN: CÓDIGO DE VALIDACIÓN AÑADIDO ---

    const { nombreUsuario, email, password, rol } = req.body;
    let connection;
    let firebaseUid; 

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


// ==========================================================
// 🔄 ACTUALIZAR USUARIO (LÓGICA ACTUALIZADA Y SINCRONIZADA)
// ==========================================================
async function update(req, res) {

    // --- INICIO: CÓDIGO DE VALIDACIÓN AÑADIDO (¡NUEVO!) ---
    // Revisa si validationResult encontró errores
    // basados en las reglas 'opcionales' que pusimos en 'usuarios.js'
    const errores = validationResult(req);
    if (!errores.isEmpty()) {
        // Si hay errores, responde inmediatamente con 400 y los detalles
        return res.status(400).json({ 
            msg: 'Errores en la validación de datos', 
            errores: errores.array() 
        });
    }
    // --- FIN: CÓDIGO DE VALIDACIÓN AÑADIDO ---

    //
    // Si el código llega aquí, los datos son válidos.
    // Tu lógica original de actualización se ejecuta sin cambios.
    //
    
    const { id } = req.params; // ID de MySQL
    const { nombre_usuario, rol, activo, email, password } = req.body;
    let connection;

    try {
        console.log(`[CONTROLLER] Iniciando actualización para usuario ID: ${id}`);
        connection = await pool.getConnection();
        await connection.beginTransaction();

        // 1. Obtenemos el UID de Firebase del usuario
        const [rows] = await connection.execute('SELECT uid_firebase, nombre_usuario, correo, rol, activo FROM USUARIOS WHERE id_usuario = ?', [id]);
        if (rows.length === 0) {
            throw new Error('Usuario no encontrado en la base de datos.');
        }
        const uidFirebase = rows[0].uid_firebase;
        const currentData = rows[0]; // Usamos los datos actuales como base

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
        await connection.query("CALL M1_ACTUALIZAR_USUARIO(?,?,?,?,?,?)", [
            id,
            uidFirebase,
            nombre_usuario || currentData.nombre_usuario,
            email || currentData.correo, 
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