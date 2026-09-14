<?php

use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/database/database.php';
require __DIR__ . '/middleware/AuthMiddleware.php';
require __DIR__ . '/middleware/AdminMiddleware.php';
require __DIR__ . '/utils/carrito.php';

Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

$env = $_ENV["APP_ENV"] ?? "prod";
$allowedEnvs = ["dev", "prod"];

if (!in_array($env, $allowedEnvs, true)) {
  throw new RuntimeException("APP_ENV inválido: $env");
}

$debug = $env === "dev";

session_start();

$app = AppFactory::create();

$renderer = new PhpRenderer(
  templatePath: __DIR__ . "/views",
  attributes: ["title" => "Maderas Artesanales | Tienda Online"],
);

$categorias = require __DIR__ . "/data/categorias.php";
$productos  = require __DIR__ . "/data/productos.php";
$carrusel = [
  ["titulo" => "Hecho a mano, pieza por pieza", "descripcion" => "Muebles de madera maciza, sin atajos.", "imagen" => "https://placehold.co/1200x400/3a2a1e/f6efe4?text=Hecho+a+mano", "link" => "#", "orden" => 1],
  ["titulo" => "Comedores a medida", "descripcion" => "Diseñamos según el espacio que tengas.", "imagen" => "https://placehold.co/1200x400/8b5a2b/f6efe4?text=Comedores", "link" => "#", "orden" => 2],
  ["titulo" => "Envíos a todo el país", "descripcion" => "Tu pedido, embalado con el mismo cuidado con el que lo hacemos.", "imagen" => "https://placehold.co/1200x400/d9b98c/2b231c?text=Envios", "link" => "#", "orden" => 3],
];

try {
  $db = getDB();
  $categoriasDB = $db->query("SELECT slug, nombre FROM categorias WHERE activo = 1 ORDER BY nombre")->fetchAll();
  $productosDB = $db->query("SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, p.imagen, p.material, p.medidas, c.slug AS categoria FROM productos p JOIN categorias c ON c.id = p.categoria_id WHERE p.activo = 1 AND c.activo = 1 ORDER BY p.id")->fetchAll();
  $carruselDB = $db->query("SELECT titulo, descripcion, imagen, link, orden FROM carrusel WHERE activo = 1 ORDER BY orden, id")->fetchAll();

  if ($categoriasDB !== []) {
    $categorias = array_column($categoriasDB, "nombre", "slug");
  }
  if ($productosDB !== []) {
    $productos = $productosDB;
  }
  if ($carruselDB !== []) {
    $carrusel = $carruselDB;
  }
} catch (Throwable $exception) {
  // Permite que la tienda siga mostrando el catálogo estático hasta ejecutar las migraciones.
}

// Wrapper sobre view(): inyecta $categorias y $cantidadCarrito en todas las páginas.
// base.php los usa directamente inline en el navbar y el footer.
$render = function (
  ResponseInterface $response,
  string $template,
  array $data = [],
  ?string $layout = null,
) use ($renderer, $categorias): ResponseInterface {
  return view($renderer, $response, $template, [
    ...$data,
    "categorias"      => $categorias,
    "cantidadCarrito" => carritoCantidadTotal(),
  ], $layout);
};

// Rutas separadas por funcionalidad. Se cargan en el mismo alcance para
// reutilizar $app, $render, $categorias y $productos.
require __DIR__ . "/routes/catalogo.routes.php";
require __DIR__ . "/routes/buscador.routes.php";
require __DIR__ . "/routes/carrito.routes.php";
require __DIR__ . "/routes/auth.routes.php";
require __DIR__ . "/routes/contacto.routes.php";
require __DIR__ . "/routes/admin.routes.php";

$app->addErrorMiddleware($debug, true, true);

return $app;