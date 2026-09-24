<?php
/**
 * Conexión a la base de datos "revista_digital".
 * Ajusta estos datos solo si tu MySQL de XAMPP tiene otro usuario/clave.
 * Por defecto, XAMPP trae el usuario "root" sin contraseña.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'revista_digital');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // En producción no se debe mostrar el error real al usuario.
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
