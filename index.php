<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';

$page_title  = SITE_NAME . ' - Inicio';
$page_active = 'inicio';

/* 1. Reportaje destacado (hero) */
$destacado = $pdo->query("
    SELECT r.*, a.nombres AS autor_nombres, a.ap_paterno AS autor_ap
    FROM reportajes r
    LEFT JOIN autores a ON a.id = r.autor_id
    WHERE r.es_destacado = 1 AND r.estado = 'publicado'
    ORDER BY r.fecha_publicacion DESC
    LIMIT 1
")->fetch();

$idExcluir = $destacado['id'] ?? 0;

/* 2. Reportajes recientes (grilla de 3) */
$stmt = $pdo->prepare("
    SELECT * FROM reportajes
    WHERE id != :idExcluir AND estado = 'publicado'
    ORDER BY fecha_publicacion DESC
    LIMIT 3
");
$stmt->execute(['idExcluir' => $idExcluir]);
$reportajesRecientes = $stmt->fetchAll();

/* 3. Noticias recientes */
$noticias = $pdo->query("
    SELECT * FROM noticias WHERE estado = 'publicado' ORDER BY fecha_publicacion DESC LIMIT 3
")->fetchAll();

/* 4. Boletín NTEP destacado (marcado desde el admin); si no hay ninguno marcado, se usa el más reciente */
$boletin = $pdo->query("
    SELECT * FROM boletines WHERE es_destacado = 1 ORDER BY fecha_publicacion DESC LIMIT 1
")->fetch();
if (!$boletin) {
    $boletin = $pdo->query("SELECT * FROM boletines ORDER BY fecha_publicacion DESC LIMIT 1")->fetch();
}

/* 5. Podcasts recientes */
$podcasts = $pdo->query("
    SELECT * FROM podcasts ORDER BY fecha_publicacion DESC LIMIT 4
")->fetchAll();

/* 6. Especiales: imagen + video, gestionados aparte desde el admin */
$especiales = $pdo->query("SELECT * FROM especiales ORDER BY orden ASC, id DESC LIMIT 8")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero / Reportaje destacado -->
<section class="w3l-video w3l-homeblock3" id="video">
    <div class="container-fluid">
        <div class="video-grids-info row">
            <div class="video-gd-right col-lg-6 p-0">
                <div class="position-relative">
                    <?php if ($destacado): ?>
                        <a href="<?= BASE_URL ?>/paginas/reportaje.php?id=<?= (int)$destacado['id'] ?>">
                            <img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($destacado['foto_principal'] ?: 'placeholder.jpg') ?>" alt="" class="img-fluid">
                        </a>
                    <?php else: ?>
                        <img src="<?= BASE_URL ?>/assets/web/img/placeholder.jpg" alt="" class="img-fluid">
                    <?php endif; ?>
                </div>
            </div>
            <div class="video-gd-left col-lg-6 p-lg-5 p-4 align-self">
                <div class="p-xl-4 p-0 video-wrap">
                    <?php if ($destacado): ?>
                        <h5><?= fecha_larga_es($destacado['fecha_publicacion']) ?></h5>
                        <h3 class="title-big text-left mb-4">
                            <a href="<?= BASE_URL ?>/paginas/reportaje.php?id=<?= (int)$destacado['id'] ?>"><?= htmlspecialchars($destacado['titulo']) ?></a>
                        </h3>
                        <p><?= htmlspecialchars($destacado['resumen_corto']) ?></p>
                        <a href="<?= BASE_URL ?>/paginas/reportaje.php?id=<?= (int)$destacado['id'] ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                    <?php else: ?>
                        <h3 class="title-big text-left mb-4">Aún no hay un reportaje destacado</h3>
                        <p>Marca un reportaje como "destacado" desde el panel de administración para que aparezca aquí.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reportajes recientes -->
<div class="grids-block-5 py-1">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php foreach ($reportajesRecientes as $r): ?>
                <div class="col-lg-4 col-md-6 grids5-info mt-5">
                    <a href="<?= BASE_URL ?>/paginas/reportaje.php?id=<?= (int)$r['id'] ?>" class="d-block foto-recorte">
                        <img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($r['foto_principal'] ?: 'placeholder.jpg') ?>" alt="" class="img-fluid">
                    </a>
                    <div class="blog-info">
                        <h5><?= fecha_larga_es($r['fecha_publicacion']) ?></h5>
                        <h4><a href="<?= BASE_URL ?>/paginas/reportaje.php?id=<?= (int)$r['id'] ?>" class="d-block"><?= htmlspecialchars($r['titulo']) ?></a></h4>
                        <a href="<?= BASE_URL ?>/paginas/reportaje.php?id=<?= (int)$r['id'] ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($reportajesRecientes)): ?>
                    <div class="col-12 text-center py-4"><p>Todavía no hay reportajes cargados.</p></div>
                <?php endif; ?>
            </div>
            <div class="pagination">
                <ul><li><a href="<?= BASE_URL ?>/paginas/reportajes.php">Ver todos</a></li></ul>
            </div>
        </div>
    </section>
</div>

<!-- Noticias recientes -->
<section class="breadcrumb-area py-sm-5 py-1">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-contents">
                    <h2 class="title-big">Noticias Recientes</h2><a class="anchor" id="actualidad"></a>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="grids-block-5 py-5">
    <section class="py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <?php foreach ($noticias as $i => $n): ?>
                <div class="col-lg-4 col-md-6 grids5-info <?= $i === 0 ? '' : 'mt-lg-0 mt-5' ?>">
                    <a target="_blank" rel="noopener" href="<?= htmlspecialchars($n['link_externo']) ?>" class="d-block">
                        <img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($n['foto'] ?: 'placeholder.jpg') ?>" alt="" class="img-fluid">
                    </a>
                    <div class="blog-info">
                        <h5><?= fecha_larga_es($n['fecha_publicacion']) ?></h5>
                        <h4><a target="_blank" rel="noopener" href="<?= htmlspecialchars($n['link_externo']) ?>" class="d-block"><?= htmlspecialchars($n['titulo']) ?></a></h4>
                        <a target="_blank" rel="noopener" href="<?= htmlspecialchars($n['link_externo']) ?>" class="btn mt-4 p-0">Leer <span class="fa fa-arrow-right"></span></a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($noticias)): ?>
                    <div class="col-12 text-center py-4"><p>Todavía no hay noticias cargadas.</p></div>
                <?php endif; ?>
            </div>
            <div class="pagination">
                <ul><li><a target="_blank" rel="noopener" href="https://www.facebook.com/DialogoyDesarrolloPeru">Ver todos</a></li></ul>
            </div>
        </div>
    </section>
</div>

<!-- Boletín NTEP -->
<?php if ($boletin): ?>
<section class="w3l-homeblock5 py-0">
    <div class="container py-lg-5 py-4">
        <div class="row">
            <div class="col-lg-8 align-self">
                <h3 class="title-big mb-4">Boletín NTEP</h3>
                <p><?= nl2br(htmlspecialchars($boletin['resumen'])) ?></p>
                <div class="row mt-sm-4 mt-2 px-3">
                    <div class="col-6 p-0">
                        <span>Nº <?= htmlspecialchars($boletin['numero_boletin']) ?></span>
                        <h4><?= date('d \d\e F', strtotime($boletin['fecha_publicacion'])) ?></h4>
                    </div>
                    <div class="col-6 p-0">
                        <?php $pdfDestacadoExiste = !empty($boletin['archivo_pdf']) && is_file(RUTA_BOLETINES . '/' . $boletin['archivo_pdf']); ?>
                        <?php if ($pdfDestacadoExiste): ?>
                            <span><a href="<?= BASE_URL ?>/boletines/<?= rawurlencode($boletin['archivo_pdf']) ?>" target="_blank" class="facebook"><span class="fa fa-download"></span></a></span>
                            <h4>Ver Boletín</h4>
                        <?php else: ?>
                            <span class="facebook" style="opacity:.5;" title="PDF aún no disponible"><span class="fa fa-download"></span></span>
                            <h4>PDF pendiente</h4>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-center text-lg-left mt-4">
                    <a href="<?= BASE_URL ?>/paginas/boletines.php" class="btn btn-style btn-primary">Ver todos</a>
                </div>
            </div>
            <div class="col-lg-4 mt-lg-0 mt-4">
                <img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($boletin['foto_portada'] ?: 'placeholder.jpg') ?>" class="img-fluid radius-image" alt="">
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Podcast -->
<section class="w3l-homeblock3 py-5">
    <div class="container py-lg-5 py-md-4">
        <h3 class="title-big mb-5 text-center">Podcast</h3>
        <div class="row">
            <?php foreach ($podcasts as $p):
                $tieneAudio = !empty($p['archivo_audio']) && is_file(RUTA_PODCASTS_AUDIO . '/' . $p['archivo_audio']);
            ?>
            <div class="col-lg-3 col-sm-6 mt-sm-0 mt-5">
                <div class="area-box podcast-card<?= $tieneAudio ? '' : ' podcast-card--sin-audio' ?>"
                     data-id="<?= (int)$p['id'] ?>"
                     tabindex="0" role="button"
                     title="<?= $tieneAudio ? 'Reproducir' : 'Audio aún no disponible' ?>">
                    <img src="<?= BASE_URL ?>/assets/web/img/podcast.png" alt="">
                    <p><?= htmlspecialchars($p['titulo']) ?></p>
                    <?php if ($tieneAudio): ?>
                        <span class="podcast-play-icon"><span class="fa fa-play"></span></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($podcasts)): ?>
                <div class="col-12 text-center py-4"><p>Todavía no hay episodios cargados.</p></div>
            <?php endif; ?>
        </div>
        <div class="text-center">
            <a href="<?= BASE_URL ?>/paginas/podcast.php" class="btn btn-style btn-primary mt-md-5 mt-4">Ver todos</a>
        </div>
    </div>
</section>

<script>
    window.DDP_PODCASTS = <?= json_encode(array_map(function ($p) {
        $existe = !empty($p['archivo_audio']) && is_file(RUTA_PODCASTS_AUDIO . '/' . $p['archivo_audio']);
        return [
            'id'     => (int)$p['id'],
            'titulo' => $p['titulo'],
            'audio'  => $existe ? BASE_URL . '/podcasts_audio/' . rawurlencode($p['archivo_audio']) : null,
        ];
    }, $podcasts), JSON_UNESCAPED_UNICODE) ?>;
</script>

<!-- Especiales (carrusel) -->
<?php if (!empty($especiales)): ?>
<section class="w3l-team" id="team">
    <div class="teams1 py-5 mb-3">
        <div class="container py-lg-3 pb-lg-5 pb-4">
            <h3 class="title-big text-center mb-5">Especiales</h3>
            <div class="owl-carousel owl-especiales owl-theme text-center">
                <?php foreach ($especiales as $e): ?>
                <div class="item">
                    <div class="d-grid team-info">
                        <div class="column position-relative">
                            <a href="<?= htmlspecialchars($e['url_video']) ?>" class="especial-video">
                                <img src="<?= BASE_URL ?>/assets/web/img/<?= htmlspecialchars($e['imagen'] ?: 'placeholder.jpg') ?>" alt="" class="img-fluid rounded team-image">
                            </a>
                        </div>
                        <div class="column">
                            <p><?= htmlspecialchars($e['titulo']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Quiénes somos + video -->
<section class="w3l-banner py-0" id="work">
    <div class="midd-w3 py-lg-4 py-md-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mt-lg-0 mt-lg-5 about-right-faq align-self">
                    <h5 class="title-small mb-2">DDP Noticias</h5>
                    <h3 class="title-banner"><?= SITE_NAME ?></h3>
                    <p class="mt-4">Somos un espacio de periodismo independiente que busca visibilizar las acciones de diálogo en el país desde una mirada constructiva.</p>
                    <a href="<?= BASE_URL ?>/paginas/contacto.php" class="btn btn-style btn-primary mt-md-5 mt-4">Nosotros</a>
                </div>
                <div class="col-md-6 left-wthree-img mt-lg-0 mt-4">
                    <div class="position-relative">
                        <img src="<?= BASE_URL ?>/assets/web/img/bannerimg.jpg" alt="" class="img-fluid">
                        <a href="#small-dialog" class="popup-with-zoom-anim play-view text-center position-absolute">
                            <span class="video-play-icon"><span class="fa fa-play"></span></span>
                        </a>
                        <div id="small-dialog" class="zoom-anim-dialog mfp-hide">
                            <iframe src="https://www.youtube.com/embed/2jI6fHBtRJU" allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
