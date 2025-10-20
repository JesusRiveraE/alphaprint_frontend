// routes/historial.js
const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/historialController");

router.get("/", ctrl.list);                  // listar todo el historial
router.get("/pedido/:idPedido", ctrl.getByPedido); // historial de un pedido específico

module.exports = router;
