// controllers/notificacionesController.js
const db = require("../db");

// GET /api/notificaciones
async function list(req, res) {
  try {
    const [results] = await db.query("CALL M2_LISTAR_NOTIFICACIONES()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ ok: false, error: err.message });
  }
}

// PUT /api/notificaciones/:id/leido
async function markAsRead(req, res) {
  try {
    await db.query("CALL M2_MARCAR_NOTIFICACION(?)", [req.params.id]);
    res.json({ ok: true, message: "Notificación marcada como leída" });
  } catch (err) {
    res.status(500).json({ ok: false, error: err.message });
  }
}

module.exports = { list, markAsRead };
