// controllers/usuariosController.js
const db = require("../db");

async function list(req, res) {
  try {
    const [results] = await db.query("CALL M1_LISTAR_USUARIOS()");
    res.json(results[0] || results);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function getById(req, res) {
  try {
    const [results] = await db.query("CALL M1_OBTENER_USUARIO(?)", [req.params.id]);
    res.json(results[0] ? results[0][0] : null);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function create(req, res) {
  try {
    const { uid_firebase, nombre_usuario, correo, rol } = req.body;
    await db.query("CALL M1_CREAR_USUARIO(?,?,?,?)", [uid_firebase, nombre_usuario, correo, rol]);
    res.json({ message: "Usuario creado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function update(req, res) {
  try {
    const { nombre_usuario, correo, rol, activo } = req.body;
    await db.query("CALL M1_ACTUALIZAR_USUARIO(?,?,?,?,?)", [
      req.params.id,
      nombre_usuario,
      correo,
      rol,
      activo,
    ]);
    res.json({ message: "Usuario actualizado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function remove(req, res) {
  try {
    await db.query("CALL M1_ELIMINAR_USUARIO(?)", [req.params.id]);
    res.json({ message: "Usuario eliminado con éxito" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

async function changeState(req, res) {
  try {
    const { activo } = req.body;
    await db.query("CALL M1_CAMBIAR_ESTADO_USUARIO(?,?)", [req.params.id, activo]);
    res.json({ message: "Estado del usuario actualizado" });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = { list, getById, create, update, remove, changeState };
