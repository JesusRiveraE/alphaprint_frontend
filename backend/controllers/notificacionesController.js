const db = require("../db");

// Listar notificaciones
async function list(req, res) {
  try {
    const [results] = await db.query("CALL M2_LISTAR_NOTIFICACIONES()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Marcar notificación como leída
async function markAsRead(req, res) {
  try {
    await db.query("CALL M2_MARCAR_NOTIFICACION(?)", [req.params.id]);
    res.json({ message: "Notificación marcada como leída" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, markAsRead };
