const db = require("../db");

// Listar pedidos
async function list(req, res) {
  try {
    const [results] = await db.query("CALL M2_LISTAR_PEDIDOS()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Obtener pedido por ID
async function getById(req, res) {
  try {
    const [results] = await db.query("CALL M2_OBTENER_PEDIDO(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Crear pedido
async function create(req, res) {
  try {
    const { id_cliente, descripcion, total } = req.body;
    await db.query("CALL M2_CREAR_PEDIDO(?,?,?)", [id_cliente, descripcion, total]);
    res.json({ message: "Pedido creado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Actualizar pedido
async function update(req, res) {
  try {
    const { id_cliente, descripcion, total } = req.body;
    await db.query("CALL M2_ACTUALIZAR_PEDIDO(?,?,?,?)", [
      req.params.id,
      id_cliente,
      descripcion,
      total,
    ]);
    res.json({ message: "Pedido actualizado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Cambiar estado del pedido
async function changeStatus(req, res) {
  try {
    const { estado } = req.body;
    await db.query("CALL M2_CAMBIAR_ESTADO_PEDIDO(?,?)", [req.params.id, estado]);
    res.json({ message: `Estado del pedido #${req.params.id} actualizado a ${estado}` });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Eliminar pedido
async function remove(req, res) {
  try {
    await db.query("CALL M2_ELIMINAR_PEDIDO(?)", [req.params.id]);
    res.json({ message: "Pedido eliminado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, getById, create, update, changeStatus, remove };
