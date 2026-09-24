<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_login();
$usuario = usuario_actual();
$esRedactor = tiene_rol('redactor');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$noticia = [
    'id' => 0, 'titulo' => '', 'foto' => '', 'link_externo' => '',
    'fecha_publicacion' => date('Y-m-d'), 'estado' => 'borrador',
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $encontrada = $stmt->fetch();
    if (!$encontrada) {
        set_mensaje('danger', 'Esa noticia no existe.');
        header('Location: index.php');
        exit;
    }
    if ($esRedactor && (int)$encontrada['usuario_id'] !== (int)$usuario['id']) {
        requerir_rol(['admin', 'editor']);
    }
    $noticia = $encontrada;
}

$admin_titulo = $id ? 'Editar noticia' : 'Nueva noticia';
$admin_activo = 'noticias';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header"><?= htmlspecialchars($admin_titulo) ?></h1>
<?php mostrar_mensajes(); ?>

<form method="post" action="guardar.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$noticia['id'] ?>">

    <div class="form-group">
        <label>Título</label>
        <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($noticia['titulo']) ?>">
    </div>

    <div class="form-group">
        <label>Enlace externo (a la noticia real)</label>
        <input type="url" name="link_externo" class="form-control" required value="<?= htmlspecialchars($noticia['link_externo']) ?>">
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Fecha de publicación</label>
                <input type="date" name="fecha_publicacion" class="form-control" required value="<?= htmlspecialchars($noticia['fecha_publicacion']) ?>">
            </div>
        </div>
        <?php if (tiene_rol(['admin', 'editor'])): ?>
        <div class="col-md-4">
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <?php foreach (['borrador', 'publicado', 'oculto'] as $e): ?>
                        <option value="<?= $e ?>" <?= $noticia['estado'] === $e ? 'selected' : '' ?>><?= ucfirst($e) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php else: ?>
            <input type="hidden" name="estado" value="borrador">
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>Imagen <?= $noticia['foto'] ? '(sube otra para reemplazarla)' : '' ?></label>
        <?php if ($noticia['foto']): ?>
            <div><img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($noticia['foto']) ?>" style="max-height:100px;" class="img-thumbnail"></div>
        <?php endif; ?>
        <input type="file" name="foto" class="form-control" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
    <a href="index.php" class="btn btn-default">Cancelar</a>
</form>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
