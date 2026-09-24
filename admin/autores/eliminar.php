<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin']);

$id = (int)($_GET['id'] ?? 0);

try {
    $pdo->prepare("DELETE FROM autores WHERE id = :id")->execute(['id' => $id]);
    set_mensaje('success', 'Autor eliminado.');
} catch (PDOException $e) {
    set_mensaje('danger', 'No se puede eliminar: este autor tiene reportajes asociados.');
}

header('Location: index.php');
exit;
