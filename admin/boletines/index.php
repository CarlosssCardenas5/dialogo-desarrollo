<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$boletines = $pdo->query("SELECT * FROM boletines ORDER BY fecha_publicacion DESC")->fetchAll();

$admin_titulo = 'Boletines NTEP';
$admin_activo = 'boletines';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header">
    Boletines
    <a href="form.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nuevo boletín</a>
</h1>
<?php mostrar_mensajes(); ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead><tr><th>Nº</th><th>Resumen</th><th>Fecha</th><th>PDF</th><th style="width:160px;">Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($boletines as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['numero_boletin']) ?></td>
                <td><?= htmlspecialchars(strlen($b['resumen']) > 80 ? substr($b['resumen'], 0, 80) . '...' : $b['resumen']) ?></td>
                <td><?= date('d/m/Y', strtotime($b['fecha_publicacion'])) ?></td>
                <td><a href="<?= BASE_URL ?>/boletines/<?= htmlspecialchars($b['archivo_pdf']) ?>" target="_blank"><i class="fa fa-file-pdf-o"></i> Ver</a></td>
                <td>
                    <a href="form.php?id=<?= (int)$b['id'] ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Editar</a>
                    <?php if (tiene_rol('admin')): ?>
                        <a href="eliminar.php?id=<?= (int)$b['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar este boletín?');"><i class="fa fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($boletines)): ?><tr><td colspan="5" class="text-center">No hay boletines.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
