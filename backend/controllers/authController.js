// controllers/authController.js
const db = require("../db");
const admin = require("firebase-admin");

// ===============================================
// 🔧 Inicialización de Firebase Admin
// ===============================================
if (!admin.apps.length) {
  const serviceAccount = require("../firebase-service-account.json");
  admin.initializeApp({
    credential: admin.credential.cert(serviceAccount),
  });
}

// ===============================================
// 🔹 Sincronizar usuario (Firebase → MySQL)
// ===============================================
async function syncUser(req, res) {
  try {
    const { token } = req.body;
    if (!token) return res.status(400).json({ error: "Token requerido" });

    const decoded = await admin.auth().verifyIdToken(token);
    const { uid, email, name } = decoded;

    const [exists] = await db.query(
      "SELECT * FROM USUARIOS WHERE uid_firebase = ?",
      [uid]
    );

    if (exists.length === 0) {
      await db.query("CALL M1_CREAR_USUARIO(?, ?, ?, ?)", [
        uid,
        name || email.split("@")[0],
        email,
        "Cliente",
      ]);
    }

    res.json({ success: true });
  } catch (err) {
    console.error("Error en /auth/sync:", err);
    res.status(500).json({ error: err.message });
  }
}

// ===============================================
// 🔹 Eliminar usuario (Firebase + MySQL)
//    - Borra en Firebase (si existe)
//    - En MySQL: intenta borrar; si está vinculado a PERSONAL → soft delete (activo = false)
// ===============================================
async function deleteUser(req, res) {
  const { uid } = req.params; // /auth/delete/:uid

  try {
    // 1) Borra en Firebase si existe (si no existe, seguimos)
    let firebaseDeleted = false;
    try {
      await admin.auth().deleteUser(uid);
      firebaseDeleted = true;
    } catch (e) {
      // Si ya no existe en Firebase, continuamos con MySQL
      if (e.code !== "auth/user-not-found") {
        console.warn("Firebase delete error:", e.code, e.message);
      }
    }

    // 2) MySQL: localizar usuario por uid_firebase
    const [user] = await db.query(
      "SELECT id_usuario FROM USUARIOS WHERE uid_firebase = ?",
      [uid]
    );

    if (user.length === 0) {
      return res.json({
        success: true,
        firebaseDeleted,
        mysql: "already-missing",
        message: "Usuario no encontrado en MySQL",
      });
    }

    const id = user[0].id_usuario;

    // 3) Intentar eliminación dura
    try {
      await db.query("CALL M1_ELIMINAR_USUARIO(?)", [id]);
      return res.json({
        success: true,
        firebaseDeleted,
        mysql: "deleted",
        message: "Usuario eliminado de MySQL",
      });
    } catch (err) {
      // Si está vinculado a PERSONAL (SQLSTATE '45000'), hacemos soft delete
      const isLinkedToPersonal =
        err?.sqlState === "45000" ||
        (err?.message || "").includes("vinculado a PERSONAL");

      if (isLinkedToPersonal) {
        await db.query("CALL M1_DESACTIVAR_USUARIO(?)", [id]);
        return res.json({
          success: true,
          firebaseDeleted,
          mysql: "soft-deactivated",
          message:
            "Usuario vinculado a PERSONAL: marcado como inactivo en MySQL",
        });
      }
      throw err;
    }
  } catch (err) {
    console.error("Error al eliminar usuario:", err);
    res.status(500).json({ error: err.message });
  }
}

module.exports = { syncUser, deleteUser };
