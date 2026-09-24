<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';
require_once __DIR__ . '/../includes/subir_archivo.php';

requerir_rol(['admin']);

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM especiales WHERE id = :id");
$stmt->execute(['id' => $id]);
$especial = $stmt->fetch();

if ($especial) {
    $pdo->prepare("DELETE FROM especiales WHERE id = :id")->execute(['id' => $id]);
    borrar_archivo($especial['imagen'], RUTA_IMG);
    set_mensaje('success', 'Especial eliminado.');
} else {
    set_mensaje('danger', 'Ese especial ya no existe.');
}

header('Location: index.php');
exit;
