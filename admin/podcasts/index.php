<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$podcasts = $pdo->query("SELECT * FROM podcasts ORDER BY fecha_publicacion DESC")->fetchAll();

$admin_titulo = 'Podcasts';
$admin_activo = 'podcasts';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header">
    Podcasts
    <a href="form.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nuevo episodio</a>
</h1>
<?php mostrar_mensajes(); ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead><tr><th>Título</th><th>Fecha</th><th>Archivo</th><th style="width:160px;">Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($podcasts as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['titulo']) ?></td>
                <td><?= date('d/m/Y', strtotime($p['fecha_publicacion'])) ?></td>
                <td>
                    <?php if (!empty($p['archivo_audio']) && is_file(RUTA_PODCASTS_AUDIO . '/' . $p['archivo_audio'])): ?>
                        <?php $ext = strtolower(pathinfo($p['archivo_audio'], PATHINFO_EXTENSION)); ?>
                        <?php if ($ext === 'mp4'): ?>
                            <video controls style="height:70px; max-width:220px;">
                                <source src="<?= BASE_URL ?>/podcasts_audio/<?= rawurlencode($p['archivo_audio']) ?>" type="video/mp4">
                            </video>
                        <?php else: ?>
                            <audio controls style="height:30px; max-width:220px;">
                                <source src="<?= BASE_URL ?>/podcasts_audio/<?= rawurlencode($p['archivo_audio']) ?>">
                            </audio>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-danger">Sin audio</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="form.php?id=<?= (int)$p['id'] ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Editar</a>
                    <?php if (tiene_rol('admin')): ?>
                        <a href="eliminar.php?id=<?= (int)$p['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar este episodio?');"><i class="fa fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($podcasts)): ?><tr><td colspan="4" class="text-center">No hay episodios.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
