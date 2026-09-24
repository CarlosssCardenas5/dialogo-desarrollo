<?php
require_once __DIR__ . '/../includes/auth.php';
header('Location: ' . (usuario_autenticado() ? 'dashboard.php' : 'login.php'));
exit;
