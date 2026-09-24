<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$id     = (int)($_GET['id'] ?? 0);
$estado = $_GET['estado'] ?? '';

if (!in_array($estado, ['borrador', 'publicado', 'oculto'], true)) {
    set_mensaje('danger', 'Estado inválido.');
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE reportajes SET estado = :estado WHERE id = :id");
$stmt->execute(['estado' => $estado, 'id' => $id]);

set_mensaje('success', 'Estado actualizado a "' . $estado . '".');
header('Location: index.php');
exit;
