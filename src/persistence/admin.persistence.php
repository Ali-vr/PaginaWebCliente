<?php

function fetch_admin_counts(): array
{
    $db = getDB();

    return [
        "productos" => (int) $db->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
        "categorias" => (int) $db->query("SELECT COUNT(*) FROM categorias")->fetchColumn(),
        "carrusel" => (int) $db->query("SELECT COUNT(*) FROM carrusel")->fetchColumn(),
        "pedidos" => (int) $db->query("SELECT COUNT(*) FROM pedidos")->fetchColumn(),
    ];
}

function fetch_admin_pedidos(): array
{
    $db = getDB();

    return $db->query(
        "SELECT p.*, u.nombre AS cliente, u.email, COUNT(pi.id) AS items_count, SUM(pi.cantidad) AS unidades_total FROM pedidos p LEFT JOIN usuarios u ON u.id = p.usuario_id LEFT JOIN pedido_items pi ON pi.pedido_id = p.id GROUP BY p.id ORDER BY p.created_at DESC"
    )->fetchAll();
}

function update_pedido_estado_admin(int $id, string $estado): void
{
    $permitidos = ["pendiente", "confirmado", "en preparacion", "enviado", "entregado", "cancelado"];
    if (!in_array($estado, $permitidos, true)) {
        throw new InvalidArgumentException("Estado de pedido inválido.");
    }

    $stmt = getDB()->prepare("UPDATE pedidos SET estado = :estado WHERE id = :id");
    $stmt->execute(["id" => $id, "estado" => $estado]);
}

function fetch_pedidos_por_usuario(int $usuarioId): array
{
    $stmt = getDB()->prepare(
        "SELECT p.*, COUNT(pi.id) AS items_count, SUM(pi.cantidad) AS unidades_total FROM pedidos p LEFT JOIN pedido_items pi ON pi.pedido_id = p.id WHERE p.usuario_id = :usuario_id GROUP BY p.id ORDER BY p.created_at DESC"
    );
    $stmt->execute(["usuario_id" => $usuarioId]);

    return $stmt->fetchAll();
}

function fetch_admin_productos(): array
{
    $db = getDB();
    return $db->query("SELECT p.*, c.nombre AS categoria_nombre FROM productos p JOIN categorias c ON c.id = p.categoria_id ORDER BY p.id DESC")->fetchAll();
}

function fetch_admin_categorias(): array
{
    $db = getDB();
    return $db->query("SELECT c.*, COUNT(p.id) AS productos_count FROM categorias c LEFT JOIN productos p ON p.categoria_id = c.id GROUP BY c.id ORDER BY c.nombre")->fetchAll();
}

function fetch_admin_carrusel(): array
{
    $db = getDB();
    return $db->query("SELECT * FROM carrusel ORDER BY orden, id")->fetchAll();
}

function fetch_admin_categorias_select(): array
{
    $db = getDB();
    return $db->query("SELECT id, nombre FROM categorias WHERE activo = 1 ORDER BY nombre")->fetchAll();
}

function fetch_product_by_id_admin(int $id): ?array
{
    $stmt = getDB()->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->execute(["id" => $id]);
    $item = $stmt->fetch();
    return $item === false ? null : $item;
}

function insert_product_admin(array $data): void
{
    $stmt = getDB()->prepare("INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, activo, imagen, material, medidas) VALUES (:categoria_id, :nombre, :descripcion, :precio, :stock, 1, :imagen, :material, :medidas)");
    $stmt->execute([
        "categoria_id" => $data["categoria_id"],
        "nombre" => $data["nombre"],
        "descripcion" => $data["descripcion"],
        "precio" => $data["precio"],
        "stock" => $data["stock"],
        "imagen" => $data["imagen"],
        "material" => $data["material"],
        "medidas" => $data["medidas"],
    ]);
}

function update_product_admin(int $id, array $data): void
{
    $stmt = getDB()->prepare("UPDATE productos SET categoria_id = :categoria_id, nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock, imagen = :imagen, material = :material, medidas = :medidas WHERE id = :id");
    $stmt->execute([
        "id" => $id,
        "categoria_id" => $data["categoria_id"],
        "nombre" => $data["nombre"],
        "descripcion" => $data["descripcion"],
        "precio" => $data["precio"],
        "stock" => $data["stock"],
        "imagen" => $data["imagen"],
        "material" => $data["material"],
        "medidas" => $data["medidas"],
    ]);
}

function soft_delete_product_admin(int $id): void
{
    $stmt = getDB()->prepare("UPDATE productos SET activo = 0 WHERE id = :id");
    $stmt->execute(["id" => $id]);
}

function fetch_categoria_by_id_admin(int $id): ?array
{
    $stmt = getDB()->prepare("SELECT * FROM categorias WHERE id = :id");
    $stmt->execute(["id" => $id]);
    $item = $stmt->fetch();
    return $item === false ? null : $item;
}

function insert_categoria_admin(string $nombre, string $slug): void
{
    $stmt = getDB()->prepare("INSERT INTO categorias (nombre, slug, activo) VALUES (:nombre, :slug, 1)");
    $stmt->execute(["nombre" => $nombre, "slug" => $slug]);
}

function update_categoria_admin(int $id, string $nombre, string $slug): void
{
    $stmt = getDB()->prepare("UPDATE categorias SET nombre = :nombre, slug = :slug WHERE id = :id");
    $stmt->execute(["id" => $id, "nombre" => $nombre, "slug" => $slug]);
}

function count_productos_by_categoria_admin(int $id): int
{
    $stmt = getDB()->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = :id AND activo = 1");
    $stmt->execute(["id" => $id]);
    return (int) $stmt->fetchColumn();
}

function soft_delete_categoria_admin(int $id): void
{
    $stmt = getDB()->prepare("UPDATE categorias SET activo = 0 WHERE id = :id");
    $stmt->execute(["id" => $id]);
}

function fetch_carrusel_by_id_admin(int $id): ?array
{
    $stmt = getDB()->prepare("SELECT * FROM carrusel WHERE id = :id");
    $stmt->execute(["id" => $id]);
    $item = $stmt->fetch();
    return $item === false ? null : $item;
}

function insert_carrusel_item_admin(array $data): void
{
    $stmt = getDB()->prepare("INSERT INTO carrusel (titulo, descripcion, imagen, link, orden, activo) VALUES (:titulo, :descripcion, :imagen, :link, :orden, 1)");
    $stmt->execute([
        "titulo" => $data["titulo"],
        "descripcion" => $data["descripcion"],
        "imagen" => $data["imagen"],
        "link" => $data["link"],
        "orden" => $data["orden"],
    ]);
}

function update_carrusel_item_admin(int $id, array $data): void
{
    $stmt = getDB()->prepare("UPDATE carrusel SET titulo = :titulo, descripcion = :descripcion, imagen = :imagen, link = :link, orden = :orden WHERE id = :id");
    $stmt->execute([
        "id" => $id,
        "titulo" => $data["titulo"],
        "descripcion" => $data["descripcion"],
        "imagen" => $data["imagen"],
        "link" => $data["link"],
        "orden" => $data["orden"],
    ]);
}

function soft_delete_carrusel_item_admin(int $id): void
{
    $stmt = getDB()->prepare("UPDATE carrusel SET activo = 0 WHERE id = :id");
    $stmt->execute(["id" => $id]);
}
