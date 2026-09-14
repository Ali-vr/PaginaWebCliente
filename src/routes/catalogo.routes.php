<?php

$app->get("/", function ($request, $response) use ($render, $productos, $carrusel) {
  return $render($response, "index.php", [
    "productos" => $productos,
    "carrusel" => $carrusel,
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

  return $render($response, "categorias.php", [
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
