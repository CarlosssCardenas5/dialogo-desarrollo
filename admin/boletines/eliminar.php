<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';
require_once __DIR__ . '/../includes/subir_archivo.php';

requerir_rol(['admin']);

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM boletines WHERE id = :id");
$stmt->execute(['id' => $id]);
$boletin = $stmt->fetch();

if ($boletin) {
    $pdo->prepare("DELETE FROM boletines WHERE id = :id")->execute(['id' => $id]);
    borrar_archivo($boletin['foto_portada'], RUTA_IMG);
    borrar_archivo($boletin['archivo_pdf'], RUTA_BOLETINES);
    set_mensaje('success', 'Boletín eliminado.');
} else {
    set_mensaje('danger', 'Ese boletín ya no existe.');
}

header('Location: index.php');
exit;
