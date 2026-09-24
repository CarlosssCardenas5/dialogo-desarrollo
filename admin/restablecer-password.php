<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/mensajes.php';

if (usuario_autenticado()) {
    header('Location: dashboard.php');
    exit;
}

/** Busca un token vigente (no usado y no vencido) y devuelve la fila junto con el usuario. */
function buscar_token_valido(PDO $pdo, string $token): ?array
{
    if ($token === '') {
        return null;
    }
    $hash = hash('sha256', $token);
    $stmt = $pdo->prepare(
        "SELECT pr.id AS reset_id, pr.usuario_id, u.email
         FROM password_resets pr
         INNER JOIN usuarios u ON u.id = pr.usuario_id
         WHERE pr.token_hash = :hash AND pr.usado = 0 AND pr.expira_en > NOW()
         LIMIT 1"
    );
    $stmt->execute(['hash' => $hash]);
    $fila = $stmt->fetch();
    return $fila ?: null;
}

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$info  = buscar_token_valido($pdo, $token);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $info) {
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $password2) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = :hash WHERE id = :id");
        $stmt->execute([
            'hash' => password_hash($password, PASSWORD_DEFAULT),
            'id'   => $info['usuario_id'],
        ]);

        // Este token ya se usó, y de paso invalidamos cualquier otro enlace
        // de recuperación pendiente de ese usuario (por si pidió varios).
        $stmt = $pdo->prepare("UPDATE password_resets SET usado = 1 WHERE usuario_id = :uid AND usado = 0");
        $stmt->execute(['uid' => $info['usuario_id']]);

        $pdo->commit();

        set_mensaje('success', 'Tu contraseña se actualizó. Ya puedes iniciar sesión.');
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer contraseña - Panel de administración</title>
    <link href="../assets/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/admin/css/startmin.css" rel="stylesheet">
    <link href="../assets/admin/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-5 col-md-offset-3">
            <div class="login-panel panel panel-default" style="margin-top:80px;">
                <div class="panel-heading">
                    <h3 class="panel-title">Crear nueva contraseña</h3>
                </div>
                <div class="panel-body">
                    <?php if (!$info): ?>
                        <div class="alert alert-danger">
                            Este enlace ya no es válido: puede que haya vencido o que ya se haya usado.
                        </div>
                        <p><a href="olvide-password.php">Solicita un enlace nuevo</a></p>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <p>Cuenta: <strong><?= htmlspecialchars($info['email']) ?></strong></p>
                        <form method="post" action="restablecer-password.php">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            <div class="form-group">
                                <input class="form-control" placeholder="Nueva contraseña" name="password" type="password" minlength="8" autofocus required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Repite la nueva contraseña" name="password2" type="password" minlength="8" required>
                            </div>
                            <p class="help-block">Mínimo 8 caracteres.</p>
                            <button type="submit" class="btn btn-lg btn-success btn-block">Guardar nueva contraseña</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
