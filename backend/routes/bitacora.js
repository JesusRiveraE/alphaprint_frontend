// routes/bitacora.js
const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/bitacoraController");

router.get("/", ctrl.list);
router.get("/:id", ctrl.getById);
router.get("/usuario/:userId", ctrl.listByUser);
router.post("/", ctrl.create);
router.delete("/:id", ctrl.remove);

module.exports = router;
