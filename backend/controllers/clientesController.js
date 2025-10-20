// controllers/clientesController.js
const db = require("../db");

async function list(req, res) {
  try {
    const [results] = await db.query("CALL M3_LISTAR_CLIENTES()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function getById(req, res) {
  try {
    const [results] = await db.query("CALL M3_OBTENER_CLIENTE(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function create(req, res) {
  try {
    const { nombre, correo, telefono, direccion } = req.body;
    await db.query("CALL M3_CREAR_CLIENTE(?,?,?,?)", [nombre, correo, telefono, direccion]);
    res.json({ message: "Cliente creado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function update(req, res) {
  try {
    const { nombre, correo, telefono, direccion } = req.body;
    await db.query("CALL M3_ACTUALIZAR_CLIENTE(?,?,?,?,?)", [
      req.params.id,
      nombre,
      correo,
      telefono,
      direccion,
    ]);
    res.json({ message: "Cliente actualizado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function remove(req, res) {
  try {
    await db.query("CALL M3_ELIMINAR_CLIENTE(?)", [req.params.id]);
    res.json({ message: "Cliente eliminado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, getById, create, update, remove };
