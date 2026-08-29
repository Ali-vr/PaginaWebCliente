<?php

use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

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

// Wrapper sobre view(): inyecta $categorias y $cantidadCarrito en todas las páginas
// base.php los usa directamente inline (navbar + subnav + footer)
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

// ─── CATÁLOGO ────────────────────────────────────────────────────────────────

$app->get("/", function ($request, $response) use ($render, $productos) {
  return $render($response, "index.php", [
    "productos" => $productos,
  ]);
});

$app->get("/categoria/{slug}", function ($request, $response, array $args) use ($render, $categorias, $productos) {
  $slug = $args["slug"];

  if (!array_key_exists($slug, $categorias)) {
    return $render($response->withStatus(404), "404.php", [
      "title" => "Página no encontrada",
    ]);
  }

  $productosFiltrados = array_values(array_filter(
    $productos,
    fn(array $p): bool => $p["categoria"] === $slug,
  ));

  return $render($response, "categoria.php", [
    "categoriaSlug"   => $slug,
    "categoriaNombre" => $categorias[$slug],
    "productos"       => $productosFiltrados,
    "title"           => $categorias[$slug] . " | Maderas Artesanales",
  ]);
});

$app->get("/producto/{id}", function ($request, $response, array $args) use ($render, $productos) {
  $id = (int) $args["id"];

  $producto = null;
  foreach ($productos as $p) {
    if ($p["id"] === $id) {
      $producto = $p;
      break;
    }
  }

  if ($producto === null) {
    return $render($response->withStatus(404), "404.php", [
      "title" => "Producto no encontrado",
    ]);
  }

  $relacionados = array_values(array_filter(
    $productos,
    fn(array $p): bool => $p["categoria"] === $producto["categoria"] && $p["id"] !== $producto["id"],
  ));

  return $render($response, "producto.php", [
    "producto"     => $producto,
    "relacionados" => array_slice($relacionados, 0, 4),
    "categoriaSlug" => $producto["categoria"],
    "urlActual"    => (string) $request->getUri(),
    "title"        => $producto["nombre"] . " | Maderas Artesanales",
  ]);
});

// ─── BUSCADOR ────────────────────────────────────────────────────────────────

$app->get("/buscar", function ($request, $response) use ($render, $productos) {
  $q = trim((string) ($request->getQueryParams()["q"] ?? ""));

  $resultados = [];
  if ($q !== "") {
    $resultados = array_values(array_filter(
      $productos,
      fn(array $p): bool => stripos($p["nombre"], $q) !== false,
    ));
  }

  return $render($response, "buscar.php", [
    "query"      => $q,
    "resultados" => $resultados,
    "title"      => $q !== "" ? "Búsqueda: $q | Maderas Artesanales" : "Buscar | Maderas Artesanales",
  ]);
});

// ─── CARRITO ─────────────────────────────────────────────────────────────────

$app->post("/carrito/agregar", function ($request, $response) use ($productos) {
  $datos      = (array) $request->getParsedBody();
  $productoId = (int) ($datos["producto_id"] ?? 0);
  $cantidad   = max(1, (int) ($datos["cantidad"] ?? 1));

  $existe = array_filter($productos, fn(array $p): bool => $p["id"] === $productoId);

  if (!empty($existe)) {
    carritoAgregar($productoId, $cantidad);
  }

  return $response->withHeader("Location", "/carrito")->withStatus(302);
});

$app->post("/carrito/eliminar/{id}", function ($request, $response, array $args) {
  carritoEliminar((int) $args["id"]);
  return $response->withHeader("Location", "/carrito")->withStatus(302);
});

$app->get("/carrito", function ($request, $response) use ($render, $productos) {
  $items = carritoObtenerItems($productos);
  return $render($response, "carrito/cesta.php", [
    "items" => $items,
    "total" => carritoTotal($items),
    "title" => "Carrito | Maderas Artesanales",
  ]);
});

$app->get("/carrito/pago", function ($request, $response) use ($render, $productos) {
  $items = carritoObtenerItems($productos);
  if (empty($items)) {
    return $response->withHeader("Location", "/carrito")->withStatus(302);
  }
  return $render($response, "carrito/pago.php", [
    "items" => $items,
    "total" => carritoTotal($items),
    "title" => "Pago | Maderas Artesanales",
  ]);
});

$app->post("/carrito/pago", function ($request, $response) use ($productos) {
  $items = carritoObtenerItems($productos);
  if (empty($items)) {
    return $response->withHeader("Location", "/carrito")->withStatus(302);
  }

  $datos = (array) $request->getParsedBody();

  $_SESSION["ultimo_pedido"] = [
    "items"       => $items,
    "total"       => carritoTotal($items),
    "metodoPago"  => $datos["metodo_pago"] ?? "tarjeta",
    "direccion"   => [
      "nombre"       => $datos["nombre"]        ?? "",
      "calle"        => $datos["calle"]          ?? "",
      "localidad"    => $datos["localidad"]      ?? "",
      "provincia"    => $datos["provincia"]      ?? "",
      "codigoPostal" => $datos["codigo_postal"]  ?? "",
      "telefono"     => $datos["telefono"]       ?? "",
    ],
    "numeroPedido" => strtoupper(substr(uniqid(), -6)),
  ];

  carritoVaciar();
  return $response->withHeader("Location", "/carrito/confirmacion")->withStatus(302);
});

$app->get("/carrito/confirmacion", function ($request, $response) use ($render) {
  $pedido = $_SESSION["ultimo_pedido"] ?? null;
  if ($pedido === null) {
    return $response->withHeader("Location", "/")->withStatus(302);
  }
  return $render($response, "carrito/confirmacion.php", [
    "pedido" => $pedido,
    "title"  => "¡Gracias por tu compra! | Maderas Artesanales",
  ]);
});

// ─── AUTENTICACIÓN (P6) ───────────────────────────────────────────────────────

$app->get("/login", function ($request, $response) use ($render) {
  // Si ya hay sesión activa, redirige directo a mi cuenta
  if (!empty($_SESSION["usuario_id"])) {
    return $response->withHeader("Location", "/mi-cuenta")->withStatus(302);
  }
  return $render($response, "auth/login.php", [
    "title" => "Iniciar sesión | Maderas Artesanales",
  ]);
});

$app->post("/login", function ($request, $response) use ($render) {
  // TODO: validar credenciales contra la tabla usuarios (DB)
  // Por ahora: placeholder que muestra error
  return $render($response, "auth/login.php", [
    "title" => "Iniciar sesión | Maderas Artesanales",
    "error" => "La base de datos aún no está conectada.",
  ]);
});

$app->get("/registro", function ($request, $response) use ($render) {
  if (!empty($_SESSION["usuario_id"])) {
    return $response->withHeader("Location", "/mi-cuenta")->withStatus(302);
  }
  return $render($response, "auth/registro.php", [
    "title" => "Crear cuenta | Maderas Artesanales",
  ]);
});

$app->post("/registro", function ($request, $response) use ($render) {
  // TODO: insertar usuario en DB con password_hash()
  return $render($response, "auth/registro.php", [
    "title" => "Crear cuenta | Maderas Artesanales",
    "error" => "La base de datos aún no está conectada.",
  ]);
});

$app->get("/logout", function ($request, $response) {
  session_destroy();
  return $response->withHeader("Location", "/")->withStatus(302);
});

$app->get("/mi-cuenta", function ($request, $response) use ($render) {
  if (empty($_SESSION["usuario_id"])) {
    return $response->withHeader("Location", "/login")->withStatus(302);
  }
  return $render($response, "auth/mi-cuenta.php", [
    "title" => "Mi cuenta | Maderas Artesanales",
  ]);
});

// ─── CONTACTO ────────────────────────────────────────────────────────────────

$app->get("/contacto", function ($request, $response) use ($render) {
  return $render($response, "contacto.php", [
    "title" => "Contacto | Maderas Artesanales",
  ]);
});

$app->addErrorMiddleware($debug, true, true);

return $app;