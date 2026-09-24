<?php
/**
 * Configuración general del sitio.
 *
 *
 * Si accedes directo en la raíz de htdocs (http://localhost/index.php)
 * entonces deja BASE_URL como cadena vacía ''.
 */
define('BASE_URL', '/dialogo-desarrollo');

define('SITE_NAME', 'Diálogo y Desarrollo Perú');

/* Rutas absolutas del servidor, usadas al subir archivos desde el admin */
define('RUTA_IMG', __DIR__ . '/../assets/web/img');
define('RUTA_BOLETINES', __DIR__ . '/../boletines');
define('RUTA_PODCASTS_AUDIO', __DIR__ . '/../podcasts_audio');

define('MAIL_FROM', 'no-responder@dialogoydesarrollo.local');
define('RESET_PASSWORD_HORAS_VIGENCIA', 1);

/**
 * Da formato "Ago 18, 2026" a una fecha SQL (YYYY-MM-DD).
 */
function fecha_larga_es(string $fechaSql): string
{
    $meses = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
    $ts = strtotime($fechaSql);
    return $meses[(int)date('n', $ts)] . ' ' . date('d', $ts) . ', ' . date('Y', $ts);
}

/**
 * Da formato "Agosto 2026" a un periodo "YYYY-MM" (usado en el archivo de reportajes).
 */
function periodo_largo_es(string $periodoYm): string
{
    $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
    [$anio, $mes] = array_map('intval', explode('-', $periodoYm));
    return ($meses[$mes] ?? $periodoYm) . ' ' . $anio;
}

