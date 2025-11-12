// backend/routes/auth.js
const express = require('express');
const router = express.Router();
const admin = require('../config/firebase-config');
const pool = require('../db');

/* -------------------------------------------------------------------------- */
/*  Middleware: verifica ID Token con revocación                               */
/* -------------------------------------------------------------------------- */
const verifyToken = async (req, res, next) => {
  const idToken = req.headers.authorization?.startsWith('Bearer ')
    ? req.headers.authorization.split('Bearer ')[1]
    : null;

  if (!idToken) {
    return res.status(403).json({ error: 'Token no proporcionado.' });
  }

  try {
    // checkRevoked: true hace que fallen tokens revocados inmediatamente
    const decoded = await admin.auth().verifyIdToken(idToken, true);
    req.user = decoded;
    return next();
  } catch (err) {
    // Errores comunes: auth/id-token-revoked, auth/argument-error, etc.
    const code = err?.errorInfo?.code || err?.code || '';
    if (code === 'auth/id-token-revoked') {
      return res.status(401).json({ error: 'Token revocado. Debe iniciar sesión nuevamente.' });
    }
    return res.status(401).json({ error: 'Token inválido o expirado.' });
  }
};

/* -------------------------------------------------------------------------- */
/*  Registro (Admin/Empleado)                                                  */
/* -------------------------------------------------------------------------- */
router.post('/register', async (req, res) => {
  const { email, password, nombreUsuario, rol } = req.body;

  if (rol !== 'Admin' && rol !== 'Empleado') {
    return res.status(400).json({ error: 'El rol debe ser "Admin" o "Empleado".' });
  }

  let connection;
  try {
    const userRecord = await admin.auth().createUser({
      email,
      password,
      displayName: nombreUsuario,
    });

    connection = await pool.getConnection();
    await connection.beginTransaction();

    await connection.execute('CALL M1_CREAR_USUARIO(?, ?, ?, ?)', [
      userRecord.uid,
      nombreUsuario,
      email,
      rol,
    ]);

    if (rol === 'Empleado') {
      const [result] = await connection.query(
        'SELECT id_usuario FROM USUARIOS WHERE uid_firebase = ?',
        [userRecord.uid]
      );
      if (result.length > 0) {
        const idUsuario = result[0].id_usuario;
        await connection.execute('CALL M4_CREAR_EMPLEADO(?, ?, ?, ?)', [
          nombreUsuario,
          null,
          'Otro',
          idUsuario,
        ]);
      }
    }

    await connection.commit();
    connection.release();
    return res
      .status(201)
      .json({ message: 'Usuario y personal creados correctamente.', uid: userRecord.uid });
  } catch (firebaseError) {
    console.error('Error al crear usuario en Firebase:', firebaseError?.message);
    try {
      if (firebaseError?.code !== 'auth/email-already-exists' && firebaseError?.uid) {
        await admin.auth().deleteUser(firebaseError.uid);
      }
    } catch (_) {}

    if (connection) {
      try {
        await connection.rollback();
      } finally {
        connection.release();
      }
    }
    return res
      .status(500)
      .json({ error: 'Error al registrar usuario.', details: firebaseError?.message });
  }
});

/* -------------------------------------------------------------------------- */
/*  Sincronización/Login: valida token (con revocación) y estado en DB         */
/* -------------------------------------------------------------------------- */
router.post('/sync', async (req, res) => {
  const { token } = req.body; // enviado desde el frontend

  if (!token) {
    return res.status(400).json({ error: 'Token no proporcionado.' });
  }

  let connection;
  try {
    // Si fue revocado, lanzará auth/id-token-revoked
    const decoded = await admin.auth().verifyIdToken(token, true);
    const uid = decoded.uid;

    // (Opcional extra) también podemos comprobar si el usuario está disabled en Firebase
    const fbUser = await admin.auth().getUser(uid);
    if (fbUser.disabled) {
      return res.status(401).json({ error: 'La cuenta está deshabilitada.' });
    }

    connection = await pool.getConnection();
    const [rows] = await connection.execute(
      'SELECT rol, activo FROM USUARIOS WHERE uid_firebase = ?',
      [uid]
    );

    if (rows.length === 0) {
      connection.release();
      return res.status(404).json({ error: 'Usuario no encontrado en la base de datos.' });
    }

    const userDB = rows[0];
    if (!userDB.activo) {
      connection.release();
      return res.status(401).json({ error: 'La cuenta del usuario está inactiva.' });
    }

    connection.release();
    return res.status(200).json({ message: 'Inicio de sesión exitoso.', rol: userDB.rol, uid });
  } catch (err) {
    if (connection) connection.release();

    const code = err?.errorInfo?.code || err?.code || '';
    if (code === 'auth/id-token-revoked') {
      return res.status(401).json({ error: 'Token revocado. Inicie sesión nuevamente.' });
    }
    return res.status(500).json({
      error: 'Error al verificar el token de inicio de sesión.',
      details: err?.message,
    });
  }
});

/* -------------------------------------------------------------------------- */
/*  Desactivar usuario (revoca sesiones)                                       */
/*  - Marca activo=0 en DB                                                     */
/*  - Deshabilita en Firebase y revoca refresh tokens                          */
/* -------------------------------------------------------------------------- */
router.patch('/deactivate/:uid', verifyToken, async (req, res) => {
  const { uid } = req.params;
  let connection;

  try {
    connection = await pool.getConnection();
    await connection.beginTransaction();

    await connection.execute('UPDATE USUARIOS SET activo = 0 WHERE uid_firebase = ?', [uid]);

    // Deshabilita en Firebase (evita nuevos logins) y revoca sesiones activas
    await admin.auth().updateUser(uid, { disabled: true });
    await admin.auth().revokeRefreshTokens(uid);

    await connection.commit();
    connection.release();

    return res.status(200).json({
      message: 'Usuario desactivado y sesiones revocadas.',
      uid,
    });
  } catch (err) {
    if (connection) {
      try {
        await connection.rollback();
      } finally {
        connection.release();
      }
    }
    return res.status(500).json({ error: 'No se pudo desactivar al usuario.', details: err?.message });
  }
});

/* -------------------------------------------------------------------------- */
/*  Reactivar usuario (opcional)                                               */
/*  - Marca activo=1 en DB                                                     */
/*  - Habilita en Firebase                                                     */
/* -------------------------------------------------------------------------- */
router.patch('/reactivate/:uid', verifyToken, async (req, res) => {
  const { uid } = req.params;
  let connection;

  try {
    connection = await pool.getConnection();
    await connection.beginTransaction();

    await connection.execute('UPDATE USUARIOS SET activo = 1 WHERE uid_firebase = ?', [uid]);

    await admin.auth().updateUser(uid, { disabled: false });
    // No se revocan tokens aquí; el usuario deberá autenticarse nuevamente si corresponde.

    await connection.commit();
    connection.release();

    return res.status(200).json({ message: 'Usuario reactivado.', uid });
  } catch (err) {
    if (connection) {
      try {
        await connection.rollback();
      } finally {
        connection.release();
      }
    }
    return res.status(500).json({ error: 'No se pudo reactivar al usuario.', details: err?.message });
  }
});

/* -------------------------------------------------------------------------- */
/*  Eliminar usuario (revoca y borra)                                          */
/*  - Marca activo=0 o elimina en DB (según tu modelo)                         */
/*  - Revoca tokens y elimina en Firebase                                      */
/* -------------------------------------------------------------------------- */
router.delete('/delete/:uid', verifyToken, async (req, res) => {
  const { uid } = req.params;
  let connection;

  try {
    connection = await pool.getConnection();
    await connection.beginTransaction();

    // Si tienes SP para borrado lógico/físico, úsalo; aquí hacemos borrado lógico.
    await connection.execute('UPDATE USUARIOS SET activo = 0 WHERE uid_firebase = ?', [uid]);

    // Revoca sesiones activas y elimina la cuenta en Firebase
    await admin.auth().revokeRefreshTokens(uid);
    await admin.auth().deleteUser(uid);

    await connection.commit();
    connection.release();

    return res.status(200).json({ message: 'Usuario eliminado y sesiones revocadas.', uid });
  } catch (err) {
    if (connection) {
      try {
        await connection.rollback();
      } finally {
        connection.release();
      }
    }
    return res.status(500).json({ error: 'No se pudo eliminar al usuario.', details: err?.message });
  }
});

module.exports = router;
