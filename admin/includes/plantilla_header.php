<?php
/**
 * Encabezado + sidebar del panel de administración.
 * Antes de incluir este archivo, cada página debe:
 *   1. require '../../includes/auth.php'  (o la ruta relativa que corresponda)
 *   2. llamar requerir_login() o requerir_rol([...])
 *   3. definir $admin_titulo y $admin_activo ('dashboard'|'reportajes'|'noticias'|
 *      'boletines'|'podcasts'|'videos'|'autores'|'usuarios')
 */

$rolActual = $_SESSION['usuario_rol'] ?? '';

function menu_activo($seccion, $admin_activo)
{
    return $seccion === $admin_activo ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($admin_titulo ?? 'Panel de administración') ?></title>

    <link href="<?= BASE_URL ?>/assets/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/admin/css/metisMenu.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/admin/css/timeline.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/admin/css/startmin.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/admin/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <style>
        .panel-podcast { border-color: #6f42c1; }
        .panel-podcast > .panel-heading { color: #fff; background-color: #6f42c1; border-color: #6f42c1; }
        .panel-podcast .panel-footer { background-color: #f7f4fb; }

        .panel-video { border-color: #17a2b8; }
        .panel-video > .panel-heading { color: #fff; background-color: #17a2b8; border-color: #17a2b8; }
        .panel-video .panel-footer { background-color: #eefbfd; }

        /* Vista previa de audio al editar un podcast (admin/podcasts/form.php):
           el <audio controls> nativo se ve "pelado"; le damos un marco propio. */
        .audio-preview {
            display: block;
            width: 100%;
            background: #f5f5f5;
            border: 1px solid #e3e3e3;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .audio-preview audio { width: 100%; height: 34px; }
    </style>
</head>
<body>
<div id="wrapper">

    <!-- Navbar superior -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="navbar-header">
            <a class="navbar-brand" href="<?= BASE_URL ?>/admin/dashboard.php">DDP - Panel</a>
        </div>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
        </button>

        <ul class="nav navbar-nav navbar-left navbar-top-links">
            <li><a href="<?= BASE_URL ?>/index.php" target="_blank"><i class="fa fa-globe fa-fw"></i> Ver sitio web</a></li>
        </ul>

        <ul class="nav navbar-right navbar-top-links">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i> <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?>
                    <span class="label label-info"><?= htmlspecialchars($rolActual) ?></span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="<?= BASE_URL ?>/admin/logout.php"><i class="fa fa-sign-out fa-fw"></i> Cerrar sesión</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar navbar-default" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <li class="<?= menu_activo('dashboard', $admin_activo) ?>">
                    <a href="<?= BASE_URL ?>/admin/dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                </li>

                <?php if (tiene_rol(['admin', 'editor'])): ?>
                    <li class="<?= menu_activo('reportajes', $admin_activo) ?>">
                        <a href="<?= BASE_URL ?>/admin/reportajes/index.php"><i class="fa fa-newspaper-o fa-fw"></i> Reportajes</a>
                    </li>
                    <li class="<?= menu_activo('noticias', $admin_activo) ?>">
                        <a href="<?= BASE_URL ?>/admin/noticias/index.php"><i class="fa fa-bullhorn fa-fw"></i> Noticias</a>
                    </li>
                    <li class="<?= menu_activo('boletines', $admin_activo) ?>">
                        <a href="<?= BASE_URL ?>/admin/boletines/index.php"><i class="fa fa-file-pdf-o fa-fw"></i> Boletines</a>
                    </li>
                    <li class="<?= menu_activo('podcasts', $admin_activo) ?>">
                        <a href="<?= BASE_URL ?>/admin/podcasts/index.php"><i class="fa fa-microphone fa-fw"></i> Podcasts</a>
                    </li>
                    <li class="<?= menu_activo('videos', $admin_activo) ?>">
                        <a href="<?= BASE_URL ?>/admin/videos/index.php"><i class="fa fa-video-camera fa-fw"></i> Videos</a>
                    </li>
                    <li class="<?= menu_activo('especiales', $admin_activo) ?>">
                        <a href="<?= BASE_URL ?>/admin/especiales/index.php"><i class="fa fa-star fa-fw"></i> Especiales</a>
                    </li>
                    <li class="<?= menu_activo('autores', $admin_activo) ?>">
                        <a href="<?= BASE_URL ?>/admin/autores/index.php"><i class="fa fa-pencil fa-fw"></i> Autores</a>
                    </li>
                <?php else: /* redactor: solo su propio contenido */ ?>
                    <li class="<?= menu_activo('reportajes', $admin_activo) ?>">
                        <a href="<?= BASE_URL ?>/admin/reportajes/index.php"><i class="fa fa-newspaper-o fa-fw"></i> Mis reportajes</a>
                    </li>
                    <li class="<?= menu_activo('noticias', $admin_activo) ?>">
                        <a href="<?= BASE_URL ?>/admin/noticias/index.php"><i class="fa fa-bullhorn fa-fw"></i> Mis noticias</a>
                    </li>
                <?php endif; ?>

                <?php if (tiene_rol('admin')): ?>
                    <li class="<?= menu_activo('usuarios', $admin_activo) ?>">
                        <a href="<?= BASE_URL ?>/admin/usuarios/index.php"><i class="fa fa-users fa-fw"></i> Usuarios</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </aside>

    <div id="page-wrapper">
        <div class="container-fluid">
