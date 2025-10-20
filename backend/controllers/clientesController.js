const db = require("../db");

// Listar todos los clientes
async function list(req, res) {
  try {
    const [results] = await db.query("CALL M3_LISTAR_CLIENTES()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Obtener cliente por ID
async function getById(req, res) {
  try {
    const [results] = await db.query("CALL M3_OBTENER_CLIENTE(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Crear cliente
async function create(req, res) {
  try {
    const { nombre, telefono, correo } = req.body;
    await db.query("CALL M3_CREAR_CLIENTE(?,?,?)", [nombre, telefono, correo]);
    res.json({ message: "Cliente creado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Actualizar cliente
async function update(req, res) {
  try {
    const { nombre, telefono, correo } = req.body;
    await db.query("CALL M3_ACTUALIZAR_CLIENTE(?,?,?,?)", [
      req.params.id,
      nombre,
      telefono,
      correo,
    ]);
    res.json({ message: "Cliente actualizado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Eliminar cliente (elimina sus pedidos asociados también)
async function remove(req, res) {
  try {
    await db.query("CALL M3_ELIMINAR_CLIENTE(?)", [req.params.id]);
    res.json({ message: "Cliente eliminado con éxito (y sus pedidos relacionados)" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, getById, create, update, remove };
