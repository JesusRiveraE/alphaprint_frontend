// controllers/bitacoraController.js
const db = require("../db");

// GET /api/bitacora
async function list(req, res) {
  try {
    const [rows] = await db.query("CALL M1_LISTAR_BITACORA()");
    // En MySQL con CALL, los datos vienen en rows[0]
    const data = rows?.[0] ?? rows ?? [];
    res.json(data);
  } catch (err) {
    console.error("bitacora.list error:", err);
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list };