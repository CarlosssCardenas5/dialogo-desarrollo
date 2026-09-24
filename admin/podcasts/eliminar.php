<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';
require_once __DIR__ . '/../includes/subir_archivo.php';

requerir_rol(['admin']);

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT archivo_audio FROM podcasts WHERE id = :id");
$stmt->execute(['id' => $id]);
$actual = $stmt->fetch();

$pdo->prepare("DELETE FROM podcasts WHERE id = :id")->execute(['id' => $id]);

if ($actual) {
    borrar_archivo($actual['archivo_audio'], RUTA_PODCASTS_AUDIO);
}

set_mensaje('success', 'Episodio eliminado.');
header('Location: index.php');
exit;
