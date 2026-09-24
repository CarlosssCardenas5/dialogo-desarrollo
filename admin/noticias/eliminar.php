<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';
require_once __DIR__ . '/../includes/subir_archivo.php';

requerir_rol(['admin']);

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = :id");
$stmt->execute(['id' => $id]);
$noticia = $stmt->fetch();

if ($noticia) {
    $pdo->prepare("DELETE FROM noticias WHERE id = :id")->execute(['id' => $id]);
    borrar_archivo($noticia['foto'], RUTA_IMG);
    set_mensaje('success', 'Noticia eliminada.');
} else {
    set_mensaje('danger', 'Esa noticia ya no existe.');
}

header('Location: index.php');
exit;
