-- ============================================================
-- Migración: estado de publicación para reportajes y noticias
-- Ejecuta esto UNA sola vez sobre tu base de datos ya existente
-- (no vuelve a crear las tablas, solo les agrega la columna).
-- ============================================================
USE revista_digital;

ALTER TABLE reportajes
    ADD COLUMN estado ENUM('borrador','publicado','oculto') NOT NULL DEFAULT 'publicado' AFTER es_destacado;

ALTER TABLE noticias
    ADD COLUMN estado ENUM('borrador','publicado','oculto') NOT NULL DEFAULT 'publicado' AFTER usuario_id;

-- Los reportajes/noticias que ya tenías quedan como "publicado" automáticamente.
