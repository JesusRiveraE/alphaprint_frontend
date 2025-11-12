// backend/middleware/authMiddleware.js
const admin = require('../config/firebase-config');
const pool = require('../db');

/* ============================================================================
 * 🧩 MIDDLEWARE GENERAL: Verifica sesión y token revocado
 * ==========================================================================*/
const verifyToken = async (req, res, next) => {
  const authHeader = req.headers.authorization;

  if (!authHeader || !authHeader.startsWith('Bearer ')) {
    // ⛔️ CORRECCIÓN: Este era el error 403 que viste antes
    return res.status(403).json({ error: 'Acceso denegado. Se requiere un token.' });
  }

  const idToken = authHeader.split('Bearer ')[1];

  try {
    // 🔰🔰 LA CORRECCIÓN ESTÁ AQUÍ 🔰🔰
    // 'admin.verifyIdTokenChecked' no existe.
    // La forma correcta es 'admin.auth().verifyIdToken(token, checkRevoked)'
    // El 'true' activa la comprobación de revocación que querías.
    const decoded = await admin.auth().verifyIdToken(idToken, true);
    // 🔰🔰 FIN DE LA CORRECCIÓN 🔰🔰

    req.user = decoded;
    next();

  } catch (error) {
    const code = error?.errorInfo?.code || error?.code || '';

    if (code === 'auth/id-token-revoked') {
      return res.status(401).json({
        error: 'Token revocado. Debes iniciar sesión nuevamente.',
        revoked: true, // Esto le dice a tu firebase.js que debe desloguear
      });
    }

    // Esto captura tokens inválidos o expirados
    return res.status(401).json({ error: 'Token inválido o expirado.' });
  }
};

/* ============================================================================
 * 🛡️ MIDDLEWARE ADMINISTRADOR
 * - Verifica token con revocación
 * - Confirma rol 'Admin' en MySQL
 * ==========================================================================*/
const verifyTokenAndAdmin = async (req, res, next) => {
  const authHeader = req.headers.authorization;

  if (!authHeader || !authHeader.startsWith('Bearer ')) {
    return res.status(403).json({ error: 'Acceso denegado. Se requiere un token.' });
  }

  const idToken = authHeader.split('Bearer ')[1];

  try {
    // 🔰🔰 LA CORRECCIÓN ESTÁ AQUÍ 🔰🔰
    // Aplicamos la misma corrección
    const decoded = await admin.auth().verifyIdToken(idToken, true);
    // 🔰🔰 FIN DE LA CORRECCIÓN 🔰🔰

    const uid = decoded.uid;

    // Busca el rol del usuario en la DB (Tu lógica aquí es excelente)
    const [rows] = await pool.execute(
      // ❗️Asegúrate que tu columna se llame 'uid_firebase' y no 'firebase_uid' como en la memoria
      'SELECT rol, activo FROM USUARIOS WHERE uid_firebase = ?', 
      [uid]
    );

    if (rows.length === 0) {
      return res.status(404).json({ error: 'Usuario no encontrado en la base de datos local.' });
    }

    const usuario = rows[0];
    if (!usuario.activo) {
      return res.status(401).json({ error: 'Cuenta inactiva. Contacta al administrador.' });
    }

    if (usuario.rol !== 'Admin') {
      return res.status(403).json({ error: 'Acceso denegado. No tienes permisos de administrador.' });
    }

    // Token y rol válidos → continuar
    req.user = { uid, rol: usuario.rol };
    next();
  } catch (error) {
    const code = error?.errorInfo?.code || error?.code || '';

    if (code === 'auth/id-token-revoked') {
      return res.status(401).json({
        error: 'Token revocado. Debes iniciar sesión nuevamente.',
        revoked: true,
      });
    }

    return res.status(401).json({ error: 'Token inválido o expirado.' });
  }
};

module.exports = { verifyToken, verifyTokenAndAdmin };