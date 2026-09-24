<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';
require_once __DIR__ . '/../includes/subir_archivo.php';

requerir_rol(['admin']); // borrado definitivo: solo admin

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM reportajes WHERE id = :id");
$stmt->execute(['id' => $id]);
$reportaje = $stmt->fetch();

if ($reportaje) {
    // Las fotos de galería se borran solas por el ON DELETE CASCADE de la BD.
    $fotos = $pdo->prepare("SELECT url_foto FROM reportajes_fotos WHERE reportaje_id = :id");
    $fotos->execute(['id' => $id]);
    foreach ($fotos->fetchAll() as $f) {
        borrar_archivo($f['url_foto'], RUTA_IMG);
    }

    $pdo->prepare("DELETE FROM reportajes WHERE id = :id")->execute(['id' => $id]);

    borrar_archivo($reportaje['foto_principal'], RUTA_IMG);
    borrar_archivo($reportaje['pdf_adjunto'], RUTA_IMG);

    set_mensaje('success', 'Reportaje eliminado.');
} else {
    set_mensaje('danger', 'Ese reportaje ya no existe.');
}

header('Location: index.php');
exit;
