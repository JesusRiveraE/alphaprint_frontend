// backend/cron/sync-job.js
const admin = require('../config/firebase-config');
const pool = require('../db');
const cron = require('node-cron');

const syncUsers = async () => {
  let connection;
  try {
    console.log('Iniciando sincronización de usuarios...');

    // 1. Obtener todos los usuarios de Firebase
    const firebaseUsers = await admin.auth().listUsers();
    const firebaseUids = firebaseUsers.users.map(user => user.uid);
    const firebaseUserMap = new Map(firebaseUsers.users.map(user => [user.uid, user]));

    // 2. Obtener todos los usuarios de MySQL
    connection = await pool.getConnection();
    const [mysqlUsers] = await connection.execute('SELECT id_usuario, uid_firebase, correo, activo FROM USUARIOS WHERE uid_firebase IS NOT NULL');
    const mysqlUserMap = new Map(mysqlUsers.map(user => [user.uid_firebase, user]));

    // 3. Identificar y sincronizar usuarios
    for (const [uid, firebaseUser] of firebaseUserMap) {
      const mysqlUser = mysqlUserMap.get(uid);

      if (!mysqlUser) {
        // Usuario en Firebase, no en MySQL (CREAR)
        console.log(`Creando usuario en MySQL para UID: ${uid}`);
        await connection.execute('CALL M1_CREAR_USUARIO(?, ?, ?, ?)', [
          uid, firebaseUser.displayName || firebaseUser.email.split('@')[0], firebaseUser.email, 'Empleado' // Rol por defecto
        ]);
      } else {
        // Usuario en ambos lados, verificar actualizaciones
        if (mysqlUser.correo !== firebaseUser.email) {
          console.log(`Actualizando correo para usuario con UID: ${uid}`);
          await connection.execute('CALL M1_ACTUALIZAR_USUARIO(?, ?, ?, ?, ?)', [
            mysqlUser.id_usuario, uid, null, firebaseUser.email, null, null
          ]);
        }
      }
    }

    // 4. Identificar usuarios en MySQL que no están en Firebase (ELIMINAR)
    const usersToDelete = mysqlUsers.filter(mysqlUser => !firebaseUids.includes(mysqlUser.uid_firebase));
    for (const user of usersToDelete) {
      console.log(`Eliminando usuario con ID MySQL ${user.id_usuario} (UID Firebase: ${user.uid_firebase})`);
      await connection.execute('CALL M1_ELIMINAR_USUARIO(?)', [user.id_usuario]);
    }

    console.log(`Sincronización completada. Se eliminaron ${usersToDelete.length} usuarios.`);
    connection.release();

  } catch (error) {
    console.error('Error durante la sincronización:', error);
    if (connection) connection.release();
  }
};

// Programa la tarea para que se ejecute cada 12 horas (puedes ajustar el cron)
cron.schedule('0 */12 * * *', () => {
  syncUsers();
});

module.exports = { syncUsers };