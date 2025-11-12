// backend/routes/empleados.js
const express = require("express");
const router = express.Router();

const ctrl = require("../controllers/empleadosController");
// 🔐 Middlewares de autenticación y autorización
const { verifyToken, verifyTokenAndAdmin } = require("../middlewares/authMiddleware");

/**
 * Aplica verificación de sesión Firebase a TODO el router.
 * - verifyToken valida el ID token (incluye detección de token revocado/expirado).
 * - Luego se pueden añadir restricciones adicionales por ruta.
 */
router.use(verifyToken);

/* =========================
 *  Rutas de solo lectura
 *  (disponibles para cualquier usuario autenticado)
 * ========================= */
router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);

/* =========================
 *  Rutas de modificación
 *  (solo para Administradores)
 * ========================= */
router.post("/", verifyTokenAndAdmin, ctrl.create);
router.put("/:id", verifyTokenAndAdmin, ctrl.update);
router.delete("/:id", verifyTokenAndAdmin, ctrl.remove);

module.exports = router;
