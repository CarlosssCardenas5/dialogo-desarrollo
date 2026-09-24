<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$especiales = $pdo->query("SELECT * FROM especiales ORDER BY orden ASC, id DESC")->fetchAll();

$admin_titulo = 'Especiales';
$admin_activo = 'especiales';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header">
    Especiales
    <a href="form.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nuevo especial</a>
</h1>
<?php mostrar_mensajes(); ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead><tr><th style="width:90px;">Imagen</th><th>Título</th><th>Video</th><th style="width:70px;">Orden</th><th style="width:160px;">Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($especiales as $e): ?>
            <tr>
                <td><img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($e['imagen']) ?>" style="max-height:60px;" class="img-thumbnail"></td>
                <td><?= htmlspecialchars($e['titulo']) ?></td>
                <td><a href="<?= htmlspecialchars($e['url_video']) ?>" target="_blank">Ver <i class="fa fa-external-link"></i></a></td>
                <td><?= (int)$e['orden'] ?></td>
                <td>
                    <a href="form.php?id=<?= (int)$e['id'] ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Editar</a>
                    <?php if (tiene_rol('admin')): ?>
                        <a href="eliminar.php?id=<?= (int)$e['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar este especial?');"><i class="fa fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($especiales)): ?><tr><td colspan="5" class="text-center">No hay especiales.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
