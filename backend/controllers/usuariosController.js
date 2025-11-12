// backend/controllers/usuariosController.js
const pool = require("../db");
const admin = require("../config/firebase-config");
const { validationResult } = require("express-validator");
const db = admin.database();

/* ============================================================================
 * LISTAR
 * ==========================================================================*/
async function list(_req, res) {
  try {
    const [results] = await pool.query("CALL M1_LISTAR_USUARIOS()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

/* ============================================================================
 * OBTENER POR ID
 * ==========================================================================*/
async function getById(req, res) {
  try {
    const [results] = await pool.query("CALL M1_OBTENER_USUARIO(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

/* ============================================================================
 * CREAR USUARIO (Firebase + MySQL, con transacción y rollback seguro)
 * ==========================================================================*/
async function create(req, res) {
  const errores = validationResult(req);
  if (!errores.isEmpty()) {
    return res.status(400).json({ msg: "Errores en la validación de datos", errores: errores.array() });
  }

  const { nombreUsuario, email, password, rol } = req.body;
  let connection;
  let firebaseUid;

  try {
    // 1) Crear en Firebase
    const userRecord = await admin.auth().createUser({
      email,
      password,
      displayName: nombreUsuario,
    });
    firebaseUid = userRecord.uid;

    // 2) Guardar en MySQL (transacción)
    connection = await pool.getConnection();
    await connection.beginTransaction();

    await connection.query("CALL M1_CREAR_USUARIO(?,?,?,?)", [
      firebaseUid,
      nombreUsuario,
      email,
      rol,
    ]);

    await connection.commit();
    connection.release();

    return res.status(201).json({
      message: "Usuario creado exitosamente en ambos sistemas",
      uid: firebaseUid,
    });
  } catch (error) {
    // Limpieza/rollback
    if (connection) {
      try { await connection.rollback(); } catch (_) {}
      connection.release();
    }
    if (firebaseUid) {
      try { await admin.auth().deleteUser(firebaseUid); } catch (_) {}
    }
    console.error("💥 Error en create:", error?.message);
    return res.status(500).json({ error: "Error al crear el usuario.", details: error?.message });
  }
}

/* ============================================================================
 * ACTUALIZAR USUARIO
 * - Sincroniza cambios con Firebase.
 * - Si cambia 'activo', aplica disable/enable y revoca tokens cuando corresponda.
 * ==========================================================================*/
async function update(req, res) {
  const errores = validationResult(req);
  if (!errores.isEmpty()) {
    return res.status(400).json({ msg: "Errores en la validación de datos", errores: errores.array() });
  }

  const { id } = req.params; // id_usuario (MySQL)
  const { nombre_usuario, rol, activo, email, password } = req.body;

  let connection;
  try {
    connection = await pool.getConnection();
    await connection.beginTransaction();

    // Datos actuales
    const [rows] = await connection.execute(
      "SELECT uid_firebase, nombre_usuario, correo, rol, activo FROM USUARIOS WHERE id_usuario = ?",
      [id]
    );
    if (rows.length === 0) {
      throw new Error("Usuario no encontrado en la base de datos.");
    }
    const current = rows[0];
    const uid = current.uid_firebase;

    // 1) Actualizar en Firebase si hay cambios de identidad
    const fbUpdate = {};
    if (email && email !== current.correo) fbUpdate.email = email;
    if (password) fbUpdate.password = password;
    if (nombre_usuario && nombre_usuario !== current.nombre_usuario) fbUpdate.displayName = nombre_usuario;
    
    // 2) Cambios de estado 'activo' -> deshabilitar/habilitar
    //    🔰 🔰 🔰 INICIO DE CORRECCIÓN 🔰 🔰 🔰
    //    `admin.setDisabled` no existe. Se usa `updateUser`.
    if (typeof activo !== "undefined" && Number(activo) !== Number(current.activo)) {
      if (Number(activo) === 0) {
        // Desactivar: deshabilitar y revocar sesiones
        fbUpdate.disabled = true;
        await admin.auth().updateUser(uid, fbUpdate);
        // `admin.revokeUserSessions` no existe. Es `revokeRefreshTokens`
        await admin.auth().revokeRefreshTokens(uid);
        await db.ref(`sesiones_revocadas/${uid}`).set(true);
      } else if (Number(activo) === 1) {
        // Reactivar: habilitar
        fbUpdate.disabled = false;
        await admin.auth().updateUser(uid, fbUpdate);
        await db.ref(`sesiones_revocadas/${uid}`).remove();
      }
    } else if (Object.keys(fbUpdate).length > 0) {
      // Si no hubo cambio de 'activo', pero sí de otros datos, aplicar
      await admin.auth().updateUser(uid, fbUpdate);
    }
    // 🔰 🔰 🔰 FIN DE CORRECCIÓN 🔰 🔰 🔰

    // 3) Actualizar en MySQL
    await connection.query("CALL M1_ACTUALIZAR_USUARIO(?,?,?,?,?,?)", [
      id,
      uid,
      nombre_usuario ?? current.nombre_usuario,
      email ?? current.correo,
      rol ?? current.rol,
      typeof activo === "undefined" ? current.activo : activo,
    ]);

    await connection.commit();
    connection.release();

    return res.status(200).json({ message: "Usuario actualizado con éxito en ambos sistemas." });
  } catch (error) {
    if (connection) {
      try { await connection.rollback(); } catch (_) {}
      connection.release();
    }
    console.error(`💥 Error al actualizar usuario ${id}:`, error?.message);
    return res.status(500).json({ error: "Error en el servidor al actualizar el usuario.", details: error?.message });
  }
}

/* ============================================================================
 * DESACTIVAR (revoca sesiones inmediatamente y deshabilita en Firebase)
 * ==========================================================================*/
async function deactivate(req, res) {
  const { id } = req.params;
  let connection;

  try {
    connection = await pool.getConnection();
    await connection.beginTransaction();

    const [rows] = await connection.execute(
      "SELECT uid_firebase FROM USUARIOS WHERE id_usuario = ?",
      [id]
    );
    if (rows.length === 0) {
      throw new Error("Usuario no encontrado.");
    }
    const uid = rows[0].uid_firebase;

    // DB: marcar inactivo
    await connection.query("CALL M1_DESACTIVAR_USUARIO(?)", [id]);

    // 🔰 🔰 🔰 INICIO DE CORRECCIÓN 🔰 🔰 🔰
    // Firebase: deshabilitar + revocar sesiones
    // `admin.setDisabled` no existe. Es `updateUser`.
    await admin.auth().updateUser(uid, { disabled: true });
    // `admin.revokeUserSessions` no existe. Es `revokeRefreshTokens`
    await admin.auth().revokeRefreshTokens(uid);
    await db.ref(`sesiones_revocadas/${uid}`).set(true);
    // 🔰 🔰 🔰 FIN DE CORRECCIÓN 🔰 🔰 🔰

    await connection.commit();
    connection.release();

    return res.json({ message: "Usuario desactivado y sesiones revocadas." });
  } catch (err) {
    if (connection) {
      try { await connection.rollback(); } catch (_) {}
      connection.release();
    }
    return res.status(500).json({ error: err.message });
  }
}

/* ============================================================================
 * ELIMINAR (revoca y borra en Firebase; elimina/borrado lógico en MySQL)
 * ==========================================================================*/
async function remove(req, res) {
  const { id } = req.params;
  let connection;

  try {
    connection = await pool.getConnection();
    await connection.beginTransaction();

    const [rows] = await connection.execute(
      "SELECT uid_firebase FROM USUARIOS WHERE id_usuario = ?",
      [id]
    );
    if (rows.length === 0) {
      throw new Error("Usuario no encontrado.");
    }
    const uid = rows[0].uid_firebase;

    // 🔰 🔰 🔰 INICIO DE CORRECCIÓN 🔰 🔰 🔰
    // Revocar cualquier sesión activa antes de eliminar
    // `admin.revokeUserSessions` no existe. Es `revokeRefreshTokens`
    try {
        await admin.auth().revokeRefreshTokens(uid);
    } catch (e) {
        console.warn(`Advertencia: No se pudo revocar tokens para ${uid} (quizás ya estaba inactivo). Continuando con el borrado.`, e.message);
    }
    await db.ref(`sesiones_revocadas/${uid}`).set(true);
    // `admin.auth().deleteUser(uid)` SÍ es correcto.
    await admin.auth().deleteUser(uid);
    // 🔰 🔰 🔰 FIN DE CORRECCIÓN 🔰 🔰 🔰

    // Borrado en tu DB
    await connection.query("CALL M1_ELIMINAR_USUARIO(?)", [id]);

    await connection.commit();
    connection.release();

    return res.json({ message: "Usuario eliminado exitosamente y sesiones revocadas." });
  } catch (error) {
    if (connection) {
      try { await connection.rollback(); } catch (_) {}
      connection.release();
    }
    return res.status(500).json({ error: "Error en el servidor.", details: error?.message });
  }
}

module.exports = { list, getById, create, update, deactivate, remove };