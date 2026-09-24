<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';
require_once __DIR__ . '/../includes/subir_archivo.php';

requerir_login();
$usuario = usuario_actual();
$esRedactor = tiene_rol('redactor');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id                = (int)($_POST['id'] ?? 0);
$titulo            = trim($_POST['titulo'] ?? '');
$link_externo      = trim($_POST['link_externo'] ?? '');
$fecha_publicacion = $_POST['fecha_publicacion'] ?? date('Y-m-d');
$estado            = tiene_rol(['admin', 'editor'])
    ? (in_array($_POST['estado'] ?? '', ['borrador', 'publicado', 'oculto'], true) ? $_POST['estado'] : 'borrador')
    : 'borrador';

if ($titulo === '' || $link_externo === '') {
    set_mensaje('danger', 'Título y enlace son obligatorios.');
    header('Location: form.php' . ($id ? "?id=$id" : ''));
    exit;
}

$fotoNueva = subir_archivo('foto', RUTA_IMG, ['jpg', 'jpeg', 'png', 'webp']);

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $actual = $stmt->fetch();
    if (!$actual) {
        set_mensaje('danger', 'Esa noticia ya no existe.');
        header('Location: index.php');
        exit;
    }
    if ($esRedactor && (int)$actual['usuario_id'] !== (int)$usuario['id']) {
        requerir_rol(['admin', 'editor']);
    }

    $foto = $fotoNueva ?: $actual['foto'];

    $pdo->prepare("UPDATE noticias SET titulo=:titulo, link_externo=:link, foto=:foto,
                    fecha_publicacion=:fecha, estado=:estado WHERE id=:id")
        ->execute([
            'titulo' => $titulo, 'link' => $link_externo, 'foto' => $foto,
            'fecha' => $fecha_publicacion, 'estado' => $estado, 'id' => $id,
        ]);

    if ($fotoNueva) {
        borrar_archivo($actual['foto'], RUTA_IMG);
    }
    $mensaje = 'Noticia actualizada correctamente.';
} else {
    $pdo->prepare("INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id, estado)
                    VALUES (:titulo, :foto, :link, :fecha, :usuario_id, :estado)")
        ->execute([
            'titulo' => $titulo, 'foto' => $fotoNueva, 'link' => $link_externo,
            'fecha' => $fecha_publicacion, 'usuario_id' => $usuario['id'], 'estado' => $estado,
        ]);
    $mensaje = $esRedactor ? 'Noticia guardada como borrador.' : 'Noticia creada correctamente.';
}

set_mensaje('success', $mensaje);
header('Location: index.php');
exit;
