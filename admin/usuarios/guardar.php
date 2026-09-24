<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/mensajes.php';

requerir_rol(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id         = (int)($_POST['id'] ?? 0);
$nombres    = trim($_POST['nombres'] ?? '');
$ap_paterno = trim($_POST['ap_paterno'] ?? '');
$ap_materno = trim($_POST['ap_materno'] ?? '');
$email      = trim($_POST['email'] ?? '');
$rol        = in_array($_POST['rol'] ?? '', ['admin', 'editor', 'redactor'], true) ? $_POST['rol'] : 'redactor';
$password   = $_POST['password'] ?? '';

if ($nombres === '' || $ap_paterno === '' || $email === '') {
    set_mensaje('danger', 'Nombres, apellido paterno y email son obligatorios.');
    header('Location: form.php' . ($id ? "?id=$id" : ''));
    exit;
}

// Evitar emails duplicados.
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email AND id != :id");
$stmt->execute(['email' => $email, 'id' => $id]);
if ($stmt->fetch()) {
    set_mensaje('danger', 'Ya existe otro usuario con ese email.');
    header('Location: form.php' . ($id ? "?id=$id" : ''));
    exit;
}

if ($id) {
    if ($password !== '') {
        $pdo->prepare("UPDATE usuarios SET nombres=:n, ap_paterno=:p, ap_materno=:m, email=:e, rol=:r, password_hash=:pass WHERE id=:id")
            ->execute([
                'n' => $nombres, 'p' => $ap_paterno, 'm' => $ap_materno, 'e' => $email,
                'r' => $rol, 'pass' => password_hash($password, PASSWORD_DEFAULT), 'id' => $id,
            ]);
    } else {
        $pdo->prepare("UPDATE usuarios SET nombres=:n, ap_paterno=:p, ap_materno=:m, email=:e, rol=:r WHERE id=:id")
            ->execute(['n' => $nombres, 'p' => $ap_paterno, 'm' => $ap_materno, 'e' => $email, 'r' => $rol, 'id' => $id]);
    }
    // Si el usuario se edita a sí mismo, refrescamos su sesión.
    if ((int)$id === (int)$_SESSION['usuario_id']) {
        $_SESSION['usuario_nombre'] = trim($nombres . ' ' . $ap_paterno);
        $_SESSION['usuario_rol']    = $rol;
    }
    $mensaje = 'Usuario actualizado.';
} else {
    if (strlen($password) < 6) {
        set_mensaje('danger', 'La contraseña debe tener al menos 6 caracteres.');
        header('Location: form.php');
        exit;
    }
    $pdo->prepare("INSERT INTO usuarios (nombres, ap_paterno, ap_materno, email, password_hash, rol) VALUES (:n, :p, :m, :e, :pass, :r)")
        ->execute([
            'n' => $nombres, 'p' => $ap_paterno, 'm' => $ap_materno, 'e' => $email,
            'pass' => password_hash($password, PASSWORD_DEFAULT), 'r' => $rol,
        ]);
    $mensaje = 'Usuario creado.';
}

set_mensaje('success', $mensaje);
header('Location: index.php');
exit;
