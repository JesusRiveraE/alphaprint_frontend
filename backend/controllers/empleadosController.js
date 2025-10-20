// controllers/empleadosController.js
const db = require("../db");

async function list(req, res) {
  try {
    const [results] = await db.query("CALL M4_LISTAR_EMPLEADOS()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function getById(req, res) {
  try {
    const [results] = await db.query("CALL M4_OBTENER_EMPLEADO(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function create(req, res) {
  try {
    const { id_usuario, nombre, area, correo, telefono, fecha_ingreso } = req.body;
    await db.query("CALL M4_CREAR_EMPLEADO(?,?,?,?,?,?)", [
      id_usuario,
      nombre,
      area,
      correo,
      telefono,
      fecha_ingreso,
    ]);
    res.json({ message: "Empleado creado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function update(req, res) {
  try {
    const { id_usuario, nombre, area, correo, telefono, fecha_ingreso } = req.body;
    await db.query("CALL M4_ACTUALIZAR_EMPLEADO(?,?,?,?,?,?,?)", [
      req.params.id,
      id_usuario,
      nombre,
      area,
      correo,
      telefono,
      fecha_ingreso,
    ]);
    res.json({ message: "Empleado actualizado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function remove(req, res) {
  try {
    await db.query("CALL M4_ELIMINAR_EMPLEADO(?)", [req.params.id]);
    res.json({ message: "Empleado eliminado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, getById, create, update, remove };
