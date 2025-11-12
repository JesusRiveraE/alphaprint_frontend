const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/usuariosController");

// 🔰 1. IMPORTAMOS AMBOS MIDDLEWARES
const { verifyToken, verifyTokenAndAdmin } = require('../middlewares/authMiddleware');

const { check } = require('express-validator');

// (Tus reglas de validación están bien, las dejamos igual)
const validacionesCrearUsuario = [
    check('email', 'El email proporcionado no es válido')
        .isEmail()
        .normalizeEmail(),
    check('nombreUsuario', 'El nombre de usuario es obligatorio')
        .not().isEmpty()
        .trim(),
    check('nombreUsuario', 'El nombre de usuario no debe contener caracteres especiales (<,>,{,},[,],etc)')
        .isAlphanumeric('es-ES', { ignore: ' _-' }),
    check('nombreUsuario', 'El nombre de usuario debe tener entre 4 y 30 caracteres')
        .isLength({ min: 4, max: 30 }),
    check('password', 'El password debe tener al menos 6 caracteres')
        .isLength({ min: 6 }),
    check('rol', 'El rol seleccionado no es válido')
        .isIn(['Admin', 'Empleado']),
];

const validacionesActualizarUsuario = [
    check('email', 'El email proporcionado no es válido')
        .optional()
        .isEmail()
        .normalizeEmail(),
    check('nombre_usuario', 'El nombre de usuario no debe contener caracteres especiales (<,>,{,},[,],etc)')
        .optional()
        .isAlphanumeric('es-ES', { ignore: ' _-' })
        .trim(),
    check('nombre_usuario', 'El nombre de usuario debe tener entre 4 y 30 caracteres')
        .optional()
        .isLength({ min: 4, max: 30 }),
    check('password', 'El password debe tener al menos 6 caracteres')
        .optional()
        .isLength({ min: 6 }),
    check('rol', 'El rol seleccionado no es válido')
        .optional()
        .isIn(['Admin', 'Empleado']),
];

// 🔰 2. APLICAMOS EL GUARDIA GENERAL (LA CORRECCIÓN DE SEGURIDAD)
// Esto protege TODAS las rutas de usuarios (incluyendo GET)
// para que solo usuarios autenticados puedan acceder.
router.use(verifyToken);

// --- Rutas de solo lectura ---
// (Ya están protegidas por el router.use() de arriba)
router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);

// --- Rutas Protegidas (Solo para Administradores) ---
// Añadimos el "guardia de seguridad" extra (verifyTokenAndAdmin)
// solo a las rutas que modifican datos.

router.post(
    "/",
    verifyTokenAndAdmin,      // 1. ¿Es Admin?
    validacionesCrearUsuario,   // 2. ¿Los datos son válidos?
    ctrl.create                 // 3. Crear
);

router.put(
    "/:id",
    verifyTokenAndAdmin,
    validacionesActualizarUsuario,
    ctrl.update
);

router.put("/:id/desactivar", verifyTokenAndAdmin, ctrl.deactivate);
router.delete("/:id", verifyTokenAndAdmin, ctrl.remove);

module.exports = router;