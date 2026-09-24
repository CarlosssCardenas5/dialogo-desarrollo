<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/mensajes.php';

// Si ya inició sesión, no tiene sentido ver el login otra vez.
if (usuario_autenticado()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Completa tu correo y tu contraseña.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            // Credenciales correctas: creamos la sesión.
            session_regenerate_id(true);
            $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = trim($usuario['nombres'] . ' ' . $usuario['ap_paterno']);
            $_SESSION['usuario_rol']    = $usuario['rol'];

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Correo o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar - Panel de administración</title>
    <link href="../assets/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/admin/css/startmin.css" rel="stylesheet">
    <link href="../assets/admin/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default" style="margin-top:80px;">
                <div class="panel-heading">
                    <h3 class="panel-title">Ingresar al panel</h3>
                </div>
                <div class="panel-body">
                    <?php mostrar_mensajes(); ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="post" action="login.php">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="Correo" name="email" type="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autofocus required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Contraseña" name="password" type="password" required>
                            </div>
                            <button type="submit" class="btn btn-lg btn-success btn-block">Ingresar</button>
                        </fieldset>
                    </form>
                    <p class="text-center" style="margin-top:15px;">
                        <a href="olvide-password.php">¿Olvidaste tu contraseña?</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
