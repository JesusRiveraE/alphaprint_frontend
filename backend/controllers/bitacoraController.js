const db = require("../db");

// Listar todos los registros de bitácora
async function list(req, res) {
  try {
    const [results] = await db.query("SELECT * FROM BITACORA ORDER BY fecha DESC");
    res.json(results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Listar por usuario
async function listByUser(req, res) {
  try {
    const [results] = await db.query("SELECT * FROM BITACORA WHERE id_usuario = ? ORDER BY fecha DESC", [
      req.params.userId,
    ]);
    res.json(results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Registrar acción manual
async function create(req, res) {
  try {
    const { id_usuario, modulo, accion } = req.body;
    await db.query("CALL M1_REGISTRAR_BITACORA(?,?,?)", [id_usuario, modulo, accion]);
    res.json({ message: "Registro agregado en bitácora" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, listByUser, create };
