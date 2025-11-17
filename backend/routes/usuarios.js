const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/usuariosController");

// 🔰 Importamos el middleware con rol desde MySQL
const { verifyTokenAndAdmin } = require('../middlewares/authMiddleware');

const { check } = require('express-validator');

// ✅ Reglas de validación para crear usuario
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

// ✅ Reglas de validación para actualizar usuario
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

/* ============================================================================
 * 🛡️ GUARDIA GLOBAL DEL MÓDULO USUARIOS
 * 
 * Todo lo que sea /api/usuarios/** es parte de ADMINISTRACIÓN.
 * Solo un usuario con rol 'Admin' (en la tabla USUARIOS, activo = 1)
 * puede acceder a cualquiera de estas rutas.
 * ========================================================================= */
router.use(verifyTokenAndAdmin);

// --- Rutas de solo lectura (solo Admin) ---
router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);

// --- Rutas que modifican datos (también solo Admin, ya protegidas arriba) ---

router.post(
    "/",
    validacionesCrearUsuario,
    ctrl.create
);

router.put(
    "/:id",
    validacionesActualizarUsuario,
    ctrl.update
);

router.put("/:id/desactivar", ctrl.deactivate);

router.delete("/:id", ctrl.remove);

module.exports = router;
