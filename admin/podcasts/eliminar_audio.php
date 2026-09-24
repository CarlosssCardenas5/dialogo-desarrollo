<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';
require_once __DIR__ . '/../includes/subir_archivo.php';

requerir_rol(['admin', 'editor']);

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    set_mensaje('danger', 'Podcast no válido.');
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT archivo_audio
    FROM podcasts
    WHERE id = :id
");
$stmt->execute(['id' => $id]);

$podcast = $stmt->fetch();

if (!$podcast) {
    set_mensaje('danger', 'El podcast no existe.');
    header('Location: index.php');
    exit;
}

if (!empty($podcast['archivo_audio'])) {
    borrar_archivo(
        $podcast['archivo_audio'],
        RUTA_PODCASTS_AUDIO
    );
}

$stmt = $pdo->prepare("
    UPDATE podcasts
    SET archivo_audio = NULL
    WHERE id = :id
");

$stmt->execute(['id' => $id]);

set_mensaje('success', 'El archivo de audio fue eliminado correctamente.');

header('Location: form.php?id=' . $id);
exit;