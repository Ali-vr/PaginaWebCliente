INSERT INTO usuarios (nombre, email, password_hash, rol, telefono, calle, localidad, provincia, codigo_postal, created_at)
VALUES (
  'Administrador',
  'admin@maderasartesanales.test',
  '$2y$10$/hS9h9aj9zvVn.6sgnLl8u22qy.ye6gX0guqQjKPCos95VNWbQIo2',
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
