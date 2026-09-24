<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$autores = $pdo->query("SELECT * FROM autores ORDER BY nombres ASC")->fetchAll();

$admin_titulo = 'Autores';
$admin_activo = 'autores';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header">
    Autores
    <a href="form.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nuevo autor</a>
</h1>
<?php mostrar_mensajes(); ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead><tr><th>Nombre</th><th>Nickname</th><th style="width:160px;">Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($autores as $a): ?>
            <tr>
                <td><?= htmlspecialchars(trim($a['nombres'] . ' ' . $a['ap_paterno'] . ' ' . $a['ap_materno'])) ?></td>
                <td><?= htmlspecialchars($a['nickname'] ?? '') ?> <?= $a['es_nickname'] ? '<span class="label label-info">visible</span>' : '' ?></td>
                <td>
                    <a href="form.php?id=<?= (int)$a['id'] ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Editar</a>
                    <?php if (tiene_rol('admin')): ?>
                        <a href="eliminar.php?id=<?= (int)$a['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar este autor? (falla si tiene reportajes asociados)');"><i class="fa fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($autores)): ?><tr><td colspan="3" class="text-center">No hay autores.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
