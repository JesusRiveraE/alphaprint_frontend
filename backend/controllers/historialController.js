// controllers/historialController.js
const db = require("../db");

// Listar todo el historial
async function list(req, res) {
  try {
    const [results] = await db.query(
      "SELECT H.*, C.nombre AS cliente_nombre " +
      "FROM HISTORIAL_ESTADOS_PEDIDO H " +
      "INNER JOIN PEDIDOS P ON H.id_pedido = P.id_pedido " +
      "INNER JOIN CLIENTES C ON P.id_cliente = C.id_cliente " +
      "ORDER BY H.fecha_cambio DESC"
    );
    res.json(results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

// Listar historial de un pedido
async function getByPedido(req, res) {
  try {
    const [results] = await db.query(
      "SELECT H.*, C.nombre AS cliente_nombre " +
      "FROM HISTORIAL_ESTADOS_PEDIDO H " +
      "INNER JOIN PEDIDOS P ON H.id_pedido = P.id_pedido " +
      "INNER JOIN CLIENTES C ON P.id_cliente = C.id_cliente " +
      "WHERE H.id_pedido = ? ORDER BY H.fecha_cambio DESC",
      [req.params.idPedido]
    );
    res.json(results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, getByPedido };
