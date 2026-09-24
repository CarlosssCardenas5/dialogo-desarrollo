<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id                = (int)($_POST['id'] ?? 0);
$titulo            = trim($_POST['titulo'] ?? '');
$url_embed         = trim($_POST['url_embed'] ?? '');
$fecha_publicacion = $_POST['fecha_publicacion'] ?? date('Y-m-d');
$usuario           = usuario_actual();

if ($titulo === '' || $url_embed === '') {
    set_mensaje('danger', 'Título y URL son obligatorios.');
    header('Location: form.php' . ($id ? "?id=$id" : ''));
    exit;
}

if ($id) {
    $pdo->prepare("UPDATE videos SET titulo=:titulo, url_embed=:url, fecha_publicacion=:fecha WHERE id=:id")
        ->execute(['titulo' => $titulo, 'url' => $url_embed, 'fecha' => $fecha_publicacion, 'id' => $id]);
    $mensaje = 'Video actualizado.';
} else {
    $pdo->prepare("INSERT INTO videos (titulo, url_embed, fecha_publicacion, usuario_id) VALUES (:titulo, :url, :fecha, :usuario_id)")
        ->execute(['titulo' => $titulo, 'url' => $url_embed, 'fecha' => $fecha_publicacion, 'usuario_id' => $usuario['id']]);
    $mensaje = 'Video creado.';
}

set_mensaje('success', $mensaje);
header('Location: index.php');
exit;
