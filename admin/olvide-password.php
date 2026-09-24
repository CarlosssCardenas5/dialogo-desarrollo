<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Si ya inició sesión, no tiene sentido ver esta página.
if (usuario_autenticado()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$enlaceLocal = ''; // fallback para probar en localhost cuando no hay servidor de correo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Escribe tu correo.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nombres, email FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            $error = 'No encontramos ninguna cuenta con ese correo.';
        } else {
            // Token aleatorio: lo enviamos al usuario, pero en la base de
            // datos solo guardamos su hash (igual que una contraseña), así
            // que si alguien ve la tabla no puede armar enlaces válidos.
            $token     = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiraEn  = date('Y-m-d H:i:s', time() + RESET_PASSWORD_HORAS_VIGENCIA * 3600);

            $stmt = $pdo->prepare(
                "INSERT INTO password_resets (usuario_id, token_hash, expira_en) VALUES (:uid, :hash, :exp)"
            );
            $stmt->execute(['uid' => $usuario['id'], 'hash' => $tokenHash, 'exp' => $expiraEn]);

            $esquema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $enlace  = $esquema . '://' . $host . BASE_URL . '/admin/restablecer-password.php?token=' . $token;

            $asunto = 'Recupera tu contraseña - ' . SITE_NAME;
            $cuerpo = "Hola {$usuario['nombres']},\n\n"
                    . "Recibimos una solicitud para restablecer tu contraseña del panel de " . SITE_NAME . ".\n"
                    . "Este enlace es válido por " . RESET_PASSWORD_HORAS_VIGENCIA . " hora(s):\n\n"
                    . $enlace . "\n\n"
                    . "Si tú no pediste esto, ignora este correo.\n";
            $cabeceras = 'From: ' . MAIL_FROM;

            // En XAMPP local normalmente no hay un servidor SMTP configurado,
            // así que mail() casi siempre falla en localhost. Lo intentamos
            // igual (para cuando esto se suba a un hosting real con correo
            // configurado), y si falla mostramos el enlace directo en pantalla
            // para poder probar el flujo completo sin depender del correo.
            $enviado = @mail($usuario['email'], $asunto, $cuerpo, $cabeceras);

            if (!$enviado) {
                $enlaceLocal = $enlace;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Olvidé mi contraseña - Panel de administración</title>
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
                    <h3 class="panel-title">Recuperar contraseña</h3>
                </div>
                <div class="panel-body">
                    <?php if ($enlaceLocal): ?>
                        <div class="alert alert-success">
                            Generamos tu enlace de recuperación (válido por <?= (int)RESET_PASSWORD_HORAS_VIGENCIA ?> hora(s)).
                        </div>
                        <div class="alert alert-warning">
                            <strong>Modo local:</strong> este servidor no tiene un correo real configurado
                            (normal en XAMPP), así que aquí tienes el enlace directo para continuar.
                            En un hosting con correo configurado, este mismo enlace llegaría a tu bandeja de entrada.
                            <br><br>
                            <a href="<?= htmlspecialchars($enlaceLocal) ?>"><?= htmlspecialchars($enlaceLocal) ?></a>
                        </div>
                    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error): ?>
                        <div class="alert alert-success">
                            Te enviamos un enlace para restablecer tu contraseña a tu correo.
                        </div>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <p>Escribe el correo con el que inicias sesión y te enviaremos un enlace para crear una nueva contraseña.</p>
                        <form method="post" action="olvide-password.php">
                            <div class="form-group">
                                <input class="form-control" placeholder="Correo" name="email" type="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autofocus required>
                            </div>
                            <button type="submit" class="btn btn-lg btn-success btn-block">Enviar enlace</button>
                        </form>
                    <?php endif; ?>
                    <p class="text-center" style="margin-top:15px;">
                        <a href="login.php"><i class="fa fa-arrow-left"></i> Volver a iniciar sesión</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
