const express = require('express');
const router = express.Router();
const ctrl = require('../controllers/notificacionesController');

router.get('/', ctrl.list);
router.put('/:id/leido', ctrl.markAsRead);

module.exports = router;