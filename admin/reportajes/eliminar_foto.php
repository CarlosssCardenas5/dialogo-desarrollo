<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';
require_once __DIR__ . '/../includes/subir_archivo.php';

requerir_login();
$usuario = usuario_actual();

$id          = (int)($_GET['id'] ?? 0);
$reportaje_id = (int)($_GET['reportaje_id'] ?? 0);

// Si es redactor, solo puede tocar fotos de sus propios reportajes.
if (tiene_rol('redactor')) {
    $stmt = $pdo->prepare("SELECT usuario_id FROM reportajes WHERE id = :id");
    $stmt->execute(['id' => $reportaje_id]);
    $r = $stmt->fetch();
    if (!$r || (int)$r['usuario_id'] !== (int)$usuario['id']) {
        requerir_rol(['admin', 'editor']);
    }
}

$stmt = $pdo->prepare("SELECT * FROM reportajes_fotos WHERE id = :id");
$stmt->execute(['id' => $id]);
$foto = $stmt->fetch();

if ($foto) {
    $pdo->prepare("DELETE FROM reportajes_fotos WHERE id = :id")->execute(['id' => $id]);
    borrar_archivo($foto['url_foto'], RUTA_IMG);
}

header('Location: form.php?id=' . $reportaje_id);
exit;
