<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin', 'editor']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$autor = ['id' => 0, 'nombres' => '', 'ap_paterno' => '', 'ap_materno' => '', 'nickname' => '', 'es_nickname' => 0];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM autores WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $autor = $stmt->fetch() ?: $autor;
}

$admin_titulo = $id ? 'Editar autor' : 'Nuevo autor';
$admin_activo = 'autores';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header"><?= htmlspecialchars($admin_titulo) ?></h1>
<?php mostrar_mensajes(); ?>

<form method="post" action="guardar.php">
    <input type="hidden" name="id" value="<?= (int)$autor['id'] ?>">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Nombres</label>
                <input type="text" name="nombres" class="form-control" required value="<?= htmlspecialchars($autor['nombres']) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Apellido paterno</label>
                <input type="text" name="ap_paterno" class="form-control" value="<?= htmlspecialchars($autor['ap_paterno']) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Apellido materno</label>
                <input type="text" name="ap_materno" class="form-control" value="<?= htmlspecialchars($autor['ap_materno']) ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Nickname / firma (ej. "Redacción DDP")</label>
                <input type="text" name="nickname" class="form-control" value="<?= htmlspecialchars($autor['nickname']) ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="checkbox" style="margin-top:25px;">
                <label><input type="checkbox" name="es_nickname" value="1" <?= $autor['es_nickname'] ? 'checked' : '' ?>> Mostrar el nickname en vez del nombre completo</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
    <a href="index.php" class="btn btn-default">Cancelar</a>
</form>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
