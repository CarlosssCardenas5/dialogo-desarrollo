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
$resumen_corto     = trim($_POST['resumen_corto'] ?? '');
$desarrollo        = trim($_POST['desarrollo'] ?? '');
$fecha_publicacion = $_POST['fecha_publicacion'] ?? date('Y-m-d');
$autor_id          = (int)($_POST['autor_id'] ?? 0);

// Un redactor jamás puede auto-publicarse ni marcarse como destacado.
if (tiene_rol(['admin', 'editor'])) {
    $estado       = in_array($_POST['estado'] ?? '', ['borrador', 'publicado', 'oculto'], true) ? $_POST['estado'] : 'borrador';
    $es_destacado = !empty($_POST['es_destacado']) ? 1 : 0;
} else {
    $estado       = 'borrador';
    $es_destacado = 0;
}

if ($titulo === '' || $desarrollo === '' || $autor_id === 0) {
    set_mensaje('danger', 'Título, desarrollo y autor son obligatorios.');
    header('Location: form.php' . ($id ? "?id=$id" : ''));
    exit;
}

$fotoNueva = subir_archivo('foto_principal', RUTA_IMG, ['jpg', 'jpeg', 'png', 'webp']);
$pdfNuevo  = subir_archivo('pdf_adjunto', RUTA_IMG, ['pdf']);

if ($id) {
    // --- Actualizar ---
    $stmt = $pdo->prepare("SELECT * FROM reportajes WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $actual = $stmt->fetch();

    if (!$actual) {
        set_mensaje('danger', 'Ese reportaje ya no existe.');
        header('Location: index.php');
        exit;
    }
    if ($esRedactor && (int)$actual['usuario_id'] !== (int)$usuario['id']) {
        requerir_rol(['admin', 'editor']);
    }

    $foto = $fotoNueva ?: $actual['foto_principal'];
    $pdf  = $pdfNuevo ?: $actual['pdf_adjunto'];

    $sql = "UPDATE reportajes SET
                titulo = :titulo, resumen_corto = :resumen, desarrollo = :desarrollo,
                foto_principal = :foto, pdf_adjunto = :pdf, fecha_publicacion = :fecha,
                autor_id = :autor_id, estado = :estado, es_destacado = :destacado
            WHERE id = :id";
    $pdo->prepare($sql)->execute([
        'titulo' => $titulo, 'resumen' => $resumen_corto, 'desarrollo' => $desarrollo,
        'foto' => $foto, 'pdf' => $pdf, 'fecha' => $fecha_publicacion,
        'autor_id' => $autor_id, 'estado' => $estado, 'destacado' => $es_destacado,
        'id' => $id,
    ]);

    if ($fotoNueva) {
        borrar_archivo($actual['foto_principal'], RUTA_IMG);
    }
    if ($pdfNuevo) {
        borrar_archivo($actual['pdf_adjunto'], RUTA_IMG);
    }

    $mensaje = 'Reportaje actualizado correctamente.';
} else {
    // --- Crear ---
    $sql = "INSERT INTO reportajes
                (titulo, resumen_corto, desarrollo, foto_principal, pdf_adjunto,
                 fecha_publicacion, es_destacado, estado, autor_id, usuario_id)
            VALUES
                (:titulo, :resumen, :desarrollo, :foto, :pdf,
                 :fecha, :destacado, :estado, :autor_id, :usuario_id)";
    $pdo->prepare($sql)->execute([
        'titulo' => $titulo, 'resumen' => $resumen_corto, 'desarrollo' => $desarrollo,
        'foto' => $fotoNueva, 'pdf' => $pdfNuevo, 'fecha' => $fecha_publicacion,
        'destacado' => $es_destacado, 'estado' => $estado,
        'autor_id' => $autor_id, 'usuario_id' => $usuario['id'],
    ]);
    $id = (int)$pdo->lastInsertId();
    $mensaje = $esRedactor ? 'Reportaje guardado como borrador. Un editor lo revisará.' : 'Reportaje creado correctamente.';
}

// Fotos nuevas de galería (puede llegar más de una a la vez)
if (!empty($_FILES['galeria']['name'][0])) {
    foreach ($_FILES['galeria']['name'] as $i => $nombreOriginal) {
        if ($_FILES['galeria']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            continue;
        }
        $nombreGuardado = uniqid('ddp_galeria_', true) . '.' . $extension;
        if (!is_dir(RUTA_IMG)) {
            mkdir(RUTA_IMG, 0775, true);
        }
        move_uploaded_file($_FILES['galeria']['tmp_name'][$i], RUTA_IMG . '/' . $nombreGuardado);

        $pdo->prepare("INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden) VALUES (:rid, :url, :orden)")
            ->execute(['rid' => $id, 'url' => $nombreGuardado, 'orden' => $i]);
    }
}

set_mensaje('success', $mensaje);
header('Location: index.php');
exit;
