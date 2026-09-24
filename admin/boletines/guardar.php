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
$numero_boletin    = trim($_POST['numero_boletin'] ?? '');
$resumen           = trim($_POST['resumen'] ?? '');
$es_destacado      = isset($_POST['es_destacado']) ? 1 : 0;
$fecha_publicacion = $_POST['fecha_publicacion'] ?? date('Y-m-d');
$usuario           = usuario_actual();

/* Solo puede quedar un boletín destacado a la vez */
if ($es_destacado) {
    $pdo->exec("UPDATE boletines SET es_destacado = 0");
}

if ($numero_boletin === '') {
    set_mensaje('danger', 'El número de boletín es obligatorio.');
    header('Location: form.php' . ($id ? "?id=$id" : ''));
    exit;
}

$fotoNueva = subir_archivo('foto_portada', RUTA_IMG, ['jpg', 'jpeg', 'png', 'webp']);
$pdfNuevo  = subir_archivo('archivo_pdf', RUTA_BOLETINES, ['pdf']);

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM boletines WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $actual = $stmt->fetch();
    if (!$actual) {
        set_mensaje('danger', 'Ese boletín ya no existe.');
        header('Location: index.php');
        exit;
    }

    $foto = $fotoNueva ?: $actual['foto_portada'];
    $pdf  = $pdfNuevo ?: $actual['archivo_pdf'];

    $pdo->prepare("UPDATE boletines SET numero_boletin=:num, resumen=:resumen, foto_portada=:foto,
                    archivo_pdf=:pdf, es_destacado=:destacado, fecha_publicacion=:fecha WHERE id=:id")
        ->execute(['num' => $numero_boletin, 'resumen' => $resumen, 'foto' => $foto, 'pdf' => $pdf, 'destacado' => $es_destacado, 'fecha' => $fecha_publicacion, 'id' => $id]);

    if ($fotoNueva) borrar_archivo($actual['foto_portada'], RUTA_IMG);
    if ($pdfNuevo)  borrar_archivo($actual['archivo_pdf'], RUTA_BOLETINES);

    $mensaje = 'Boletín actualizado.';
} else {
    if (!$pdfNuevo) {
        set_mensaje('danger', 'Debes subir el PDF del boletín.');
        header('Location: form.php');
        exit;
    }
    $pdo->prepare("INSERT INTO boletines (numero_boletin, resumen, foto_portada, archivo_pdf, es_destacado, fecha_publicacion, usuario_id)
                    VALUES (:num, :resumen, :foto, :pdf, :destacado, :fecha, :usuario_id)")
        ->execute([
            'num' => $numero_boletin, 'resumen' => $resumen, 'foto' => $fotoNueva,
            'pdf' => $pdfNuevo, 'destacado' => $es_destacado, 'fecha' => $fecha_publicacion, 'usuario_id' => $usuario['id'],
        ]);
    $mensaje = 'Boletín creado.';
}

set_mensaje('success', $mensaje);
header('Location: index.php');
exit;
