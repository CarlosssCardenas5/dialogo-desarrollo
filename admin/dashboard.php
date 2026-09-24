<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/mensajes.php';

requerir_login();

$usuario = usuario_actual();
$esRedactor = tiene_rol('redactor');

if ($esRedactor) {
    // El redactor solo ve sus propios números.
    $stmt = $pdo->prepare("SELECT COUNT(*) c FROM reportajes WHERE usuario_id = :uid");
    $stmt->execute(['uid' => $usuario['id']]);
    $totalReportajes = $stmt->fetch()['c'];

    $stmt = $pdo->prepare("SELECT COUNT(*) c FROM noticias WHERE usuario_id = :uid");
    $stmt->execute(['uid' => $usuario['id']]);
    $totalNoticias = $stmt->fetch()['c'];

    $stmt = $pdo->prepare("SELECT COUNT(*) c FROM reportajes WHERE usuario_id = :uid AND estado = 'borrador'");
    $stmt->execute(['uid' => $usuario['id']]);
    $totalBorradores = $stmt->fetch()['c'];
} else {
    $totalReportajes = $pdo->query("SELECT COUNT(*) c FROM reportajes")->fetch()['c'];
    $totalNoticias   = $pdo->query("SELECT COUNT(*) c FROM noticias")->fetch()['c'];
    $totalBoletines  = $pdo->query("SELECT COUNT(*) c FROM boletines")->fetch()['c'];
    $totalPodcasts   = $pdo->query("SELECT COUNT(*) c FROM podcasts")->fetch()['c'];
    $totalVideos     = $pdo->query("SELECT COUNT(*) c FROM videos")->fetch()['c'];
    $totalBorradores = $pdo->query("SELECT COUNT(*) c FROM reportajes WHERE estado = 'borrador'")->fetch()['c'];
}

$admin_titulo = 'Dashboard';
$admin_activo = 'dashboard';
require __DIR__ . '/includes/plantilla_header.php';
?>

<h1 class="page-header">Hola, <?= htmlspecialchars($usuario['nombre']) ?></h1>
<?php mostrar_mensajes(); ?>

<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-newspaper-o fa-5x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= (int)$totalReportajes ?></div>
                        <div><?= $esRedactor ? 'Mis reportajes' : 'Reportajes' ?></div>
                    </div>
                </div>
            </div>
            <a href="reportajes/index.php">
                <div class="panel-footer">
                    <span class="pull-left">Ver todos</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-bullhorn fa-5x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= (int)$totalNoticias ?></div>
                        <div><?= $esRedactor ? 'Mis noticias' : 'Noticias' ?></div>
                    </div>
                </div>
            </div>
            <a href="noticias/index.php">
                <div class="panel-footer">
                    <span class="pull-left">Ver todas</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-pencil-square-o fa-5x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= (int)$totalBorradores ?></div>
                        <div>Borradores pendientes</div>
                    </div>
                </div>
            </div>
            <a href="reportajes/index.php?estado=borrador">
                <div class="panel-footer">
                    <span class="pull-left">Revisar</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>

    <?php if (!$esRedactor): ?>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-file-pdf-o fa-5x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= (int)$totalBoletines ?></div>
                        <div>Boletines</div>
                    </div>
                </div>
            </div>
            <a href="boletines/index.php">
                <div class="panel-footer">
                    <span class="pull-left">Ver todos</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (!$esRedactor): ?>
<div class="row">
    <div class="col-md-6">
        <?php if (!$esRedactor): ?>
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-podcast">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-microphone fa-5x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= (int)$totalPodcasts ?></div>
                        <div>Podcasts</div>
                    </div>
                </div>
            </div>
            <a href="podcasts/index.php">
                <div class="panel-footer">
                    <span class="pull-left">Ver todos</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-video">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3"><i class="fa fa-video-camera fa-5x"></i></div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= (int)$totalVideos ?></div>
                        <div>Videos</div>
                    </div>
                </div>
            </div>
            <a href="videos/index.php">
                <div class="panel-footer">
                    <span class="pull-left">Ver todos</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/plantilla_footer.php'; ?>
