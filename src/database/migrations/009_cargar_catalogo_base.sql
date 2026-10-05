INSERT INTO categorias (id, nombre, slug, activo)
VALUES
  (1, 'Mesas', 'mesas', 1),
  (2, 'Lámparas', 'lamparas', 1),
  (3, 'Estantes', 'estantes', 1),
  (4, 'Comedores', 'comedores', 1),
  (5, 'Carteles', 'carteles', 1),
  (6, 'Mostradores', 'mostradores', 1)
ON DUPLICATE KEY UPDATE
  nombre = VALUES(nombre),
  slug = VALUES(slug),
  activo = VALUES(activo);

INSERT INTO productos (id, categoria_id, nombre, descripcion, precio, stock, imagen, material, medidas, activo)
VALUES
  (
    1,
    1,
    'Mesa ratona de roble',
    'Mesa ratona maciza de roble, terminación natural al aceite. Ideal para living, combina con cualquier estilo de sillón.',
    85000.00,
    1,
    'https://placehold.co/600x450/8b5a2b/f6efe4?text=Mesa+ratona',
    'Roble macizo',
    '100 x 55 x 40 cm',
    1
  ),
  (
    2,
    2,
    'Lámpara colgante de pino',
    'Lámpara colgante torneada a mano en pino, pantalla de lino natural. Pensada para mesas de comedor o rincones de lectura.',
    32000.00,
    1,
    'https://placehold.co/600x450/8b5a2b/f6efe4?text=Lampara',
    'Pino + pantalla de lino',
    'Ø 30 x 25 cm',
    1
  ),
  (
    3,
    3,
    'Estante flotante',
    'Estante flotante con fijación oculta, ideal para libros o plantas. Cantos redondeados y terminación lisa.',
    21000.00,
    0,
    'https://placehold.co/600x450/8b5a2b/f6efe4?text=Estante',
    'Pinotea',
    '80 x 20 x 4 cm',
    1
  ),
  (
    4,
    4,
    'Mesa de comedor 6 personas',
    'Mesa de comedor para 6 personas, tapa de una sola pieza y patas torneadas. Hecha bajo pedido.',
    210000.00,
    1,
    'https://placehold.co/600x450/8b5a2b/f6efe4?text=Comedor',
    'Algarrobo macizo',
    '180 x 90 x 75 cm',
    1
  ),
  (
    5,
    5,
    'Cartel de madera personalizado',
    'Cartel grabado a fuego con el texto que elijas. Ideal para locales, casas o como regalo.',
    15000.00,
    1,
    'https://placehold.co/600x450/8b5a2b/f6efe4?text=Cartel',
    'Pino tratado',
    '40 x 20 cm',
    1
  ),
  (
    6,
    6,
    'Mostrador de recepción',
    'Mostrador a medida para locales comerciales, con espacio inferior para guardado.',
    320000.00,
    0,
    'https://placehold.co/600x450/8b5a2b/f6efe4?text=Mostrador',
    'MDF revestido en cedro',
    '150 x 60 x 110 cm',
    1
  )
ON DUPLICATE KEY UPDATE
  categoria_id = VALUES(categoria_id),
  nombre = VALUES(nombre),
  descripcion = VALUES(descripcion),
  precio = VALUES(precio),
  stock = VALUES(stock),
  imagen = VALUES(imagen),
  material = VALUES(material),
  medidas = VALUES(medidas),
  activo = VALUES(activo);

INSERT INTO carrusel (id, titulo, descripcion, imagen, link, orden, activo)
VALUES
  (
    1,
    'Hecho a mano, pieza por pieza',
    'Muebles de madera maciza, sin atajos.',
    'https://placehold.co/1200x400/3a2a1e/f6efe4?text=Hecho+a+mano',
    '#',
    1,
    1
  ),
  (
    2,
    'Comedores a medida',
    'Diseñamos según el espacio que tengas.',
    'https://placehold.co/1200x400/8b5a2b/f6efe4?text=Comedores',
    '#',
    2,
    1
  ),
  (
    3,
    'Envíos a todo el país',
    'Tu pedido, embalado con el mismo cuidado con el que lo hacemos.',
    'https://placehold.co/1200x400/d9b98c/2b231c?text=Envios',
    '#',
    3,
    1
  )
ON DUPLICATE KEY UPDATE
  titulo = VALUES(titulo),
  descripcion = VALUES(descripcion),
  imagen = VALUES(imagen),
  link = VALUES(link),
  orden = VALUES(orden),
  activo = VALUES(activo);
