// index.js
const express = require("express");
const cors = require("cors");
const app = express();
const PORT = 3000;

// ===============================
// 🔧 MIDDLEWARES
// ===============================
app.use(express.json());

// Configuración CORS: permitir solicitudes desde Laravel (localhost y 127.0.0.1)
app.use(
  cors({
    origin: ["http://localhost:8000", "http://127.0.0.1:8000"], // dominios de Laravel
    methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    allowedHeaders: ["Content-Type", "Authorization"],
    credentials: true,
  })
);

// ===============================
// 📦 RUTAS PRINCIPALES (API)
// ===============================
app.use("/api/usuarios", require("./routes/usuarios"));
app.use("/api/empleados", require("./routes/empleados"));
app.use("/api/clientes", require("./routes/clientes"));
app.use("/api/pedidos", require("./routes/pedidos"));
app.use("/api/valoraciones", require("./routes/valoraciones"));
app.use("/api/notificaciones", require("./routes/notificaciones"));
app.use("/api/archivos", require("./routes/archivos"));
app.use("/api/bitacora", require("./routes/bitacora"));
app.use("/api/historial", require("./routes/historial"));

// ===============================
// 🔐 AUTENTICACIÓN (Firebase → MySQL)
// ===============================
app.use("/auth", require("./routes/auth")); // Ruta pública para sincronizar usuarios

// ===============================
// ⏰ CRON JOB PARA SINCRONIZACIÓN DE USUARIOS
// ===============================
require('./cron/sync-job'); // Inicia la tarea de sincronización

// ===============================
// 🧭 RUTA BASE
// ===============================
app.get("/", (req, res) => {
  res.send("✅ API Alphaprint corriendo correctamente en localhost:3000");
});

// ===============================
// 🚀 INICIAR SERVIDOR
// ===============================
app.listen(PORT, "localhost", () => {
  console.log(`Servidor corriendo en http://localhost:${PORT}`);
});