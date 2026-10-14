CREATE TABLE IF NOT EXISTS solicitudes_personalizacion (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  descripcion TEXT NOT NULL,
  archivo_nombre VARCHAR(255) NOT NULL,
  archivo_guardado VARCHAR(80) NOT NULL,
  archivo_mime VARCHAR(100) NOT NULL,
  estado ENUM('pendiente', 'aceptada', 'rechazada') NOT NULL DEFAULT 'pendiente',
  presupuesto DECIMAL(10,2) DEFAULT NULL,
  respuesta TEXT DEFAULT NULL,
  respondida_en DATETIME DEFAULT NULL,
  notificacion_enviada_en DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_solicitudes_personalizacion_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_solicitudes_personalizacion_estado (estado, created_at),
  INDEX idx_solicitudes_personalizacion_usuario (usuario_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
