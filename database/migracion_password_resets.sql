-- ============================================================
-- Migración: tabla para "olvidé mi contraseña" en el panel admin
-- Ejecuta esto UNA sola vez sobre tu base de datos ya existente
-- (no toca las tablas que ya tienes, solo agrega una nueva).
-- ============================================================
USE revista_digital;

CREATE TABLE IF NOT EXISTS password_resets (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id      INT UNSIGNED NOT NULL,
    token_hash      VARCHAR(64) NOT NULL,
    expira_en       DATETIME NOT NULL,
    usado           TINYINT(1) NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_password_resets_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_password_resets_token (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
