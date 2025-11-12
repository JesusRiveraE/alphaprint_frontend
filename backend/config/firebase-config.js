// backend/config/firebase-config.js
const admin = require('firebase-admin');

let serviceAccount;

// Permite usar credenciales por ENV o por archivo local
try {
  if (process.env.FIREBASE_SERVICE_ACCOUNT) {
    serviceAccount = JSON.parse(process.env.FIREBASE_SERVICE_ACCOUNT);

    // Fix común: private_key con \n escapados en variables de entorno
    if (serviceAccount.private_key && serviceAccount.private_key.includes('\\n')) {
      serviceAccount.private_key = serviceAccount.private_key.replace(/\\n/g, '\n');
    }
  } else {
    // Fallback al archivo del repo
    serviceAccount = require('../firebase-service-account.json');
  }
} catch (e) {
  console.error('❌ No se pudo cargar el service account de Firebase:', e);
  throw e;
}

try {
  if (!admin.apps.length) {
    admin.initializeApp({
      credential: admin.credential.cert(serviceAccount),
      projectId: serviceAccount.project_id,
    });
    console.log('✅ Firebase Admin SDK inicializado.');
  }
} catch (error) {
  console.error('❌ Error inicializando Firebase Admin SDK:', error);
  throw error;
}

// ⛔️ SE ELIMINÓ LA SECCIÓN DE HELPERS INNECESARIOS ⛔️
// (admin.verifyIdTokenChecked, admin.revokeUserSessions, admin.setDisabled)
// Ya no son necesarios porque usamos las funciones reales en los controladores.

module.exports = admin;