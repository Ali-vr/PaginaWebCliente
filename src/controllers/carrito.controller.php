<?php

require_once __DIR__ . "/../services/carrito.service.php";
require_once __DIR__ . "/../services/catalogo.service.php";

function handle_carrito_agregar($request, $response)
{
    global $productos;

    $datos = (array) $request->getParsedBody();
    $productoId = (int) ($datos["producto_id"] ?? 0);
    $cantidad = max(1, (int) ($datos["cantidad"] ?? 1));

    if (obtener_producto_por_id($productos, $productoId) !== null) {
        agregar_producto_al_carrito($productoId, $cantidad);
    }

    return $response->withHeader("Location", "/carrito")->withStatus(302);
}

function handle_carrito_eliminar($request, $response, array $args = [])
{
    eliminar_producto_del_carrito((int) ($args["id"] ?? 0));
    return $response->withHeader("Location", "/carrito")->withStatus(302);
}

function show_carrito($request, $response)
{
    global $render, $productos;

    $items = obtener_items_del_carrito($productos);
    return $render($response, "carrito/cesta.php", [
        "items" => $items,
        "total" => total_carrito($items),
        "title" => "Carrito | Maderas Artesanales",
    ]);
}

function show_carrito_pago($request, $response)
{
    global $render, $productos;

    $items = obtener_items_del_carrito($productos);
    if (empty($items)) {
        return $response->withHeader("Location", "/carrito")->withStatus(302);
    }

    return $render($response, "carrito/pago.php", [
        "items" => $items,
        "total" => total_carrito($items),
        "title" => "Pago | Maderas Artesanales",
    ]);
}

function handle_carrito_pago($request, $response)
{
    global $productos;

    $items = obtener_items_del_carrito($productos);
    if (empty($items)) {
        return $response->withHeader("Location", "/carrito")->withStatus(302);
    }

    $datos = (array) $request->getParsedBody();
    guardar_ultimo_pedido($items, $datos);
    vaciar_carrito();

    return $response->withHeader("Location", "/carrito/confirmacion")->withStatus(302);
}

function show_confirmacion($request, $response)
{
    global $render;

    $pedido = obtener_ultimo_pedido();
    if ($pedido === null) {
        return $response->withHeader("Location", "/")->withStatus(302);
    }

    return $render($response, "carrito/confirmacion.php", [
        "pedido" => $pedido,
        "title" => "¡Gracias por tu compra! | Maderas Artesanales",
    ]);
}
