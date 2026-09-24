<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$boletin = ['id' => 0, 'numero_boletin' => '', 'resumen' => '', 'foto_portada' => '', 'archivo_pdf' => '', 'es_destacado' => 0, 'fecha_publicacion' => date('Y-m-d')];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM boletines WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $boletin = $stmt->fetch() ?: $boletin;
}

$admin_titulo = $id ? 'Editar boletín' : 'Nuevo boletín';
$admin_activo = 'boletines';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header"><?= htmlspecialchars($admin_titulo) ?></h1>
<?php mostrar_mensajes(); ?>

<form method="post" action="guardar.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$boletin['id'] ?>">

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label>Número</label>
                <input type="text" name="numero_boletin" class="form-control" required value="<?= htmlspecialchars($boletin['numero_boletin']) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Fecha de publicación</label>
                <input type="date" name="fecha_publicacion" class="form-control" required value="<?= htmlspecialchars($boletin['fecha_publicacion']) ?>">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Resumen (una línea por punto destacado)</label>
        <textarea name="resumen" class="form-control" rows="4"><?= htmlspecialchars($boletin['resumen']) ?></textarea>
    </div>

    <div class="form-group">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="es_destacado" value="1" <?= $boletin['es_destacado'] ? 'checked' : '' ?>>
                Mostrar este boletín como destacado en la página de inicio
            </label>
        </div>
        <p class="help-block">Solo puede haber un boletín destacado a la vez; si marcas este, se desmarca cualquier otro automáticamente.</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Imagen de portada <?= $boletin['foto_portada'] ? '(sube otra para reemplazarla)' : '' ?></label>
                <?php if ($boletin['foto_portada']): ?>
                    <div><img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($boletin['foto_portada']) ?>" style="max-height:100px;" class="img-thumbnail"></div>
                <?php endif; ?>
                <input type="file" name="foto_portada" class="form-control" accept="image/*">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Archivo PDF del boletín <?= $boletin['archivo_pdf'] ? '(sube otro para reemplazarlo)' : '' ?></label>
                <?php if ($boletin['archivo_pdf']): ?>
                    <div><a href="<?= BASE_URL ?>/boletines/<?= htmlspecialchars($boletin['archivo_pdf']) ?>" target="_blank">Ver PDF actual</a></div>
                <?php endif; ?>
                <input type="file" name="archivo_pdf" class="form-control" accept="application/pdf" <?= $id ? '' : 'required' ?>>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
    <a href="index.php" class="btn btn-default">Cancelar</a>
</form>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
