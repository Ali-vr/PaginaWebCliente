<?php

require_once __DIR__ . "/../persistence/admin.persistence.php";

function obtener_estadisticas_admin(): array
{
    return fetch_admin_counts();
}

function listar_pedidos_admin(): array
{
    return fetch_admin_pedidos();
}

function actualizar_estado_pedido_admin(int $id, string $estado): void
{
    update_pedido_estado_admin($id, $estado);
}

function listar_productos_admin(): array
{
    return fetch_admin_productos();
}

function listar_categorias_admin(): array
{
    return fetch_admin_categorias();
}

function listar_carrusel_admin(): array
{
    return fetch_admin_carrusel();
}

function obtener_categorias_formulario_admin(): array
{
    return fetch_admin_categorias_select();
}

function validar_producto_admin(array $datos): array
{
    $nombre = trim((string) ($datos["nombre"] ?? ""));
    $precio = filter_var($datos["precio"] ?? null, FILTER_VALIDATE_FLOAT);
    $categoriaId = (int) ($datos["categoria_id"] ?? 0);

    if ($nombre === "" || $precio === false || $precio < 0 || $categoriaId < 1) {
        return [
            "ok" => false,
            "error" => "Completá correctamente los campos obligatorios.",
            "item" => $datos,
        ];
    }

    return [
        "ok" => true,
        "nombre" => $nombre,
        "descripcion" => trim((string) ($datos["descripcion"] ?? "")),
        "precio" => $precio,
        "stock" => !empty($datos["stock"]) ? 1 : 0,
        "imagen" => trim((string) ($datos["imagen"] ?? "")),
        "material" => trim((string) ($datos["material"] ?? "")),
        "medidas" => trim((string) ($datos["medidas"] ?? "")),
        "categoria_id" => $categoriaId,
    ];
}

function crear_producto_admin(array $datos): void
{
    $validado = validar_producto_admin($datos);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException((string) $validado["error"]);
    }

    insert_product_admin([
        "categoria_id" => $validado["categoria_id"],
        "nombre" => $validado["nombre"],
        "descripcion" => $validado["descripcion"],
        "precio" => $validado["precio"],
        "stock" => $validado["stock"],
        "imagen" => $validado["imagen"],
        "material" => $validado["material"],
        "medidas" => $validado["medidas"],
    ]);
}

function obtener_producto_admin_por_id(int $id): ?array
{
    return fetch_product_by_id_admin($id);
}

function actualizar_producto_admin(int $id, array $datos): void
{
    $validado = validar_producto_admin($datos);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException((string) $validado["error"]);
    }

    update_product_admin($id, [
        "categoria_id" => $validado["categoria_id"],
        "nombre" => $validado["nombre"],
        "descripcion" => $validado["descripcion"],
        "precio" => $validado["precio"],
        "stock" => $validado["stock"],
        "imagen" => $validado["imagen"],
        "material" => $validado["material"],
        "medidas" => $validado["medidas"],
    ]);
}

function eliminar_producto_admin(int $id): void
{
    soft_delete_product_admin($id);
}

function normalizar_slug_categoria(string $slug): string
{
    $slug = strtolower(trim($slug));
    $slug = $slug !== "" ? preg_replace('/[^a-z0-9-]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $slug)) : "";
    return trim((string) $slug, "-");
}

function validar_categoria_admin(array $datos): array
{
    $nombre = trim((string) ($datos["nombre"] ?? ""));
    $slug = normalizar_slug_categoria((string) ($datos["slug"] ?? ""));

    if ($nombre === "" || $slug === "") {
        return [
            "ok" => false,
            "error" => "El nombre y el slug son obligatorios.",
            "item" => $datos,
        ];
    }

    return [
        "ok" => true,
        "nombre" => $nombre,
        "slug" => $slug,
    ];
}

function crear_categoria_admin(array $datos): void
{
    $validado = validar_categoria_admin($datos);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException((string) $validado["error"]);
    }

    insert_categoria_admin($validado["nombre"], $validado["slug"]);
}

function obtener_categoria_admin_por_id(int $id): ?array
{
    return fetch_categoria_by_id_admin($id);
}

function actualizar_categoria_admin(int $id, array $datos): void
{
    $validado = validar_categoria_admin($datos);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException((string) $validado["error"]);
    }

    update_categoria_admin($id, $validado["nombre"], $validado["slug"]);
}

function puede_eliminar_categoria_admin(int $id): bool
{
    return count_productos_by_categoria_admin($id) === 0;
}

function eliminar_categoria_admin(int $id): void
{
    soft_delete_categoria_admin($id);
}

function validar_carrusel_admin(array $datos): array
{
    $titulo = trim((string) ($datos["titulo"] ?? ""));
    $imagen = trim((string) ($datos["imagen"] ?? ""));

    if ($titulo === "" || $imagen === "") {
        return [
            "ok" => false,
            "error" => "El título y la imagen son obligatorios.",
            "item" => $datos,
        ];
    }

    return [
        "ok" => true,
        "titulo" => $titulo,
        "descripcion" => trim((string) ($datos["descripcion"] ?? "")),
        "imagen" => $imagen,
        "link" => trim((string) ($datos["link"] ?? "")),
        "orden" => (int) ($datos["orden"] ?? 0),
    ];
}

function crear_carrusel_admin(array $datos): void
{
    $validado = validar_carrusel_admin($datos);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException((string) $validado["error"]);
    }

    insert_carrusel_item_admin([
        "titulo" => $validado["titulo"],
        "descripcion" => $validado["descripcion"],
        "imagen" => $validado["imagen"],
        "link" => $validado["link"],
        "orden" => $validado["orden"],
    ]);
}

function obtener_carrusel_admin_por_id(int $id): ?array
{
    return fetch_carrusel_by_id_admin($id);
}

function actualizar_carrusel_admin(int $id, array $datos): void
{
    $validado = validar_carrusel_admin($datos);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException((string) $validado["error"]);
    }

    update_carrusel_item_admin($id, [
        "titulo" => $validado["titulo"],
        "descripcion" => $validado["descripcion"],
        "imagen" => $validado["imagen"],
        "link" => $validado["link"],
        "orden" => $validado["orden"],
    ]);
}

function eliminar_carrusel_admin(int $id): void
{
    soft_delete_carrusel_item_admin($id);
}
