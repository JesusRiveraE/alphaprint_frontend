// routes/bitacora.js
const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/bitacoraController");

// Listar bitácora
router.get("/", ctrl.list);

module.exports = router;
