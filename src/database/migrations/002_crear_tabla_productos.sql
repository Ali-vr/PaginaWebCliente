CREATE TABLE IF NOT EXISTS productos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT UNSIGNED NOT NULL,
  nombre VARCHAR(200) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  stock TINYINT(1) NOT NULL DEFAULT 1,
  imagen VARCHAR(500),
  material VARCHAR(150),
  medidas VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO productos (id, categoria_id, nombre, descripcion, precio, stock, imagen, material, medidas) VALUES
(1, 1, 'Mesa ratona de roble', 'Mesa ratona maciza de roble, terminación natural al aceite. Ideal para living, combina con cualquier estilo de sillón.', 85000.00, 1, 'https://placehold.co/600x450/8b5a2b/f6efe4?text=Mesa+ratona', 'Roble macizo', '100 x 55 x 40 cm'),
(2, 2, 'Lámpara colgante de pino', 'Lámpara colgante torneada a mano en pino, pantalla de lino natural. Pensada para mesas de comedor o rincones de lectura.', 32000.00, 1, 'https://placehold.co/600x450/8b5a2b/f6efe4?text=Lampara', 'Pino + pantalla de lino', 'Ø 30 x 25 cm'),
(3, 3, 'Estante flotante', 'Estante flotante con fijación oculta, ideal para libros o plantas. Cantos redondeados y terminación lisa.', 21000.00, 0, 'https://placehold.co/600x450/8b5a2b/f6efe4?text=Estante', 'Pinotea', '80 x 20 x 4 cm'),
(4, 4, 'Mesa de comedor 6 personas', 'Mesa de comedor para 6 personas, tapa de una sola pieza y patas torneadas. Hecha bajo pedido.', 210000.00, 1, 'https://placehold.co/600x450/8b5a2b/f6efe4?text=Comedor', 'Algarrobo macizo', '180 x 90 x 75 cm'),
(5, 5, 'Cartel de madera personalizado', 'Cartel grabado a fuego con el texto que elijas. Ideal para locales, casas o como regalo.', 15000.00, 1, 'https://placehold.co/600x450/8b5a2b/f6efe4?text=Cartel', 'Pino tratado', '40 x 20 cm'),
(6, 6, 'Mostrador de recepción', 'Mostrador a medida para locales comerciales, con espacio inferior para guardado.', 320000.00, 0, 'https://placehold.co/600x450/8b5a2b/f6efe4?text=Mostrador', 'MDF revestido en cedro', '150 x 60 x 110 cm');
