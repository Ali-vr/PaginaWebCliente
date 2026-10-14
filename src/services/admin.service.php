<?php

use Psr\Http\Message\UploadedFileInterface;

require_once __DIR__ . "/../persistence/admin.persistence.php";

function guardar_imagen_admin(
    ?UploadedFileInterface $archivo,
    string $imagenActual,
    string $coleccion,
    bool $obligatoria = false,
): string {
    if ($archivo === null || $archivo->getError() === UPLOAD_ERR_NO_FILE) {
        if ($obligatoria && $imagenActual === "") {
            throw new InvalidArgumentException("Seleccioná una imagen para continuar.");
        }

        return $imagenActual;
    }

    if ($archivo->getError() !== UPLOAD_ERR_OK) {
        if (in_array($archivo->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            throw new InvalidArgumentException("La imagen supera el tamaño máximo permitido.");
        }

        throw new RuntimeException("No se pudo recibir la imagen. Volvé a intentarlo.");
    }

    $limiteBytes = 5 * 1024 * 1024;
    if (($archivo->getSize() ?? 0) > $limiteBytes) {
        throw new InvalidArgumentException("La imagen no puede superar los 5 MB.");
    }

    $stream = $archivo->getStream();
    $contenido = $stream->getContents();
    if (strlen($contenido) > $limiteBytes) {
        throw new InvalidArgumentException("La imagen no puede superar los 5 MB.");
    }

    $extensiones = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp",
        "image/gif" => "gif",
    ];
    $dimensiones = getimagesizefromstring($contenido);
    $mime = is_array($dimensiones) ? ($dimensiones["mime"] ?? null) : null;
    if (
        !is_string($mime)
        || !isset($extensiones[$mime])
        || $dimensiones[0] > 10000
        || $dimensiones[1] > 10000
        || $dimensiones[0] * $dimensiones[1] > 40000000
    ) {
        throw new InvalidArgumentException("El archivo debe ser una imagen JPG, PNG, WebP o GIF válida.");
    }

    if (!in_array($coleccion, ["productos", "carrusel"], true)) {
        throw new LogicException("Colección de imágenes inválida.");
    }

    $directorio = dirname(__DIR__, 2) . "/public/uploads/" . $coleccion;
    if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
        throw new RuntimeException("No se pudo preparar el directorio para guardar imágenes.");
    }

    $nombre = bin2hex(random_bytes(16)) . "." . $extensiones[$mime];
    $rutaDestino = $directorio . "/" . $nombre;
    $archivo->moveTo($rutaDestino);

    return "/uploads/" . $coleccion . "/" . $nombre;
}

function eliminar_imagen_local_admin(string $ruta): void
{
    if (!preg_match('~^/uploads/(productos|carrusel)/[a-f0-9]{32}\.(jpg|png|webp|gif)$~', $ruta)) {
        return;
    }

    $rutaArchivo = dirname(__DIR__, 2) . "/public" . $ruta;
    if (is_file($rutaArchivo) && !unlink($rutaArchivo)) {
        throw new RuntimeException("No se pudo borrar el archivo de imagen {$ruta}.");
    }
}

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

function crear_producto_admin(array $datos, ?UploadedFileInterface $archivoImagen = null): void
{
    $validado = validar_producto_admin($datos);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException((string) $validado["error"]);
    }

    $imagen = guardar_imagen_admin($archivoImagen, $validado["imagen"], "productos");
    try {
        insert_product_admin([
            "categoria_id" => $validado["categoria_id"],
            "nombre" => $validado["nombre"],
            "descripcion" => $validado["descripcion"],
            "precio" => $validado["precio"],
            "stock" => $validado["stock"],
            "imagen" => $imagen,
            "material" => $validado["material"],
            "medidas" => $validado["medidas"],
        ]);
    } catch (Throwable $exception) {
        if ($imagen !== $validado["imagen"]) {
            eliminar_imagen_local_admin($imagen);
        }
        throw $exception;
    }
}

function obtener_producto_admin_por_id(int $id): ?array
{
    return fetch_product_by_id_admin($id);
}

function actualizar_producto_admin(int $id, array $datos, ?UploadedFileInterface $archivoImagen = null): void
{
    $validado = validar_producto_admin($datos);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException((string) $validado["error"]);
    }

    $productoActual = obtener_producto_admin_por_id($id);
    if ($productoActual === null) {
        throw new RuntimeException("No se encontró el producto que se quiere actualizar.");
    }

    $imagenActual = (string) ($productoActual["imagen"] ?? "");
    $imagen = guardar_imagen_admin($archivoImagen, $imagenActual, "productos");
    try {
        update_product_admin($id, [
            "categoria_id" => $validado["categoria_id"],
            "nombre" => $validado["nombre"],
            "descripcion" => $validado["descripcion"],
            "precio" => $validado["precio"],
            "stock" => $validado["stock"],
            "imagen" => $imagen,
            "material" => $validado["material"],
            "medidas" => $validado["medidas"],
        ]);
    } catch (Throwable $exception) {
        if ($imagen !== $imagenActual) {
            eliminar_imagen_local_admin($imagen);
        }
        throw $exception;
    }

    if ($imagen !== $imagenActual) {
        eliminar_imagen_local_admin($imagenActual);
    }
}

function eliminar_producto_admin(int $id): void
{
    soft_delete_product_admin($id);
}

function eliminar_producto_permanentemente_admin(int $id): bool
{
    $producto = obtener_producto_admin_por_id($id);
    if ($producto === null || !delete_product_if_unreferenced_admin($id)) {
        return false;
    }

    eliminar_imagen_local_admin((string) ($producto["imagen"] ?? ""));
    return true;
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

function eliminar_categoria_permanentemente_admin(int $id): bool
{
    return delete_categoria_if_without_products_admin($id);
}

function validar_carrusel_admin(array $datos, bool $imagenAdjunta = false): array
{
    $titulo = trim((string) ($datos["titulo"] ?? ""));
    $imagen = trim((string) ($datos["imagen"] ?? ""));

    if ($titulo === "" || ($imagen === "" && !$imagenAdjunta)) {
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

function crear_carrusel_admin(array $datos, ?UploadedFileInterface $archivoImagen = null): void
{
    $validado = validar_carrusel_admin($datos, $archivoImagen !== null);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException((string) $validado["error"]);
    }

    $imagen = guardar_imagen_admin($archivoImagen, $validado["imagen"], "carrusel", true);
    try {
        insert_carrusel_item_admin([
            "titulo" => $validado["titulo"],
            "descripcion" => $validado["descripcion"],
            "imagen" => $imagen,
            "link" => $validado["link"],
            "orden" => $validado["orden"],
        ]);
    } catch (Throwable $exception) {
        eliminar_imagen_local_admin($imagen);
        throw $exception;
    }
}

function obtener_carrusel_admin_por_id(int $id): ?array
{
    return fetch_carrusel_by_id_admin($id);
}

function actualizar_carrusel_admin(int $id, array $datos, ?UploadedFileInterface $archivoImagen = null): void
{
    $validado = validar_carrusel_admin($datos, $archivoImagen !== null);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException((string) $validado["error"]);
    }

    $itemActual = obtener_carrusel_admin_por_id($id);
    if ($itemActual === null) {
        throw new RuntimeException("No se encontró el elemento del carrusel que se quiere actualizar.");
    }

    $imagenActual = (string) ($itemActual["imagen"] ?? "");
    $imagen = guardar_imagen_admin($archivoImagen, $imagenActual, "carrusel", true);
    try {
        update_carrusel_item_admin($id, [
            "titulo" => $validado["titulo"],
            "descripcion" => $validado["descripcion"],
            "imagen" => $imagen,
            "link" => $validado["link"],
            "orden" => $validado["orden"],
        ]);
    } catch (Throwable $exception) {
        if ($imagen !== $imagenActual) {
            eliminar_imagen_local_admin($imagen);
        }
        throw $exception;
    }

    if ($imagen !== $imagenActual) {
        eliminar_imagen_local_admin($imagenActual);
    }
}

function eliminar_carrusel_admin(int $id): void
{
    soft_delete_carrusel_item_admin($id);
}
