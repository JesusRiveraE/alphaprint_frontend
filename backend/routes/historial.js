const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/historialController");

router.get("/", ctrl.list);
router.get("/pedido/:id_pedido", ctrl.listByPedido);

module.exports = router;
