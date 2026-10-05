<?php

function fetch_active_categorias(): array
{
    try {
        return getDB()->query("SELECT slug, nombre FROM categorias WHERE activo = 1 ORDER BY nombre")->fetchAll();
    } catch (Throwable $exception) {
        return [];
    }
}

function fetch_active_productos(): array
{
    try {
        return getDB()->query("SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, p.imagen, p.material, p.medidas, c.slug AS categoria FROM productos p JOIN categorias c ON c.id = p.categoria_id WHERE p.activo = 1 AND c.activo = 1 ORDER BY p.id")->fetchAll();
    } catch (Throwable $exception) {
        return [];
    }
}

function fetch_active_carrusel(): array
{
    try {
        return getDB()->query("SELECT titulo, descripcion, imagen, link, orden FROM carrusel WHERE activo = 1 ORDER BY orden, id")->fetchAll();
    } catch (Throwable $exception) {
        return [];
    }
}
