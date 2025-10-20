const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/archivosController");

// 📁 Listar todos los archivos
router.get("/", ctrl.listAll);

// 📂 Listar archivos de un pedido específico
router.get("/pedido/:id_pedido", ctrl.listByPedido);

// 🔍 Obtener un archivo por ID
router.get("/:id", ctrl.getById);

// ➕ Crear un nuevo archivo
router.post("/", ctrl.create);

// ❌ Eliminar un archivo
router.delete("/:id", ctrl.remove);

module.exports = router;
