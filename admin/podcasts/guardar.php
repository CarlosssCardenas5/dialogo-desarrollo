<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';
require_once __DIR__ . '/../includes/subir_archivo.php';

requerir_rol(['admin', 'editor']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id                = (int)($_POST['id'] ?? 0);
$titulo            = trim($_POST['titulo'] ?? '');
$fecha_publicacion = $_POST['fecha_publicacion'] ?? date('Y-m-d');
$usuario           = usuario_actual();

if ($titulo === '') {
    set_mensaje('danger', 'El título es obligatorio.');
    header('Location: form.php' . ($id ? "?id=$id" : ''));
    exit;
}

// Si el usuario seleccionó un archivo, un fallo de subida NO debe tratarse
// como si simplemente no hubiera archivo. Mostramos el error y no tocamos DB.
$archivoSeleccionado = isset($_FILES['archivo_audio']) && ($_FILES['archivo_audio']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
$errorSubida = null;
$audioNuevo = subir_archivo(
    'archivo_audio',
    RUTA_PODCASTS_AUDIO,
    ['mp3', 'wav', 'ogg', 'm4a', 'mp4'],
    $errorSubida
);

if ($archivoSeleccionado && !$audioNuevo) {
    set_mensaje('danger', $errorSubida ?: 'No se pudo subir el archivo de podcast.');
    header('Location: form.php' . ($id ? "?id=$id" : ''));
    exit;
}

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM podcasts WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $actual = $stmt->fetch();

    if (!$actual) {
        set_mensaje('danger', 'Ese episodio ya no existe.');
        header('Location: index.php');
        exit;
    }

    $audio = $audioNuevo ?: $actual['archivo_audio'];

    $pdo->prepare("UPDATE podcasts SET titulo=:titulo, archivo_audio=:audio, fecha_publicacion=:fecha WHERE id=:id")
        ->execute([
            'titulo' => $titulo,
            'audio' => $audio,
            'fecha' => $fecha_publicacion,
            'id' => $id,
        ]);

    if ($audioNuevo) {
        borrar_archivo($actual['archivo_audio'], RUTA_PODCASTS_AUDIO);
    }

    $mensaje = 'Episodio actualizado correctamente.';
} else {
    if (!$audioNuevo) {
        set_mensaje('danger', 'Debes subir un archivo de podcast (MP3, WAV, OGG, M4A o MP4).');
        header('Location: form.php');
        exit;
    }

    $pdo->prepare("INSERT INTO podcasts (titulo, archivo_audio, fecha_publicacion, usuario_id) VALUES (:titulo, :audio, :fecha, :usuario_id)")
        ->execute([
            'titulo' => $titulo,
            'audio' => $audioNuevo,
            'fecha' => $fecha_publicacion,
            'usuario_id' => $usuario['id'],
        ]);

    $mensaje = 'Episodio creado correctamente.';
}

set_mensaje('success', $mensaje);
header('Location: index.php');
exit;
