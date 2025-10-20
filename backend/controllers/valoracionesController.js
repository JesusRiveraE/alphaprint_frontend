// controllers/valoracionesController.js
const db = require("../db");

async function list(req, res) {
  try {
    const [results] = await db.query("CALL M2_LISTAR_VALORACIONES()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function getById(req, res) {
  try {
    const [results] = await db.query("CALL M2_OBTENER_VALORACION(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function create(req, res) {
  try {
    const { id_cliente, calificacion, comentario } = req.body;
    await db.query("CALL M2_CREAR_VALORACION(?,?,?)", [id_cliente || null, calificacion, comentario]);
    res.json({ message: "Valoración creada con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function remove(req, res) {
  try {
    await db.query("CALL M2_ELIMINAR_VALORACION(?)", [req.params.id]);
    res.json({ message: "Valoración eliminada con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, getById, create, remove };
