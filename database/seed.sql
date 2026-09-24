-- ============================================================
-- Datos de prueba para revista_digital
-- Ejecutar DESPUÉS de schema.sql
-- Usuarios de prueba (contraseña "admin123" para los tres):
--   admin@dialogoydesarrollo.com.pe   -> rol: admin
--   editor@ddp.pe                     -> rol: editor
--   redactor@ddp.pe                   -> rol: redactor
-- ============================================================
USE revista_digital;
SET NAMES utf8mb4;

INSERT INTO usuarios (nombres, ap_paterno, ap_materno, email, password_hash, rol) VALUES
('Admin', 'Principal', NULL, 'admin@dialogoydesarrollo.com.pe', '$2y$10$EcsaXzIE3ziv5pdMkrE1.O.d1F76IRxBGka2CcVThJlKMFti5XOxK', 'admin'),
('Elena', 'Editora', NULL, 'editor@ddp.pe', '$2y$10$EcsaXzIE3ziv5pdMkrE1.O.d1F76IRxBGka2CcVThJlKMFti5XOxK', 'editor'),
('Rita', 'Redactora', NULL, 'redactor@ddp.pe', '$2y$10$EcsaXzIE3ziv5pdMkrE1.O.d1F76IRxBGka2CcVThJlKMFti5XOxK', 'redactor');

INSERT INTO autores (nombres, ap_paterno, ap_materno, nickname, es_nickname) VALUES
('María', 'Gonzales', 'Ruiz', NULL, 0),
('Redacción', NULL, NULL, 'Redacción DDP', 1);

INSERT INTO reportajes (titulo, resumen_corto, desarrollo, foto_principal, pdf_adjunto, fecha_publicacion, es_destacado, autor_id, usuario_id) VALUES
('Más de 730 mineros con Reinfo vigente o suspendido participan en las elecciones regionales y municipales',
 '43 candidatos buscan llegar a gobiernos regionales y 692 postulan a alcaldías y regidurías.',
 'Contenido completo del reportaje pendiente de redacción. Reemplaza este texto con la nota real proporcionada por tu equipo de redacción.',
 'video.jpg', NULL, '2026-08-28', 1, 1, 1),
('Quiruvilca: el pueblo perforado por la minería ilegal',
 'Un recorrido por los efectos de la minería informal en la localidad de Quiruvilca.',
 'Contenido completo del reportaje pendiente de redacción.',
 'reportaje-18-08-26.jpg', NULL, '2026-08-18', 0, 2, 1),
('Cómo evitar que el canon del boom minero termine en obras de poco impacto',
 'Un análisis sobre el uso eficiente del canon minero en los gobiernos locales.',
 'Contenido completo del reportaje pendiente de redacción.',
 'reportaje-12-08-26.jpg', NULL, '2026-08-12', 0, 1, 1),
('Minería ilegal: la brecha sigue abierta a una semana del nuevo gobierno',
 'Balance de los primeros días del nuevo gobierno frente al problema de la minería ilegal.',
 'Contenido completo del reportaje pendiente de redacción.',
 'reportaje-05-08-26.jpg', NULL, '2026-08-05', 1, 2, 1);

INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden, descripcion) VALUES
(2, 'reportaje-18-08-26.jpg', 1, 'Vista general de la zona afectada');

INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id) VALUES
('Impulsan talento local en Hualgayoc', 'nota-facebook-21-11-25.png', 'https://minart.pe/', '2025-11-21', 1),
('Inauguran moderno colegio en Cerro Azul', 'nota-facebook-21-11-25b.png', 'https://andina.pe/', '2025-11-21', 1),
('Megaproyecto de saneamiento en Juliaca', 'nota-facebook-20-11-25.png', 'https://diarioelnoticiero.com/', '2025-11-20', 1);

INSERT INTO boletines (numero_boletin, resumen, foto_portada, archivo_pdf, fecha_publicacion, usuario_id) VALUES
('45', '-Promueven megaproyectos turísticos por S/ 2,400 mllns.\n-Invertirán S/ 9 millones en zonas rurales de Cusco.\n-Producción láctea se duplica en Cajamarca.',
 'boletin-ntep-45.png', 'boletin-NTEP-edicion-N45-2808.pdf', '2026-08-28', 1);

-- Estos dos episodios de ejemplo quedan sin archivo de audio (archivo_audio = NULL).
-- Súbelos desde admin/podcasts para que se puedan reproducir en la web.
INSERT INTO podcasts (titulo, archivo_audio, fecha_publicacion, usuario_id) VALUES
('Aumentan casos de hackeo de WhatsApp y delitos informáticos en el país', NULL, '2026-08-20', 1),
('Ministerio Público exige mayor presupuesto para la lucha contra las extorsiones', NULL, '2026-08-15', 1);

INSERT INTO videos (titulo, url_embed, fecha_publicacion, usuario_id) VALUES
('Por una minería artesanal segura para todos', 'https://www.youtube.com/embed/2jI6fHBtRJU', '2026-08-10', 1);

INSERT INTO especiales (titulo, imagen, url_video, orden, usuario_id) VALUES
('Por una minería artesanal segura para todos', 'placeholder.jpg', 'https://www.youtube.com/watch?v=2jI6fHBtRJU', 1, 1),
('REINFO: días decisivos en el Congreso', 'placeholder.jpg', 'https://www.youtube.com/watch?v=2jI6fHBtRJU', 2, 1);
