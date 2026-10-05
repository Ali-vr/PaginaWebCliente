INSERT INTO usuarios (nombre, email, password_hash, rol, telefono, calle, localidad, provincia, codigo_postal, created_at)
VALUES (
  'Administrador',
  'admin@maderasartesanales.test',
  '$2y$10$2lq9Wtlk43JAyTE5c7cy9.26b5sQSEez9GQz4IGoNn1WJXj4pG6BG',
  'admin',
  '555-0100',
  'Calle Principal 123',
  'Ciudad',
  'Buenos Aires',
  '1000',
  NOW()
)
ON DUPLICATE KEY UPDATE
  nombre = VALUES(nombre),
  password_hash = VALUES(password_hash),
  rol = VALUES(rol),
  telefono = VALUES(telefono),
  calle = VALUES(calle),
  localidad = VALUES(localidad),
  provincia = VALUES(provincia),
  codigo_postal = VALUES(codigo_postal);
