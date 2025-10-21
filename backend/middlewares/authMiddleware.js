const admin = require('../config/firebase-config');
const pool = require('../db');

// ==========================================================
// 🔧 NUEVO MIDDLEWARE (Solo para probar)
// Verifica que el usuario simplemente haya iniciado sesión.
// ==========================================================
const verifyToken = async (req, res, next) => {
    const authHeader = req.headers.authorization;

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(403).json({ error: 'Acceso denegado. Se requiere un token.' });
    }
    const idToken = authHeader.split('Bearer ')[1];

    try {
        // Solo verificamos que el token sea válido. No revisamos el rol.
        await admin.auth().verifyIdToken(idToken);
        next(); // Si el token es válido, permite que la petición continúe.
    } catch (error) {
        res.status(401).json({ error: 'Token inválido o expirado.' });
    }
};


// ==========================================================
// 🛡️ MIDDLEWARE DE ADMINISTRADOR (Para cuando tus roles estén listos)
// Este lo dejaremos aquí para el futuro.
// ==========================================================
const verifyTokenAndAdmin = async (req, res, next) => {
    const authHeader = req.headers.authorization;

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(403).json({ error: 'Acceso denegado. Se requiere un token.' });
    }
    const idToken = authHeader.split('Bearer ')[1];

    try {
        const decodedToken = await admin.auth().verifyIdToken(idToken);
        const uid = decodedToken.uid;

        const [rows] = await pool.execute('SELECT rol FROM USUARIOS WHERE uid_firebase = ?', [uid]);

        if (rows.length === 0 || rows[0].rol !== 'Admin') {
            return res.status(403).json({ error: 'Acceso denegado. No tienes permisos de administrador.' });
        }
        
        next();
    } catch (error) {
        res.status(401).json({ error: 'Token inválido o expirado.' });
    }
};

// Exportamos ambas funciones
module.exports = { verifyToken, verifyTokenAndAdmin };