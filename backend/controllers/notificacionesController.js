// controllers/notificacionesController.js
const db = require("../db");

async function list(req, res) {
  try {
    const [results] = await db.query("CALL M2_LISTAR_NOTIFICACIONES()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function getById(req, res) {
  try {
    const [results] = await db.query("CALL M2_OBTENER_NOTIFICACION(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function markRead(req, res) {
  try {
    await db.query("CALL M2_MARCAR_NOTIFICACION_LEIDA(?)", [req.params.id]);
    res.json({ message: "Notificación marcada como leída" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function remove(req, res) {
  try {
    await db.query("CALL M2_ELIMINAR_NOTIFICACION(?)", [req.params.id]);
    res.json({ message: "Notificación eliminada con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, getById, markRead, remove };
