<?php

require_once __DIR__ . "/../services/catalogo.service.php";

function show_home($request, $response)
{
    global $render, $productos, $carrusel;

    return $render($response, "index.php", [
        "productos" => $productos,
        "carrusel" => $carrusel,
    ]);
}

function show_categoria($request, $response, array $args = [])
{
    global $render, $categorias, $productos;

    $slug = (string) ($args["slug"] ?? "");
    $categoria = obtener_categoria_por_slug($categorias, $slug);

    if ($categoria === null) {
        return $render($response->withStatus(404), "404.php", [
            "title" => "Página no encontrada",
        ]);
    }

    $productosFiltrados = obtener_productos_por_categoria($productos, $slug);

    return $render($response, "categorias.php", [
        "categoriaSlug" => $slug,
        "categoriaNombre" => $categoria["nombre"],
        "productos" => $productosFiltrados,
        "title" => $categoria["nombre"] . " | Maderas Artesanales",
    ]);
}

function show_producto($request, $response, array $args = [])
{
    global $render, $productos;

    $id = (int) ($args["id"] ?? 0);
    $producto = obtener_producto_por_id($productos, $id);

    if ($producto === null) {
        return $render($response->withStatus(404), "404.php", [
            "title" => "Producto no encontrado",
        ]);
    }

    $relacionados = obtener_productos_relacionados($productos, $producto);

    return $render($response, "producto.php", [
        "producto" => $producto,
        "relacionados" => $relacionados,
        "categoriaSlug" => $producto["categoria"],
        "urlActual" => (string) $request->getUri(),
        "title" => $producto["nombre"] . " | Maderas Artesanales",
    ]);
}
