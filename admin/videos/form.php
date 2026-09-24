<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$podcast = ['id' => 0, 'titulo' => '', 'url_embed' => '', 'fecha_publicacion' => date('Y-m-d')];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $podcast = $stmt->fetch() ?: $podcast;
}

$admin_titulo = $id ? 'Editar video' : 'Nuevo video';
$admin_activo = 'videos';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header"><?= htmlspecialchars($admin_titulo) ?></h1>
<?php mostrar_mensajes(); ?>

<form method="post" action="guardar.php">
    <input type="hidden" name="id" value="<?= (int)$podcast['id'] ?>">
    <div class="form-group">
        <label>Título</label>
        <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($podcast['titulo']) ?>">
    </div>
    <div class="form-group">
        <label>URL de embed (YouTube, Spotify, etc.)</label>
        <input type="url" name="url_embed" class="form-control" required placeholder="https://www.youtube.com/embed/XXXXXXXX" value="<?= htmlspecialchars($podcast['url_embed']) ?>">
    </div>
    <div class="form-group">
        <label>Fecha de publicación</label>
        <input type="date" name="fecha_publicacion" class="form-control" required value="<?= htmlspecialchars($podcast['fecha_publicacion']) ?>">
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
    <a href="index.php" class="btn btn-default">Cancelar</a>
</form>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
