// backend/middlewares/authMiddleware.js
const admin = require('../config/firebase-config');
const pool = require('../db');

/* ============================================================================
 * 🧩 MIDDLEWARE GENERAL: Verifica sesión, token revocado y rol en MySQL
 * ==========================================================================*/
const verifyToken = async (req, res, next) => {
  const authHeader = req.headers.authorization;

  if (!authHeader || !authHeader.startsWith('Bearer ')) {
    return res
      .status(403)
      .json({ error: 'Acceso denegado. Se requiere un token.' });
  }

  const idToken = authHeader.split('Bearer ')[1];

  try {
    // Verificamos el token de Firebase (incluyendo revocación)
    const decoded = await admin.auth().verifyIdToken(idToken, true);
    const uid = decoded.uid;

    // Buscamos el rol del usuario en la DB local
    const [rows] = await pool.execute(
      'SELECT rol, activo FROM USUARIOS WHERE uid_firebase = ?',
      [uid]
    );

    if (rows.length === 0) {
      return res
        .status(404)
        .json({ error: 'Usuario no encontrado en la base de datos local.' });
    }

    const usuario = rows[0];

    if (!usuario.activo) {
      return res
        .status(403)
        .json({ error: 'Cuenta inactiva. Contacta al administrador.' });
    }

    // ✅ Adjuntamos el usuario al request:
    //    - Conservamos todo lo decodificado del token,
    //    - y agregamos el rol proveniente de MySQL.
    req.user = {
      ...decoded,
      role: usuario.rol, // 'Admin', 'Empleado', 'Cliente'
    };

    return next();
  } catch (error) {
    const code = error?.errorInfo?.code || error?.code || '';

    if (code === 'auth/id-token-revoked') {
      return res.status(401).json({
        error: 'Token revocado. Debes iniciar sesión nuevamente.',
        revoked: true, // para que el frontend sepa que debe desloguear
      });
    }

    // Errores típicos de Firebase (token inválido, expirado, etc.)
    if (code && code.startsWith('auth/')) {
      return res.status(401).json({
        error: 'Token inválido o expirado.',
      });
    }

    // Cualquier otro error (por ejemplo, de MySQL)
    console.error('Error al verificar token/rol del usuario:', error);
    return res.status(500).json({
      error: 'Error interno al validar usuario en la base de datos local.',
    });
  }
};

/* ============================================================================
 * 🛡️ MIDDLEWARE ADMINISTRADOR
 * - Usa verifyToken y verifica que el rol sea 'Admin'
 * ==========================================================================*/
const verifyTokenAndAdmin = (req, res, next) => {
  // Reutilizamos verifyToken para no duplicar lógica
  verifyToken(req, res, () => {
    if (!req.user || req.user.role !== 'Admin') {
      return res
        .status(403)
        .json({ error: 'Acceso denegado. No tienes permisos de administrador.' });
    }

    return next();
  });
};

module.exports = { verifyToken, verifyTokenAndAdmin };
