// controllers/bitacoraController.js
const db = require("../db");

async function list(req, res) {
  try {
    const [results] = await db.query("CALL M1_LISTAR_BITACORA()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function getById(req, res) {
  try {
    const [results] = await db.query("CALL M1_OBTENER_BITACORA(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function listByUser(req, res) {
  try {
    const [results] = await db.query("CALL M1_LISTAR_BITACORA_USUARIO(?)", [req.params.userId]);
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function create(req, res) {
  try {
    const { id_usuario, modulo, accion } = req.body;
    await db.query("CALL M1_REGISTRAR_BITACORA(?,?,?)", [id_usuario, modulo, accion]);
    res.json({ message: "Registro de bitácora creado" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function remove(req, res) {
  try {
    await db.query("CALL M1_ELIMINAR_BITACORA(?)", [req.params.id]);
    res.json({ message: "Registro de bitácora eliminado" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, getById, listByUser, create, remove };
