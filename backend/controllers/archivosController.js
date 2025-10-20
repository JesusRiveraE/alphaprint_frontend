const db = require("../db");

/**
 * 🔹 Listar todos los archivos
 */
async function listAll(req, res) {
  try {
    const [results] = await db.query("CALL M5_LISTAR_TODOS_ARCHIVOS()");
    // Limpiar el formato del resultado
    const data = Array.isArray(results) && Array.isArray(results[0]) ? results[0] : results;
    res.json(data);
  } catch (err) {
    console.error("❌ Error en listAll:", err.message);
    res.status(500).json({ error: err.message });
  }
}

/**
 * 🔹 Listar archivos por pedido
 */
async function listByPedido(req, res) {
  try {
    const [results] = await db.query("CALL M5_LISTAR_ARCHIVOS_PEDIDO(?)", [req.params.id_pedido]);
    const data = Array.isArray(results) && Array.isArray(results[0]) ? results[0] : results;
    res.json(data);
  } catch (err) {
    console.error("❌ Error en listByPedido:", err.message);
    res.status(500).json({ error: err.message });
  }
}

/**
 * 🔹 Obtener un archivo por ID
 */
async function getById(req, res) {
  try {
    const [results] = await db.query("CALL M5_OBTENER_ARCHIVO(?)", [req.params.id]);
    const data = results[0] && results[0][0] ? results[0][0] : null;
    res.json(data);
  } catch (err) {
    console.error("❌ Error en getById:", err.message);
    res.status(500).json({ error: err.message });
  }
}

/**
 * 🔹 Crear un nuevo archivo
 */
async function create(req, res) {
  try {
    const { id_pedido, url, comentario } = req.body;
    await db.query("CALL M5_CREAR_ARCHIVO(?,?,?)", [id_pedido, url, comentario]);
    res.json({ message: "✅ Archivo registrado con éxito" });
  } catch (err) {
    console.error("❌ Error en create:", err.message);
    res.status(500).json({ error: err.message });
  }
}

/**
 * 🔹 Eliminar archivo
 */
async function remove(req, res) {
  try {
    await db.query("CALL M5_ELIMINAR_ARCHIVO(?)", [req.params.id]);
    res.json({ message: "🗑️ Archivo eliminado correctamente" });
  } catch (err) {
    console.error("❌ Error en remove:", err.message);
    res.status(500).json({ error: err.message });
  }
}

/**
 * ✅ Exportar controladores
 */
module.exports = { listAll, listByPedido, getById, create, remove };
