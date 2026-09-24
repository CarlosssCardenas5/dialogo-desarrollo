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

$id        = (int)($_POST['id'] ?? 0);
$titulo    = trim($_POST['titulo'] ?? '');
$url_video = trim($_POST['url_video'] ?? '');
$orden     = (int)($_POST['orden'] ?? 0);
$usuario   = usuario_actual();

if ($titulo === '' || $url_video === '') {
    set_mensaje('danger', 'Título y URL del video son obligatorios.');
    header('Location: form.php' . ($id ? "?id=$id" : ''));
    exit;
}

$imagenNueva = subir_archivo('imagen', RUTA_IMG, ['jpg', 'jpeg', 'png', 'webp']);

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM especiales WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $actual = $stmt->fetch();
    if (!$actual) {
        set_mensaje('danger', 'Ese especial ya no existe.');
        header('Location: index.php');
        exit;
    }

    $imagen = $imagenNueva ?: $actual['imagen'];

    $pdo->prepare("UPDATE especiales SET titulo=:titulo, imagen=:imagen, url_video=:url, orden=:orden WHERE id=:id")
        ->execute(['titulo' => $titulo, 'imagen' => $imagen, 'url' => $url_video, 'orden' => $orden, 'id' => $id]);

    if ($imagenNueva) borrar_archivo($actual['imagen'], RUTA_IMG);

    $mensaje = 'Especial actualizado.';
} else {
    if (!$imagenNueva) {
        set_mensaje('danger', 'Debes subir una imagen para el especial.');
        header('Location: form.php');
        exit;
    }
    $pdo->prepare("INSERT INTO especiales (titulo, imagen, url_video, orden, usuario_id) VALUES (:titulo, :imagen, :url, :orden, :usuario_id)")
        ->execute(['titulo' => $titulo, 'imagen' => $imagenNueva, 'url' => $url_video, 'orden' => $orden, 'usuario_id' => $usuario['id']]);
    $mensaje = 'Especial creado.';
}

set_mensaje('success', $mensaje);
header('Location: index.php');
exit;
