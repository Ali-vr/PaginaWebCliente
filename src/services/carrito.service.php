<?php

function agregar_producto_al_carrito(int $productoId, int $cantidad = 1): void
{
    if (!isset($_SESSION["carrito"])) {
        $_SESSION["carrito"] = [];
    }

    if (isset($_SESSION["carrito"][$productoId])) {
        $_SESSION["carrito"][$productoId] += $cantidad;
    } else {
        $_SESSION["carrito"][$productoId] = $cantidad;
    }
}

function eliminar_producto_del_carrito(int $productoId): void
{
    unset($_SESSION["carrito"][$productoId]);
}

function vaciar_carrito(): void
{
    $_SESSION["carrito"] = [];
}

function obtener_items_del_carrito(array $productos): array
{
    $carrito = $_SESSION["carrito"] ?? [];
    $items = [];

    foreach ($carrito as $productoId => $cantidad) {
        foreach ($productos as $producto) {
            if ((int) ($producto["id"] ?? 0) === (int) $productoId) {
                $items[] = [
                    "producto" => $producto,
                    "cantidad" => $cantidad,
                    "subtotal" => (float) ($producto["precio"] ?? 0) * (int) $cantidad,
                ];
                break;
            }
        }
    }

    return $items;
}

function total_carrito(array $items): float
{
    return (float) array_sum(array_column($items, "subtotal"));
}

function cantidad_total_carrito(): int
{
    return (int) array_sum($_SESSION["carrito"] ?? []);
}

function guardar_ultimo_pedido(array $items, array $datos): void
{
    $_SESSION["ultimo_pedido"] = [
        "items" => $items,
        "total" => total_carrito($items),
        "metodoPago" => $datos["metodo_pago"] ?? "tarjeta",
        "direccion" => [
            "nombre" => $datos["nombre"] ?? "",
            "calle" => $datos["calle"] ?? "",
            "localidad" => $datos["localidad"] ?? "",
            "provincia" => $datos["provincia"] ?? "",
            "codigoPostal" => $datos["codigo_postal"] ?? "",
            "telefono" => $datos["telefono"] ?? "",
        ],
        "numeroPedido" => strtoupper(substr(uniqid(), -6)),
    ];
}

function obtener_ultimo_pedido(): ?array
{
    return $_SESSION["ultimo_pedido"] ?? null;
}
