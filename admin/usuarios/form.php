<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$usuarioForm = ['id' => 0, 'nombres' => '', 'ap_paterno' => '', 'ap_materno' => '', 'email' => '', 'rol' => 'redactor'];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $usuarioForm = $stmt->fetch() ?: $usuarioForm;
}

$admin_titulo = $id ? 'Editar usuario' : 'Nuevo usuario';
$admin_activo = 'usuarios';
require __DIR__ . '/../includes/plantilla_header.php';
?>

<h1 class="page-header"><?= htmlspecialchars($admin_titulo) ?></h1>
<?php mostrar_mensajes(); ?>

<form method="post" action="guardar.php">
    <input type="hidden" name="id" value="<?= (int)$usuarioForm['id'] ?>">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Nombres</label>
                <input type="text" name="nombres" class="form-control" required value="<?= htmlspecialchars($usuarioForm['nombres']) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Apellido paterno</label>
                <input type="text" name="ap_paterno" class="form-control" required value="<?= htmlspecialchars($usuarioForm['ap_paterno']) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Apellido materno</label>
                <input type="text" name="ap_materno" class="form-control" value="<?= htmlspecialchars($usuarioForm['ap_materno']) ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($usuarioForm['email']) ?>">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Rol</label>
                <select name="rol" class="form-control">
                    <?php foreach (['admin', 'editor', 'redactor'] as $r): ?>
                        <option value="<?= $r ?>" <?= $usuarioForm['rol'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Contraseña <?= $id ? '(déjala vacía para no cambiarla)' : '' ?></label>
                <input type="password" name="password" class="form-control" <?= $id ? '' : 'required' ?> minlength="6">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
    <a href="index.php" class="btn btn-default">Cancelar</a>
</form>

<?php require __DIR__ . '/../includes/plantilla_footer.php'; ?>
