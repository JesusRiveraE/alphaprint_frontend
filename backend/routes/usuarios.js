// routes/usuarios.js
const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/usuariosController");

router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);
router.post("/", ctrl.create);
router.put("/:id", ctrl.update);
router.delete("/:id", ctrl.remove);
router.patch("/:id/estado", ctrl.changeState);

module.exports = router;
