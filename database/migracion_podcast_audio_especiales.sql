-- ============================================================
-- Migración: podcasts con archivo de audio + tabla "especiales"
-- Ejecuta esto UNA sola vez sobre tu base de datos ya existente
-- (no vuelve a crear las tablas que ya tenías, solo agrega lo que falta).
-- ============================================================
USE revista_digital;

-- 1) Los podcasts ahora se suben como archivo de audio (mp3, wav, ogg, m4a)
--    en vez de un link de embed. Se deja "url_embed" opcional por si
--    quieres conservar el histórico, pero ya no se usa desde el panel.
ALTER TABLE podcasts
    ADD COLUMN archivo_audio VARCHAR(255) NULL AFTER titulo,
    MODIFY COLUMN url_embed VARCHAR(500) NULL;

-- 2) Tabla que faltaba para el carrusel de "Especiales" del inicio.
--    Si ya la creaste manualmente, este CREATE TABLE no hará nada.
CREATE TABLE IF NOT EXISTS especiales (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo              VARCHAR(255) NOT NULL,
    imagen              VARCHAR(255) NOT NULL,
    url_video           VARCHAR(500) NOT NULL,
    orden               INT NOT NULL DEFAULT 0,
    usuario_id          INT UNSIGNED NOT NULL,
    CONSTRAINT fk_especiales_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_especiales_orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
