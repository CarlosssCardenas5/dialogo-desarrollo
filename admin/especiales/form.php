<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$especial = ['id' => 0, 'titulo' => '', 'imagen' => '', 'url_video' => '', 'orden' => 0];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM especiales WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $especial = $stmt->fetch() ?: $especial;
}

$admin_titulo = $id ? 'Editar especial' : 'Nuevo especial';
$admin_activo = 'especiales';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header"><?= htmlspecialchars($admin_titulo) ?></h1>
<?php mostrar_mensajes(); ?>

<form method="post" action="guardar.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$especial['id'] ?>">

    <div class="form-group">
        <label>Título</label>
        <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($especial['titulo']) ?>">
    </div>

    <div class="form-group">
        <label>URL del video (YouTube, Facebook, etc.)</label>
        <input type="url" name="url_video" class="form-control" required placeholder="https://www.youtube.com/watch?v=XXXXXXXX" value="<?= htmlspecialchars($especial['url_video']) ?>">
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Imagen <?= $especial['imagen'] ? '(sube otra para reemplazarla)' : '' ?></label>
                <?php if ($especial['imagen']): ?>
                    <div><img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($especial['imagen']) ?>" style="max-height:100px;" class="img-thumbnail"></div>
                <?php endif; ?>
                <input type="file" name="imagen" class="form-control" accept="image/*" <?= $id ? '' : 'required' ?>>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Orden (menor número aparece primero)</label>
                <input type="number" name="orden" class="form-control" value="<?= (int)$especial['orden'] ?>">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
    <a href="index.php" class="btn btn-default">Cancelar</a>
</form>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
