// controllers/pedidosController.js
const db = require("../db");

async function list(req, res) {
  try {
    const [results] = await db.query("CALL M2_LISTAR_PEDIDOS()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function getById(req, res) {
  try {
    const [results] = await db.query("CALL M2_OBTENER_PEDIDO(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function create(req, res) {
  try {
    const { id_cliente, total, observaciones } = req.body;
    await db.query("CALL M2_CREAR_PEDIDO(?,?,?)", [id_cliente, total, observaciones]);
    res.json({ message: "Pedido creado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function update(req, res) {
  try {
    const { id_cliente, total, observaciones } = req.body;
    await db.query("CALL M2_ACTUALIZAR_PEDIDO(?,?,?,?)", [
      req.params.id,
      id_cliente,
      total,
      observaciones,
    ]);
    res.json({ message: "Pedido actualizado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function changeState(req, res) {
  try {
    const { estado } = req.body;
    await db.query("CALL M2_CAMBIAR_ESTADO_PEDIDO(?,?)", [req.params.id, estado]);
    res.json({ message: "Estado del pedido actualizado" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function remove(req, res) {
  try {
    await db.query("CALL M2_ELIMINAR_PEDIDO(?)", [req.params.id]);
    res.json({ message: "Pedido eliminado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, getById, create, update, changeState, remove };
