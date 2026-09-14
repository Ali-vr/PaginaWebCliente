<?php

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
