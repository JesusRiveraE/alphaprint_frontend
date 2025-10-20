const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/usuariosController");

router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);
router.post("/", ctrl.create);
router.put("/:id", ctrl.update);
router.put("/:id/desactivar", ctrl.deactivate);
router.delete("/:id", ctrl.remove);

module.exports = router;
