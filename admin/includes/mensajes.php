<?php
/** Guarda un mensaje para mostrarlo justo después de un redirect. */
function set_mensaje(string $tipo, string $texto): void
{
    $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
}

/** Imprime (y limpia) el mensaje guardado, si existe. Tipos: success | danger | warning. */
function mostrar_mensajes(): void
{
    if (!empty($_SESSION['mensaje'])) {
        $m = $_SESSION['mensaje'];
        echo '<div class="alert alert-' . htmlspecialchars($m['tipo']) . ' alert-dismissible">'
           . '<button type="button" class="close" data-dismiss="alert">&times;</button>'
           . htmlspecialchars($m['texto']) . '</div>';
        unset($_SESSION['mensaje']);
    }
}
