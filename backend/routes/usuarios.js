const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/usuariosController");
// Importamos el middleware de seguridad
const { verifyTokenAndAdmin } = require('../middlewares/authMiddleware');

// 1. IMPORTAMOS las herramientas de 'express-validator'
const { check } = require('express-validator');

// 2. DEFINIMOS EL ARRAY DE REGLAS DE VALIDACIÓN PARA CREAR
//    (Basado en tu captura de pantalla)
const validacionesCrearUsuario = [
    check('email', 'El email proporcionado no es válido')
        .isEmail()
        .normalizeEmail(),

    check('nombreUsuario', 'El nombre de usuario es obligatorio')
        .not().isEmpty()
        .trim(),
    check('nombreUsuario', 'El nombre de usuario no debe contener caracteres especiales (<,>,{,},[,],etc)')
        .isAlphanumeric('es-ES', { ignore: ' _-' }), // Permite letras, números, espacios, guiones
    check('nombreUsuario', 'El nombre de usuario debe tener entre 4 y 30 caracteres')
        .isLength({ min: 4, max: 30 }),

    check('password', 'El password debe tener al menos 6 caracteres')
        .isLength({ min: 6 }),

    check('rol', 'El rol seleccionado no es válido')
        .isIn(['Admin', 'Empleado']),
];

// 3. DEFINIMOS EL ARRAY DE REGLAS DE VALIDACIÓN PARA ACTUALIZAR (¡NUEVO!)
//    Usamos .optional() para que solo valide los campos que se envían.
//    Nota: el campo es 'nombre_usuario' según tu controlador de update.
const validacionesActualizarUsuario = [
    check('email', 'El email proporcionado no es válido')
        .optional() // Solo valida si 'email' se envía en el body
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


// --- Rutas Públicas ---
// Estas rutas no modifican datos, así que pueden ser públicas.
router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);

// --- Rutas Protegidas (Solo para Administradores) ---
// Nos aseguramos de que TODAS las rutas que crean o modifican
// tengan el "guardia de seguridad" verifyTokenAndAdmin.

// Ruta POST (Crear)
router.post(
    "/",
    verifyTokenAndAdmin,       // 1. Middleware de Auth
    validacionesCrearUsuario,  // 2. Middleware de Validación
    ctrl.create                // 3. Controlador
);

// Ruta PUT (Actualizar) - ¡MODIFICADA!
router.put(
    "/:id",
    verifyTokenAndAdmin,             // 1. Middleware de Auth
    validacionesActualizarUsuario, // 2. Middleware de Validación (¡Nuevo!)
    ctrl.update                    // 3. Controlador
);

// (Otras rutas sin cambios)
router.put("/:id/desactivar", verifyTokenAndAdmin, ctrl.deactivate);
router.delete("/:id", verifyTokenAndAdmin, ctrl.remove); // Esta ya estaba correcta

module.exports = router;