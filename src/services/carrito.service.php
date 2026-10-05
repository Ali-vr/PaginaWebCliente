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

function crear_pedido_desde_carrito(array $items, array $datos, ?int $usuarioId = null): array
{
    if (empty($items)) {
        throw new InvalidArgumentException("El carrito está vacío.");
    }

    $db = getDB();
    $metodoPago = in_array(($datos["metodo_pago"] ?? "tarjeta"), ["tarjeta", "mercadopago"], true)
        ? ($datos["metodo_pago"] ?? "tarjeta")
        : "tarjeta";

    $numeroPedido = "PED-" . date("Ymd") . "-" . strtoupper(substr(md5((string) uniqid((string) microtime(true), true)), 0, 8));
    $total = total_carrito($items);

    $stmt = $db->prepare(
        "INSERT INTO pedidos (usuario_id, numero, total, metodo_pago, estado, nombre_envio, calle_envio, localidad_envio, provincia_envio, cp_envio, telefono_envio) VALUES (:usuario_id, :numero, :total, :metodo_pago, 'pendiente', :nombre_envio, :calle_envio, :localidad_envio, :provincia_envio, :cp_envio, :telefono_envio)"
    );
    $stmt->execute([
        "usuario_id" => $usuarioId,
        "numero" => $numeroPedido,
        "total" => number_format($total, 2, ".", ""),
        "metodo_pago" => $metodoPago,
        "nombre_envio" => trim((string) ($datos["nombre"] ?? "")),
        "calle_envio" => trim((string) ($datos["calle"] ?? "")),
        "localidad_envio" => trim((string) ($datos["localidad"] ?? "")),
        "provincia_envio" => trim((string) ($datos["provincia"] ?? "")),
        "cp_envio" => trim((string) ($datos["codigo_postal"] ?? "")),
        "telefono_envio" => trim((string) ($datos["telefono"] ?? "")),
    ]);

    $pedidoId = (int) $db->lastInsertId();

    foreach ($items as $item) {
        $producto = $item["producto"] ?? null;
        if (!is_array($producto)) {
            continue;
        }

        $itemStmt = $db->prepare(
            "INSERT INTO pedido_items (pedido_id, producto_id, nombre_snapshot, precio_snapshot, cantidad) VALUES (:pedido_id, :producto_id, :nombre_snapshot, :precio_snapshot, :cantidad)"
        );
        $itemStmt->execute([
            "pedido_id" => $pedidoId,
            "producto_id" => (int) ($producto["id"] ?? 0),
            "nombre_snapshot" => (string) ($producto["nombre"] ?? ""),
            "precio_snapshot" => number_format((float) ($producto["precio"] ?? 0), 2, ".", ""),
            "cantidad" => (int) ($item["cantidad"] ?? 1),
        ]);
    }

    $pedido = [
        "id" => $pedidoId,
        "items" => $items,
        "total" => $total,
        "metodoPago" => $metodoPago,
        "direccion" => [
            "nombre" => trim((string) ($datos["nombre"] ?? "")),
            "calle" => trim((string) ($datos["calle"] ?? "")),
            "localidad" => trim((string) ($datos["localidad"] ?? "")),
            "provincia" => trim((string) ($datos["provincia"] ?? "")),
            "codigoPostal" => trim((string) ($datos["codigo_postal"] ?? "")),
            "telefono" => trim((string) ($datos["telefono"] ?? "")),
        ],
        "numeroPedido" => $numeroPedido,
    ];

    $_SESSION["ultimo_pedido"] = $pedido;
    return $pedido;
}

function guardar_ultimo_pedido(array $items, array $datos): array
{
    $usuarioId = isset($_SESSION["usuario_id"]) ? (int) $_SESSION["usuario_id"] : null;
    return crear_pedido_desde_carrito($items, $datos, $usuarioId);
}

function obtener_ultimo_pedido(): ?array
{
    return $_SESSION["ultimo_pedido"] ?? null;
}
