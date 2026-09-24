<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$podcast = ['id' => 0, 'titulo' => '', 'archivo_audio' => '', 'fecha_publicacion' => date('Y-m-d')];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM podcasts WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $podcast = $stmt->fetch() ?: $podcast;
}

$admin_titulo = $id ? 'Editar episodio' : 'Nuevo episodio';
$admin_activo = 'podcasts';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header"><?= htmlspecialchars($admin_titulo) ?></h1>
<?php mostrar_mensajes(); ?>

<form method="post" action="guardar.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$podcast['id'] ?>">
    <div class="form-group">
        <label>Título</label>
        <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($podcast['titulo']) ?>">
    </div>
    <div class="form-group">
        <label>Archivo del podcast <?= $podcast['archivo_audio'] ? '(sube otro para reemplazarlo)' : '' ?></label>
        <?php if ($podcast['archivo_audio']): ?>
            <div class="audio-preview mb-2">
                <audio controls>
                    <source src="<?= BASE_URL ?>/podcasts_audio/<?= htmlspecialchars($podcast['archivo_audio']) ?>">
                </audio>
            </div>

            <a href="eliminar_audio.php?id=<?= (int)$podcast['id'] ?>"
            class="btn btn-xs btn-danger"
            onclick="return confirm('¿Seguro que deseas eliminar solamente el archivo de audio?');">
                <i class="fa fa-trash"></i> Eliminar audio
            </a>
        <?php endif; ?>
        <input type="file" name="archivo_audio" class="form-control" accept="audio/mpeg,audio/wav,audio/ogg,audio/mp4,video/mp4,.mp3,.wav,.ogg,.m4a,.mp4" <?= $id ? '' : 'required' ?>>
        <p class="help-block">Formatos permitidos: MP3, WAV, OGG, M4A o MP4.</p>
    </div>
    <div class="form-group">
        <label>Fecha de publicación</label>
        <input type="date" name="fecha_publicacion" class="form-control" required value="<?= htmlspecialchars($podcast['fecha_publicacion']) ?>">
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
    <a href="index.php" class="btn btn-default">Cancelar</a>
</form>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
