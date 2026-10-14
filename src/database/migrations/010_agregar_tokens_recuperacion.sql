ALTER TABLE usuarios
  ADD COLUMN reset_token CHAR(64) DEFAULT NULL,
  ADD COLUMN reset_token_expires_at DATETIME DEFAULT NULL,
  ADD INDEX idx_usuarios_reset_token (reset_token);
