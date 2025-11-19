// routes/bitacora.js
const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/bitacoraController");

// GET /api/bitacora
router.get("/", ctrl.list);

module.exports = router;
