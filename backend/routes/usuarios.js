const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/usuariosController");
// Importamos el middleware de seguridad
const { verifyTokenAndAdmin } = require('../middlewares/authMiddleware');

// --- Rutas Públicas ---
// Estas rutas no modifican datos, así que pueden ser públicas.
router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);

// --- Rutas Protegidas (Solo para Administradores) ---
// Nos aseguramos de que TODAS las rutas que crean o modifican
// tengan el "guardia de seguridad" verifyTokenAndAdmin.

// 🔰 AQUÍ ESTÁ LA CORRECCIÓN:
// Añadimos "verifyTokenAndAdmin" a la ruta POST para crear.
router.post("/", verifyTokenAndAdmin, ctrl.create);

router.put("/:id", verifyTokenAndAdmin, ctrl.update);
router.put("/:id/desactivar", verifyTokenAndAdmin, ctrl.deactivate);
router.delete("/:id", verifyTokenAndAdmin, ctrl.remove); // Esta ya estaba correcta

module.exports = router;