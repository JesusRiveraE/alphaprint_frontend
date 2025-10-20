// routes/pedidos.js
const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/pedidosController");

router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);
router.post("/", ctrl.create);
router.put("/:id", ctrl.update);
router.patch("/:id/estado", ctrl.changeState);
router.delete("/:id", ctrl.remove);

module.exports = router;
