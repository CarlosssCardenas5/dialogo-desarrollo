<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_login();
$usuario = usuario_actual();
$esRedactor = tiene_rol('redactor');

$filtroEstado = $_GET['estado'] ?? '';

$sql = "SELECT r.*, a.nombres AS autor_nombres, a.ap_paterno AS autor_ap
        FROM reportajes r
        LEFT JOIN autores a ON a.id = r.autor_id
        WHERE 1=1";
$params = [];

if ($esRedactor) {
    $sql .= " AND r.usuario_id = :uid";
    $params['uid'] = $usuario['id'];
}
if (in_array($filtroEstado, ['borrador', 'publicado', 'oculto'], true)) {
    $sql .= " AND r.estado = :estado";
    $params['estado'] = $filtroEstado;
}
$sql .= " ORDER BY r.fecha_publicacion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reportajes = $stmt->fetchAll();

$admin_titulo = $esRedactor ? 'Mis reportajes' : 'Reportajes';
$admin_activo = 'reportajes';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header">
    <?= htmlspecialchars($admin_titulo) ?>
    <a href="form.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nuevo reportaje</a>
</h1>
<?php mostrar_mensajes(); ?>

<div class="btn-group" style="margin-bottom:15px;">
    <a href="?estado=" class="btn btn-default <?= $filtroEstado === '' ? 'active' : '' ?>">Todos</a>
    <a href="?estado=borrador" class="btn btn-default <?= $filtroEstado === 'borrador' ? 'active' : '' ?>">Borradores</a>
    <a href="?estado=publicado" class="btn btn-default <?= $filtroEstado === 'publicado' ? 'active' : '' ?>">Publicados</a>
    <a href="?estado=oculto" class="btn btn-default <?= $filtroEstado === 'oculto' ? 'active' : '' ?>">Ocultos</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Fecha</th>
                <th>Destacado</th>
                <th>Estado</th>
                <th style="width:220px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($reportajes as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['titulo']) ?></td>
                <td><?= htmlspecialchars(trim($r['autor_nombres'] . ' ' . $r['autor_ap'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['fecha_publicacion'])) ?></td>
                <td><?= $r['es_destacado'] ? '<span class="label label-warning">Sí</span>' : '—' ?></td>
                <td>
                    <?php
                        $badge = ['borrador' => 'default', 'publicado' => 'success', 'oculto' => 'default'][$r['estado']] ?? 'default';
                    ?>
                    <span class="label label-<?= $badge ?>"><?= htmlspecialchars($r['estado']) ?></span>
                </td>
                <td>
                    <a href="form.php?id=<?= (int)$r['id'] ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Editar</a>

                    <?php if (tiene_rol(['admin', 'editor'])): ?>
                        <?php if ($r['estado'] !== 'publicado'): ?>
                            <a href="cambiar_estado.php?id=<?= (int)$r['id'] ?>&estado=publicado" class="btn btn-xs btn-success"><i class="fa fa-check"></i> Publicar</a>
                        <?php else: ?>
                            <a href="cambiar_estado.php?id=<?= (int)$r['id'] ?>&estado=oculto" class="btn btn-xs btn-warning"><i class="fa fa-eye-slash"></i> Ocultar</a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (tiene_rol('admin')): ?>
                        <a href="eliminar.php?id=<?= (int)$r['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar este reportaje definitivamente?');"><i class="fa fa-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($reportajes)): ?>
            <tr><td colspan="6" class="text-center">No hay reportajes para mostrar.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
