const db = require("../db");

// Listar todas las valoraciones
async function list(req, res) {
  try {
    const [results] = await db.query("CALL M2_LISTAR_VALORACIONES()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Crear valoración
async function create(req, res) {
  try {
    const { puntuacion, comentario } = req.body;
    await db.query("CALL M2_CREAR_VALORACION(?,?)", [puntuacion, comentario]);
    res.json({ message: "Valoración registrada con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, create };
