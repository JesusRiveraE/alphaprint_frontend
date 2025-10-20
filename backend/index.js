// index.js
const express = require("express");
const app = express();
const PORT = 3000;

app.use(express.json());

// Rutas
app.use("/api/usuarios", require("./routes/usuarios"));
app.use("/api/empleados", require("./routes/empleados"));
app.use("/api/clientes", require("./routes/clientes"));
app.use("/api/pedidos", require("./routes/pedidos"));
app.use("/api/valoraciones", require("./routes/valoraciones"));
app.use("/api/notificaciones", require("./routes/notificaciones"));
app.use("/api/archivos", require("./routes/archivos"));
app.use("/api/bitacora", require("./routes/bitacora"));
app.use("/api/historial", require("./routes/historial"));

app.get("/", (req, res) => res.send("API Alphaprint corriendo"));

app.listen(PORT, () => {
  console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
