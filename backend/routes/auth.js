/* routes/auth.js
const express = require("express");
const router = express.Router();
const { syncUser, deleteUser } = require("../controllers/authController");

router.post("/sync", syncUser);
router.delete("/delete/:uid", deleteUser); // Nueva ruta para eliminar usuario

module.exports = router;*/



// backend/routes/auth.js
const express = require('express');
const router = express.Router();
const admin = require('../config/firebase-config');
const pool = require('../db');

// Middleware para verificar el token de Firebase (protege rutas)
const verifyToken = async (req, res, next) => {
  const idToken = req.headers.authorization?.split('Bearer ')[1];

  if (!idToken) {
    return res.status(403).json({ error: 'Token no proporcionado.' });
  }

  try {
    const decodedToken = await admin.auth().verifyIdToken(idToken);
    req.user = decodedToken;
    next();
  } catch (error) {
    res.status(401).json({ error: 'Token inválido o expirado.' });
  }
};

// ===========================================
// 📝 RUTA DE REGISTRO
// (Esta ruta se mantiene igual, no se usa desde login.js)
// ===========================================
router.post('/register', async (req, res) => {
  const { email, password, nombreUsuario, rol } = req.body;
  if (rol !== 'Admin' && rol !== 'Empleado') {
    return res.status(400).json({ error: 'El rol debe ser "Admin" o "Empleado".' });
  }
  
  let connection;
  try {
    const userRecord = await admin.auth().createUser({ email, password, displayName: nombreUsuario });
    connection = await pool.getConnection();
    await connection.beginTransaction();

    await connection.execute('CALL M1_CREAR_USUARIO(?, ?, ?, ?)', [
      userRecord.uid, nombreUsuario, email, rol
    ]);

    if (rol === 'Empleado') {
      const [result] = await connection.query('SELECT id_usuario FROM USUARIOS WHERE uid_firebase = ?', [userRecord.uid]);
      if (result.length > 0) {
        const idUsuario = result[0].id_usuario;
        await connection.execute('CALL M4_CREAR_EMPLEADO(?, ?, ?, ?)', [
          nombreUsuario, null, 'Otro', idUsuario
        ]);
      }
    }

    await connection.commit();
    connection.release();
    res.status(201).json({ message: 'Usuario y personal creados correctamente.', uid: userRecord.uid });
  } catch (firebaseError) {
    console.error('Error al crear usuario en Firebase:', firebaseError.message);
    if (firebaseError.code !== 'auth/email-already-exists' && firebaseError.uid) {
        await admin.auth().deleteUser(firebaseError.uid);
    }
    if (connection) {
        await connection.rollback();
        connection.release();
    }
    res.status(500).json({ error: 'Error al registrar usuario.', details: firebaseError.message });
  }
});


// ===========================================
// 📝 RUTA DE SINCRONIZACIÓN Y LOGIN
// ===========================================
router.post('/sync', async (req, res) => {
    const { token } = req.body; // El token viene del frontend (login.js)

    if (!token) {
        return res.status(400).json({ error: 'Token no proporcionado.' });
    }

    let connection;
    try {
        // 1. Verificar el token de Firebase para obtener el UID del usuario
        const decodedToken = await admin.auth().verifyIdToken(token);
        const uid = decodedToken.uid;

        // 2. Conectar a la base de datos para verificar el estado de la cuenta
        connection = await pool.getConnection();
        const [rows] = await connection.execute('SELECT rol, activo FROM USUARIOS WHERE uid_firebase = ?', [uid]);

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
        res.status(200).json({ message: 'Inicio de sesión exitoso.', rol: userDB.rol, uid: uid });
    } catch (error) {
        if (connection) {
            connection.release();
        }
        res.status(500).json({ error: 'Error al verificar el token de inicio de sesión.', details: error.message });
    }
});

// Exporta el router
module.exports = router;