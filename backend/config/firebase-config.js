// backend/config/firebase-config.js
const admin = require('firebase-admin');
const serviceAccount = require('../firebase-service-account.json');

try {
  if (!admin.apps.length) {
    admin.initializeApp({
      credential: admin.credential.cert(serviceAccount)
    });
    console.log('✅ Firebase Admin SDK inicializado.');
  }
} catch (error) {
  console.error("❌ Error inicializando Firebase Admin SDK:", error);
}

module.exports = admin;