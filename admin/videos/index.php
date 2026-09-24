<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$videos = $pdo->query("SELECT * FROM videos ORDER BY fecha_publicacion DESC")->fetchAll();

$admin_titulo = 'Videos';
$admin_activo = 'videos';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header">
    Videos
    <a href="form.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nuevo video</a>
</h1>
<?php mostrar_mensajes(); ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead><tr><th>Título</th><th>Fecha</th><th>Embed</th><th style="width:160px;">Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($videos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['titulo']) ?></td>
                <td><?= date('d/m/Y', strtotime($p['fecha_publicacion'])) ?></td>
                <td><a href="<?= htmlspecialchars($p['url_embed']) ?>" target="_blank">Ver <i class="fa fa-external-link"></i></a></td>
                <td>
                    <a href="form.php?id=<?= (int)$p['id'] ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Editar</a>
                    <?php if (tiene_rol('admin')): ?>
                        <a href="eliminar.php?id=<?= (int)$p['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar este video?');"><i class="fa fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($videos)): ?><tr><td colspan="4" class="text-center">No hay videos.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
