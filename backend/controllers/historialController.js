const db = require("../db");

// Listar todo el historial
async function list(req, res) {
  try {
    const [results] = await db.query(
      "SELECT H.*, P.descripcion, P.estado FROM HISTORIAL_ESTADO_PEDIDO H JOIN PEDIDOS P ON H.id_pedido = P.id_pedido ORDER BY H.fecha DESC"
    );
    res.json(results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Listar historial de un pedido específico
async function listByPedido(req, res) {
  try {
    const [results] = await db.query(
      "SELECT * FROM HISTORIAL_ESTADO_PEDIDO WHERE id_pedido = ? ORDER BY fecha DESC",
      [req.params.id_pedido]
    );
    res.json(results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, listByPedido };
