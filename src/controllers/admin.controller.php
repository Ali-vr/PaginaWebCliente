<?php

require_once __DIR__ . "/../services/admin.service.php";

function show_admin_dashboard($request, $response)
{
    global $render;

    $counts = obtener_estadisticas_admin();
    return $render($response, "admin/index.php", [
        "title" => "Panel administrador | Maderas Artesanales",
        "counts" => $counts,
    ]);
}

function show_admin_pedidos($request, $response)
{
    global $render;

    $pedidos = listar_pedidos_admin();
    return $render($response, "admin/pedidos/index.php", [
        "title" => "Pedidos | Administración",
        "pedidos" => $pedidos,
    ]);
}

function handle_admin_pedido_estado($request, $response, array $args = [])
{
    $id = (int) ($args["id"] ?? 0);
    $estado = trim((string) ($request->getParsedBody()["estado"] ?? ""));

    if ($id > 0 && $estado !== "") {
        actualizar_estado_pedido_admin($id, $estado);
    }

    return $response->withHeader("Location", "/admin/pedidos")->withStatus(302);
}

function show_admin_productos($request, $response)
{
    global $render;

    $productos = listar_productos_admin();
    return $render($response, "admin/productos/index.php", [
        "title" => "Productos | Administración",
        "productos" => $productos,
    ]);
}

function handle_admin_producto_create($request, $response)
{
    global $render;

    $categorias = obtener_categorias_formulario_admin();

    if ($request->getMethod() === "POST") {
        $datos = (array) $request->getParsedBody();
        $validado = validar_producto_admin($datos);

        if (!$validado["ok"]) {
            return $render($response->withStatus(422), "admin/form.php", [
                "title" => "Nuevo producto",
                "tipo" => "producto",
                "error" => $validado["error"],
                "item" => $validado["item"],
                "categoriasAdmin" => $categorias,
            ]);
        }

        crear_producto_admin($datos);
        return $response->withHeader("Location", "/admin/productos")->withStatus(302);
    }

    return $render($response, "admin/form.php", [
        "title" => "Nuevo producto",
        "tipo" => "producto",
        "item" => [],
        "categoriasAdmin" => $categorias,
    ]);
}

function handle_admin_producto_edit($request, $response, array $args = [])
{
    global $render;

    $id = (int) ($args["id"] ?? 0);
    $item = obtener_producto_admin_por_id($id);

    if (!$item) {
        return $response->withStatus(404);
    }

    $categorias = obtener_categorias_formulario_admin();

    if ($request->getMethod() === "POST") {
        $datos = (array) $request->getParsedBody();
        $validado = validar_producto_admin($datos);

        if (!$validado["ok"]) {
            return $render($response->withStatus(422), "admin/form.php", [
                "title" => "Editar producto",
                "tipo" => "producto",
                "error" => $validado["error"],
                "item" => array_merge($item, $datos),
                "categoriasAdmin" => $categorias,
            ]);
        }

        actualizar_producto_admin($id, $datos);
        return $response->withHeader("Location", "/admin/productos")->withStatus(302);
    }

    return $render($response, "admin/form.php", [
        "title" => "Editar producto",
        "tipo" => "producto",
        "item" => $item,
        "categoriasAdmin" => $categorias,
    ]);
}

function handle_admin_producto_delete($request, $response, array $args = [])
{
    eliminar_producto_admin((int) ($args["id"] ?? 0));
    return $response->withHeader("Location", "/admin/productos")->withStatus(302);
}

function show_admin_categorias($request, $response)
{
    global $render;

    $categorias = listar_categorias_admin();
    return $render($response, "admin/categorias/index.php", [
        "title" => "Categorías | Administración",
        "categoriasAdmin" => $categorias,
    ]);
}

function handle_admin_categoria_create($request, $response)
{
    global $render;

    if ($request->getMethod() === "POST") {
        $datos = (array) $request->getParsedBody();
        $validado = validar_categoria_admin($datos);

        if (!$validado["ok"]) {
            return $render($response->withStatus(422), "admin/form.php", [
                "title" => "Nueva categoría",
                "tipo" => "categoria",
                "error" => $validado["error"],
                "item" => $datos,
            ]);
        }

        try {
            crear_categoria_admin($datos);
        } catch (PDOException $exception) {
            if ($exception->getCode() === "23000") {
                return $render($response->withStatus(422), "admin/form.php", [
                    "title" => "Nueva categoría",
                    "tipo" => "categoria",
                    "error" => "El slug ya existe.",
                    "item" => $datos,
                ]);
            }
            throw $exception;
        }

        return $response->withHeader("Location", "/admin/categorias")->withStatus(302);
    }

    return $render($response, "admin/form.php", [
        "title" => "Nueva categoría",
        "tipo" => "categoria",
        "item" => [],
    ]);
}

function handle_admin_categoria_edit($request, $response, array $args = [])
{
    global $render;

    $id = (int) ($args["id"] ?? 0);
    $item = obtener_categoria_admin_por_id($id);

    if (!$item) {
        return $response->withStatus(404);
    }

    if ($request->getMethod() === "POST") {
        $datos = (array) $request->getParsedBody();
        $validado = validar_categoria_admin($datos);

        if (!$validado["ok"]) {
            return $render($response->withStatus(422), "admin/form.php", [
                "title" => "Editar categoría",
                "tipo" => "categoria",
                "error" => $validado["error"],
                "item" => array_merge($item, $datos),
            ]);
        }

        actualizar_categoria_admin($id, $datos);
        return $response->withHeader("Location", "/admin/categorias")->withStatus(302);
    }

    return $render($response, "admin/form.php", [
        "title" => "Editar categoría",
        "tipo" => "categoria",
        "item" => $item,
    ]);
}

function handle_admin_categoria_delete($request, $response, array $args = [])
{
    $id = (int) ($args["id"] ?? 0);
    if (puede_eliminar_categoria_admin($id)) {
        eliminar_categoria_admin($id);
    }

    return $response->withHeader("Location", "/admin/categorias")->withStatus(302);
}

function show_admin_carrusel($request, $response)
{
    global $render;

    $items = listar_carrusel_admin();
    return $render($response, "admin/carrusel/index.php", [
        "title" => "Carrusel | Administración",
        "items" => $items,
    ]);
}

function handle_admin_carrusel_create($request, $response)
{
    global $render;

    if ($request->getMethod() === "POST") {
        $datos = (array) $request->getParsedBody();
        $validado = validar_carrusel_admin($datos);

        if (!$validado["ok"]) {
            return $render($response->withStatus(422), "admin/form.php", [
                "title" => "Nueva oferta",
                "tipo" => "carrusel",
                "error" => $validado["error"],
                "item" => $datos,
            ]);
        }

        crear_carrusel_admin($datos);
        return $response->withHeader("Location", "/admin/carrusel")->withStatus(302);
    }

    return $render($response, "admin/form.php", [
        "title" => "Nueva oferta",
        "tipo" => "carrusel",
        "item" => [],
    ]);
}

function handle_admin_carrusel_edit($request, $response, array $args = [])
{
    global $render;

    $id = (int) ($args["id"] ?? 0);
    $item = obtener_carrusel_admin_por_id($id);

    if (!$item) {
        return $response->withStatus(404);
    }

    if ($request->getMethod() === "POST") {
        $datos = (array) $request->getParsedBody();
        $validado = validar_carrusel_admin($datos);

        if (!$validado["ok"]) {
            return $render($response->withStatus(422), "admin/form.php", [
                "title" => "Editar oferta",
                "tipo" => "carrusel",
                "error" => $validado["error"],
                "item" => $datos,
            ]);
        }

        actualizar_carrusel_admin($id, $datos);
        return $response->withHeader("Location", "/admin/carrusel")->withStatus(302);
    }

    return $render($response, "admin/form.php", [
        "title" => "Editar oferta",
        "tipo" => "carrusel",
        "item" => $item,
    ]);
}

function handle_admin_carrusel_delete($request, $response, array $args = [])
{
    eliminar_carrusel_admin((int) ($args["id"] ?? 0));
    return $response->withHeader("Location", "/admin/carrusel")->withStatus(302);
}
