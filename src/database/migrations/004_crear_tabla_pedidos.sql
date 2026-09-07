CREATE TABLE IF NOT EXISTS pedidos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED DEFAULT NULL,
  numero VARCHAR(20) NOT NULL UNIQUE,
  total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  metodo_pago ENUM('tarjeta','mercadopago') NOT NULL DEFAULT 'tarjeta',
  estado ENUM('pendiente','confirmado','enviado','cancelado') NOT NULL DEFAULT 'pendiente',
  nombre_envio VARCHAR(150) NOT NULL,
  calle_envio VARCHAR(200) NOT NULL,
  localidad_envio VARCHAR(100) NOT NULL,
  provincia_envio VARCHAR(100) NOT NULL,
  cp_envio VARCHAR(10) NOT NULL,
  telefono_envio VARCHAR(30) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
