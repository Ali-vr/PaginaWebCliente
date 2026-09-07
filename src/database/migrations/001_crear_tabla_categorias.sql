CREATE TABLE IF NOT EXISTS categorias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(80) NOT NULL UNIQUE,
  nombre VARCHAR(120) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO categorias (slug, nombre) VALUES
('mesas', 'Mesas'),
('lamparas', 'Lámparas'),
('estantes', 'Estantes'),
('comedores', 'Comedores'),
('carteles', 'Carteles'),
('mostradores', 'Mostradores');
