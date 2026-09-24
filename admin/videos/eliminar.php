<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin']);

$id = (int)($_GET['id'] ?? 0);
$pdo->prepare("DELETE FROM videos WHERE id = :id")->execute(['id' => $id]);
set_mensaje('success', 'Episodio eliminado.');
header('Location: index.php');
exit;
