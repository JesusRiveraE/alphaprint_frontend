// routes/auth.js
const express = require("express");
const router = express.Router();
const { syncUser, deleteUser } = require("../controllers/authController");

router.post("/sync", syncUser);
router.delete("/delete/:uid", deleteUser); // Nueva ruta para eliminar usuario

module.exports = router;
