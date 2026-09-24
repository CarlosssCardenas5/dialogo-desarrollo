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

$id          = (int)($_POST['id'] ?? 0);
$nombres     = trim($_POST['nombres'] ?? '');
$ap_paterno  = trim($_POST['ap_paterno'] ?? '');
$ap_materno  = trim($_POST['ap_materno'] ?? '');
$nickname    = trim($_POST['nickname'] ?? '');
$es_nickname = !empty($_POST['es_nickname']) ? 1 : 0;

if ($nombres === '') {
    set_mensaje('danger', 'El nombre es obligatorio.');
    header('Location: form.php' . ($id ? "?id=$id" : ''));
    exit;
}

if ($id) {
    $pdo->prepare("UPDATE autores SET nombres=:n, ap_paterno=:p, ap_materno=:m, nickname=:nick, es_nickname=:esn WHERE id=:id")
        ->execute(['n' => $nombres, 'p' => $ap_paterno, 'm' => $ap_materno, 'nick' => $nickname, 'esn' => $es_nickname, 'id' => $id]);
    $mensaje = 'Autor actualizado.';
} else {
    $pdo->prepare("INSERT INTO autores (nombres, ap_paterno, ap_materno, nickname, es_nickname) VALUES (:n, :p, :m, :nick, :esn)")
        ->execute(['n' => $nombres, 'p' => $ap_paterno, 'm' => $ap_materno, 'nick' => $nickname, 'esn' => $es_nickname]);
    $mensaje = 'Autor creado.';
}

set_mensaje('success', $mensaje);
header('Location: index.php');
exit;
