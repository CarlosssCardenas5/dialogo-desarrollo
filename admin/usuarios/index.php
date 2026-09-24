<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin']);
$usuario = usuario_actual();

$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY nombres ASC")->fetchAll();

$admin_titulo = 'Usuarios';
$admin_activo = 'usuarios';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header">
    Usuarios
    <a href="form.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nuevo usuario</a>
</h1>
<?php mostrar_mensajes(); ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th style="width:160px;">Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= htmlspecialchars(trim($u['nombres'] . ' ' . $u['ap_paterno'])) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="label label-info"><?= htmlspecialchars($u['rol']) ?></span></td>
                <td>
                    <a href="form.php?id=<?= (int)$u['id'] ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Editar</a>
                    <?php if ((int)$u['id'] !== (int)$usuario['id']): ?>
                        <a href="eliminar.php?id=<?= (int)$u['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar este usuario?');"><i class="fa fa-trash"></i></a>
                    <?php else: ?>
                        <span class="text-muted">(tú)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
