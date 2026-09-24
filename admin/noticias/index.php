<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_login();
$usuario = usuario_actual();
$esRedactor = tiene_rol('redactor');

$filtroEstado = $_GET['estado'] ?? '';

$sql = "SELECT * FROM noticias WHERE 1=1";
$params = [];
if ($esRedactor) {
    $sql .= " AND usuario_id = :uid";
    $params['uid'] = $usuario['id'];
}
if (in_array($filtroEstado, ['borrador', 'publicado', 'oculto'], true)) {
    $sql .= " AND estado = :estado";
    $params['estado'] = $filtroEstado;
}
$sql .= " ORDER BY fecha_publicacion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$noticias = $stmt->fetchAll();

$admin_titulo = $esRedactor ? 'Mis noticias' : 'Noticias';
$admin_activo = 'noticias';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header">
    <?= htmlspecialchars($admin_titulo) ?>
    <a href="form.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nueva noticia</a>
</h1>
<?php mostrar_mensajes(); ?>

<div class="btn-group" style="margin-bottom:15px;">
    <a href="?estado=" class="btn btn-default <?= $filtroEstado === '' ? 'active' : '' ?>">Todas</a>
    <a href="?estado=borrador" class="btn btn-default <?= $filtroEstado === 'borrador' ? 'active' : '' ?>">Borradores</a>
    <a href="?estado=publicado" class="btn btn-default <?= $filtroEstado === 'publicado' ? 'active' : '' ?>">Publicadas</a>
    <a href="?estado=oculto" class="btn btn-default <?= $filtroEstado === 'oculto' ? 'active' : '' ?>">Ocultas</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead><tr><th>Título</th><th>Fecha</th><th>Enlace</th><th>Estado</th><th style="width:220px;">Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($noticias as $n): ?>
            <tr>
                <td><?= htmlspecialchars($n['titulo']) ?></td>
                <td><?= date('d/m/Y', strtotime($n['fecha_publicacion'])) ?></td>
                <td><a href="<?= htmlspecialchars($n['link_externo']) ?>" target="_blank">Ver <i class="fa fa-external-link"></i></a></td>
                <td>
                    <?php $badge = ['borrador' => 'default', 'publicado' => 'success', 'oculto' => 'default'][$n['estado']] ?? 'default'; ?>
                    <span class="label label-<?= $badge ?>"><?= htmlspecialchars($n['estado']) ?></span>
                </td>
                <td>
                    <a href="form.php?id=<?= (int)$n['id'] ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Editar</a>
                    <?php if (tiene_rol(['admin', 'editor'])): ?>
                        <?php if ($n['estado'] !== 'publicado'): ?>
                            <a href="cambiar_estado.php?id=<?= (int)$n['id'] ?>&estado=publicado" class="btn btn-xs btn-success"><i class="fa fa-check"></i> Publicar</a>
                        <?php else: ?>
                            <a href="cambiar_estado.php?id=<?= (int)$n['id'] ?>&estado=oculto" class="btn btn-xs btn-warning"><i class="fa fa-eye-slash"></i> Ocultar</a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (tiene_rol('admin')): ?>
                        <a href="eliminar.php?id=<?= (int)$n['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar esta noticia?');"><i class="fa fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($noticias)): ?>
            <tr><td colspan="5" class="text-center">No hay noticias para mostrar.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
