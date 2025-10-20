// routes/notificaciones.js
const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/notificacionesController");

router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);
router.patch("/:id/leida", ctrl.markRead);
router.delete("/:id", ctrl.remove);

module.exports = router;
