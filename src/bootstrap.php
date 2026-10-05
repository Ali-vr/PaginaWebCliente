<?php

use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/database/database.php';
require_once __DIR__ . '/persistence/catalogo.persistence.php';
require_once __DIR__ . '/persistence/auth.persistence.php';
require_once __DIR__ . '/persistence/admin.persistence.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/AdminMiddleware.php';
require_once __DIR__ . '/utils/carrito.php';

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
  $categoriasDB = fetch_active_categorias();
  $productosDB = fetch_active_productos();
  $carruselDB = fetch_active_carrusel();

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