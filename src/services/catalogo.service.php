<?php

require_once __DIR__ . "/../persistence/catalogo.persistence.php";

function get_catalogo_context(): array
{
    return [
        "categorias" => fetch_active_categorias(),
        "productos" => fetch_active_productos(),
        "carrusel" => fetch_active_carrusel(),
    ];
}

function obtener_categoria_por_slug(array $categorias, string $slug): ?array
{
    foreach ($categorias as $categoria) {
        if (($categoria["slug"] ?? "") === $slug) {
            return $categoria;
        }
    }

    return null;
}

function obtener_productos_por_categoria(array $productos, string $slug): array
{
    return array_values(array_filter(
        $productos,
        static fn (array $producto): bool => ($producto["categoria"] ?? "") === $slug,
    ));
}

function obtener_producto_por_id(array $productos, int $id): ?array
{
    foreach ($productos as $producto) {
        if ((int) ($producto["id"] ?? 0) === $id) {
            return $producto;
        }
    }

    return null;
}

function obtener_productos_relacionados(array $productos, array $producto): array
{
    $categoria = (string) ($producto["categoria"] ?? "");

    $relacionados = array_values(array_filter(
        $productos,
        static fn (array $item): bool => ($item["categoria"] ?? "") === $categoria && (int) ($item["id"] ?? 0) !== (int) ($producto["id"] ?? 0),
    ));

    return array_slice($relacionados, 0, 4);
}

function buscar_productos_por_nombre(array $productos, string $query): array
{
    if ($query === "") {
        return [];
    }

    return array_values(array_filter(
        $productos,
        static fn (array $producto): bool => stripos((string) ($producto["nombre"] ?? ""), $query) !== false,
    ));
}
