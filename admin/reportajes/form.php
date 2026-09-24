<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_login();
$usuario = usuario_actual();
$esRedactor = tiene_rol('redactor');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$reportaje = [
    'id' => 0, 'titulo' => '', 'resumen_corto' => '', 'desarrollo' => '',
    'foto_principal' => '', 'pdf_adjunto' => '', 'fecha_publicacion' => date('Y-m-d'),
    'es_destacado' => 0, 'estado' => 'borrador', 'autor_id' => '',
];
$fotos = [];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM reportajes WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $encontrado = $stmt->fetch();
    if (!$encontrado) {
        set_mensaje('danger', 'Ese reportaje no existe.');
        header('Location: index.php');
        exit;
    }
    // Un redactor solo puede tocar sus propios reportajes.
    if ($esRedactor && (int)$encontrado['usuario_id'] !== (int)$usuario['id']) {
        requerir_rol(['admin', 'editor']); // corta con 403
    }
    $reportaje = $encontrado;

    $stmtFotos = $pdo->prepare("SELECT * FROM reportajes_fotos WHERE reportaje_id = :id ORDER BY orden ASC");
    $stmtFotos->execute(['id' => $id]);
    $fotos = $stmtFotos->fetchAll();
}

$autores = $pdo->query("SELECT * FROM autores ORDER BY nombres ASC")->fetchAll();

$admin_titulo = $id ? 'Editar reportaje' : 'Nuevo reportaje';
$admin_activo = 'reportajes';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header"><?= htmlspecialchars($admin_titulo) ?></h1>
<?php mostrar_mensajes(); ?>

<form method="post" action="guardar.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$reportaje['id'] ?>">

    <div class="form-group">
        <label>Título</label>
        <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($reportaje['titulo']) ?>">
    </div>

    <div class="form-group">
        <label>Resumen corto</label>
        <textarea name="resumen_corto" class="form-control" rows="2"><?= htmlspecialchars($reportaje['resumen_corto']) ?></textarea>
    </div>

    <div class="form-group">
        <label>Desarrollo completo</label>
        <textarea name="desarrollo" class="form-control" rows="8" required><?= htmlspecialchars($reportaje['desarrollo']) ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Fecha de publicación</label>
                <input type="date" name="fecha_publicacion" class="form-control" required value="<?= htmlspecialchars($reportaje['fecha_publicacion']) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Autor</label>
                <select name="autor_id" class="form-control" required>
                    <option value="">-- Selecciona --</option>
                    <?php foreach ($autores as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= (string)$a['id'] === (string)$reportaje['autor_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['es_nickname'] && $a['nickname'] ? $a['nickname'] : trim($a['nombres'] . ' ' . $a['ap_paterno'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php if (tiene_rol(['admin', 'editor'])): ?>
        <div class="col-md-2">
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <?php foreach (['borrador', 'publicado', 'oculto'] as $e): ?>
                        <option value="<?= $e ?>" <?= $reportaje['estado'] === $e ? 'selected' : '' ?>><?= ucfirst($e) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="checkbox">
                    <label><input type="checkbox" name="es_destacado" value="1" <?= $reportaje['es_destacado'] ? 'checked' : '' ?>> Destacado (portada)</label>
                </div>
            </div>
        </div>
        <?php else: ?>
            <input type="hidden" name="estado" value="borrador">
            <div class="col-md-4">
                <p class="text-muted" style="margin-top:25px;">Se guardará como <b>borrador</b>. Un editor lo revisará y publicará.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Foto principal <?= $reportaje['foto_principal'] ? '(ya tiene una, sube otra para reemplazarla)' : '' ?></label>
                <?php if ($reportaje['foto_principal']): ?>
                    <div><img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($reportaje['foto_principal']) ?>" style="max-height:100px;" class="img-thumbnail"></div>
                <?php endif; ?>
                <input type="file" name="foto_principal" class="form-control" accept="image/*">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>PDF adjunto (opcional)</label>
                <?php if ($reportaje['pdf_adjunto']): ?>
                    <div><a href="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($reportaje['pdf_adjunto']) ?>" target="_blank">Ver PDF actual</a></div>
                <?php endif; ?>
                <input type="file" name="pdf_adjunto" class="form-control" accept="application/pdf">
            </div>
        </div>
    </div>

    <?php if ($id): ?>
    <div class="form-group">
        <label>Fotos de galería</label>
        <?php if ($fotos): ?>
            <div class="row">
                <?php foreach ($fotos as $f): ?>
                    <div class="col-md-2 text-center" style="margin-bottom:10px;">
                        <img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($f['url_foto']) ?>" class="img-thumbnail" style="height:80px;">
                        <br><a href="eliminar_foto.php?id=<?= (int)$f['id'] ?>&reportaje_id=<?= $id ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Quitar esta foto?');">Quitar</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <label>Agregar nuevas fotos a la galería</label>
        <input type="file" name="galeria[]" class="form-control" accept="image/*" multiple>
    </div>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
    <a href="index.php" class="btn btn-default">Cancelar</a>
</form>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
