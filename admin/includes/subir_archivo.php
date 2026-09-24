<?php
/**
 * Sube un archivo de $_FILES a una carpeta del servidor y devuelve el
 * nombre generado (para guardar en la base de datos), o null si no se
 * subió nada / el archivo no es válido.
 *
 * Si se pasa $error, se devuelve allí el motivo de fallo para que el
 * formulario pueda mostrar un mensaje útil al usuario.
 */
function subir_archivo(string $campo, string $carpetaAbsoluta, array $extensionesPermitidas, ?string &$error = null): ?string
{
    $error = null;

    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $archivo = $_FILES[$campo];

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $mensajes = [
            UPLOAD_ERR_INI_SIZE   => 'El archivo supera el tamaño máximo permitido por PHP.',
            UPLOAD_ERR_FORM_SIZE  => 'El archivo supera el tamaño máximo permitido por el formulario.',
            UPLOAD_ERR_PARTIAL    => 'El archivo se subió de forma incompleta.',
            UPLOAD_ERR_NO_TMP_DIR => 'PHP no tiene disponible la carpeta temporal de subida.',
            UPLOAD_ERR_CANT_WRITE => 'PHP no pudo escribir el archivo en el disco.',
            UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida del archivo.',
        ];
        $error = $mensajes[$archivo['error']] ?? 'No se pudo subir el archivo.';
        return null;
    }

    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $extensionesPermitidas, true)) {
        $error = 'Formato no permitido. Formatos aceptados: ' . strtoupper(implode(', ', $extensionesPermitidas)) . '.';
        return null;
    }

    if (!is_dir($carpetaAbsoluta) && !mkdir($carpetaAbsoluta, 0775, true) && !is_dir($carpetaAbsoluta)) {
        $error = 'No se pudo crear la carpeta de destino del archivo.';
        return null;
    }

    $nombre = 'ddp_' . bin2hex(random_bytes(12)) . '.' . $ext;
    $destino = $carpetaAbsoluta . '/' . $nombre;

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        $error = 'PHP recibió el archivo, pero no pudo guardarlo en la carpeta de destino.';
        return null;
    }

    return $nombre;
}

/** Borra un archivo (si existe) de una carpeta. Silencioso si no existe. */
function borrar_archivo(?string $nombre, string $carpetaAbsoluta): void
{
    if (!$nombre) {
        return;
    }

    $ruta = $carpetaAbsoluta . '/' . $nombre;
    if (is_file($ruta)) {
        @unlink($ruta);
    }
}
