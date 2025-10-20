// routes/valoraciones.js
const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/valoracionesController");

router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);
router.post("/", ctrl.create);
router.delete("/:id", ctrl.remove);

module.exports = router;
