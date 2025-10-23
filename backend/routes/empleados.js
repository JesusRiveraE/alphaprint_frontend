const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/empleadosController"); // Tu controlador

// 🔰 1. Importamos el "guardia" de seguridad
const { verifyTokenAndAdmin } = require('../middlewares/authMiddleware');

// --- Rutas Protegidas (Solo para Administradores) ---
// Aplicamos 'verifyTokenAndAdmin' a todas las rutas que modifican datos.

// POST /api/empleados (Crear)
router.post("/", verifyTokenAndAdmin, ctrl.create);

// PUT /api/empleados/:id (Actualizar)
router.put("/:id", verifyTokenAndAdmin, ctrl.update);

// DELETE /api/empleados/:id (Eliminar)
router.delete("/:id", verifyTokenAndAdmin, ctrl.remove);

// --- Rutas Públicas ---
// Dejamos que 'list' y 'getById' sean públicas para que todos las vean.
router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);

module.exports = router;