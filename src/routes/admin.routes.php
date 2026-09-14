<?php

use App\Middleware\AdminMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->group("/admin", function (RouteCollectorProxy $admin) use ($render) {
  $admin->get("", function ($request, $response) use ($render) {
    $db = getDB();
    $counts = [
      "productos" => (int) $db->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
      "categorias" => (int) $db->query("SELECT COUNT(*) FROM categorias")->fetchColumn(),
      "carrusel" => (int) $db->query("SELECT COUNT(*) FROM carrusel")->fetchColumn(),
    ];
    return $render($response, "admin/index.php", ["title" => "Panel administrador | Maderas Artesanales", "counts" => $counts]);
  });

  $admin->get("/productos", function ($request, $response) use ($render) {
    $productos = getDB()->query("SELECT p.*, c.nombre AS categoria_nombre FROM productos p JOIN categorias c ON c.id = p.categoria_id ORDER BY p.id DESC")->fetchAll();
    return $render($response, "admin/productos/index.php", ["title" => "Productos | Administración", "productos" => $productos]);
  });

  $admin->map(["GET", "POST"], "/productos/create", function ($request, $response) use ($render) {
    $db = getDB();
    $categorias = $db->query("SELECT id, nombre FROM categorias WHERE activo = 1 ORDER BY nombre")->fetchAll();
    if ($request->getMethod() === "POST") {
      $datos = (array) $request->getParsedBody();
      $nombre = trim((string) ($datos["nombre"] ?? ""));
      $precio = filter_var($datos["precio"] ?? null, FILTER_VALIDATE_FLOAT);
      $categoriaId = (int) ($datos["categoria_id"] ?? 0);
      if ($nombre === "" || $precio === false || $precio < 0 || $categoriaId < 1) {
        return $render($response->withStatus(422), "admin/form.php", ["title" => "Nuevo producto", "tipo" => "producto", "error" => "Completá correctamente los campos obligatorios.", "item" => $datos, "categoriasAdmin" => $categorias]);
      }
      $stmt = $db->prepare("INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, activo, imagen, material, medidas) VALUES (:categoria_id, :nombre, :descripcion, :precio, :stock, 1, :imagen, :material, :medidas)");
      $stmt->execute(["categoria_id" => $categoriaId, "nombre" => $nombre, "descripcion" => trim((string) ($datos["descripcion"] ?? "")), "precio" => $precio, "stock" => !empty($datos["stock"]) ? 1 : 0, "imagen" => trim((string) ($datos["imagen"] ?? "")), "material" => trim((string) ($datos["material"] ?? "")), "medidas" => trim((string) ($datos["medidas"] ?? ""))]);
      return $response->withHeader("Location", "/admin/productos")->withStatus(302);
    }
    return $render($response, "admin/form.php", ["title" => "Nuevo producto", "tipo" => "producto", "item" => [], "categoriasAdmin" => $categorias]);
  });

  $admin->map(["GET", "POST"], "/productos/{id}/edit", function ($request, $response, array $args) use ($render) {
    $db = getDB();
    $id = (int) $args["id"];
    $stmt = $db->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->execute(["id" => $id]);
    $item = $stmt->fetch();
    if (!$item) return $response->withStatus(404);
    $categorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre")->fetchAll();
    if ($request->getMethod() === "POST") {
      $datos = (array) $request->getParsedBody();
      $nombre = trim((string) ($datos["nombre"] ?? ""));
      $precio = filter_var($datos["precio"] ?? null, FILTER_VALIDATE_FLOAT);
      $categoriaId = (int) ($datos["categoria_id"] ?? 0);
      if ($nombre === "" || $precio === false || $precio < 0 || $categoriaId < 1) {
        return $render($response->withStatus(422), "admin/form.php", ["title" => "Editar producto", "tipo" => "producto", "error" => "Completá correctamente los campos obligatorios.", "item" => array_merge($item, $datos), "categoriasAdmin" => $categorias]);
      }
      $update = $db->prepare("UPDATE productos SET categoria_id = :categoria_id, nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock, imagen = :imagen, material = :material, medidas = :medidas WHERE id = :id");
      $update->execute(["id" => $id, "categoria_id" => $categoriaId, "nombre" => $nombre, "descripcion" => trim((string) ($datos["descripcion"] ?? "")), "precio" => $precio, "stock" => !empty($datos["stock"]) ? 1 : 0, "imagen" => trim((string) ($datos["imagen"] ?? "")), "material" => trim((string) ($datos["material"] ?? "")), "medidas" => trim((string) ($datos["medidas"] ?? ""))]);
      return $response->withHeader("Location", "/admin/productos")->withStatus(302);
    }
    return $render($response, "admin/form.php", ["title" => "Editar producto", "tipo" => "producto", "item" => $item, "categoriasAdmin" => $categorias]);
  });

  $admin->post("/productos/{id}/delete", function ($request, $response, array $args) {
    $stmt = getDB()->prepare("UPDATE productos SET activo = 0 WHERE id = :id");
    $stmt->execute(["id" => (int) $args["id"]]);
    return $response->withHeader("Location", "/admin/productos")->withStatus(302);
  });

  $admin->get("/categorias", function ($request, $response) use ($render) {
    $categorias = getDB()->query("SELECT c.*, COUNT(p.id) AS productos_count FROM categorias c LEFT JOIN productos p ON p.categoria_id = c.id GROUP BY c.id ORDER BY c.nombre")->fetchAll();
    return $render($response, "admin/categorias/index.php", ["title" => "Categorías | Administración", "categoriasAdmin" => $categorias]);
  });

  $admin->map(["GET", "POST"], "/categorias/create", function ($request, $response) use ($render) {
    $item = [];
    if ($request->getMethod() === "POST") {
      $datos = (array) $request->getParsedBody();
      $nombre = trim((string) ($datos["nombre"] ?? ""));
      $slug = strtolower(trim((string) ($datos["slug"] ?? "")));
      $slug = $slug !== "" ? preg_replace('/[^a-z0-9-]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $slug)) : "";
      $slug = trim((string) $slug, "-");
      if ($nombre === "" || $slug === "") return $render($response->withStatus(422), "admin/form.php", ["title" => "Nueva categoría", "tipo" => "categoria", "error" => "El nombre y el slug son obligatorios.", "item" => $datos]);
      try {
        $stmt = getDB()->prepare("INSERT INTO categorias (nombre, slug, activo) VALUES (:nombre, :slug, 1)");
        $stmt->execute(["nombre" => $nombre, "slug" => $slug]);
      } catch (PDOException $exception) {
        if ($exception->getCode() === "23000") return $render($response->withStatus(422), "admin/form.php", ["title" => "Nueva categoría", "tipo" => "categoria", "error" => "El slug ya existe.", "item" => $datos]);
        throw $exception;
      }
      return $response->withHeader("Location", "/admin/categorias")->withStatus(302);
    }
    return $render($response, "admin/form.php", ["title" => "Nueva categoría", "tipo" => "categoria", "item" => $item]);
  });

  $admin->map(["GET", "POST"], "/categorias/{id}/edit", function ($request, $response, array $args) use ($render) {
    $db = getDB();
    $id = (int) $args["id"];
    $stmt = $db->prepare("SELECT * FROM categorias WHERE id = :id");
    $stmt->execute(["id" => $id]);
    $item = $stmt->fetch();
    if (!$item) return $response->withStatus(404);
    if ($request->getMethod() === "POST") {
      $datos = (array) $request->getParsedBody();
      $nombre = trim((string) ($datos["nombre"] ?? ""));
      $slug = trim((string) ($datos["slug"] ?? ""));
      if ($nombre === "" || $slug === "") return $render($response->withStatus(422), "admin/form.php", ["title" => "Editar categoría", "tipo" => "categoria", "error" => "El nombre y el slug son obligatorios.", "item" => array_merge($item, $datos)]);
      $update = $db->prepare("UPDATE categorias SET nombre = :nombre, slug = :slug WHERE id = :id");
      $update->execute(["id" => $id, "nombre" => $nombre, "slug" => $slug]);
      return $response->withHeader("Location", "/admin/categorias")->withStatus(302);
    }
    return $render($response, "admin/form.php", ["title" => "Editar categoría", "tipo" => "categoria", "item" => $item]);
  });

  $admin->post("/categorias/{id}/delete", function ($request, $response, array $args) {
    $db = getDB();
    $check = $db->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = :id AND activo = 1");
    $check->execute(["id" => (int) $args["id"]]);
    if ((int) $check->fetchColumn() === 0) {
      $stmt = $db->prepare("UPDATE categorias SET activo = 0 WHERE id = :id");
      $stmt->execute(["id" => (int) $args["id"]]);
    }
    return $response->withHeader("Location", "/admin/categorias")->withStatus(302);
  });

  $admin->get("/carrusel", function ($request, $response) use ($render) {
    $items = getDB()->query("SELECT * FROM carrusel ORDER BY orden, id")->fetchAll();
    return $render($response, "admin/carrusel/index.php", ["title" => "Carrusel | Administración", "items" => $items]);
  });

  $admin->map(["GET", "POST"], "/carrusel/create", function ($request, $response) use ($render) {
    if ($request->getMethod() === "POST") {
      $datos = (array) $request->getParsedBody();
      if (trim((string) ($datos["titulo"] ?? "")) === "" || trim((string) ($datos["imagen"] ?? "")) === "") return $render($response->withStatus(422), "admin/form.php", ["title" => "Nueva oferta", "tipo" => "carrusel", "error" => "El título y la imagen son obligatorios.", "item" => $datos]);
      $stmt = getDB()->prepare("INSERT INTO carrusel (titulo, descripcion, imagen, link, orden, activo) VALUES (:titulo, :descripcion, :imagen, :link, :orden, 1)");
      $stmt->execute(["titulo" => trim($datos["titulo"]), "descripcion" => trim((string) ($datos["descripcion"] ?? "")), "imagen" => trim($datos["imagen"]), "link" => trim((string) ($datos["link"] ?? "")), "orden" => (int) ($datos["orden"] ?? 0)]);
      return $response->withHeader("Location", "/admin/carrusel")->withStatus(302);
    }
    return $render($response, "admin/form.php", ["title" => "Nueva oferta", "tipo" => "carrusel", "item" => []]);
  });

  $admin->map(["GET", "POST"], "/carrusel/{id}/edit", function ($request, $response, array $args) use ($render) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM carrusel WHERE id = :id");
    $stmt->execute(["id" => (int) $args["id"]]);
    $item = $stmt->fetch();
    if (!$item) return $response->withStatus(404);
    if ($request->getMethod() === "POST") {
      $datos = (array) $request->getParsedBody();
      $update = $db->prepare("UPDATE carrusel SET titulo = :titulo, descripcion = :descripcion, imagen = :imagen, link = :link, orden = :orden WHERE id = :id");
      $update->execute(["id" => (int) $args["id"], "titulo" => trim($datos["titulo"] ?? ""), "descripcion" => trim((string) ($datos["descripcion"] ?? "")), "imagen" => trim($datos["imagen"] ?? ""), "link" => trim((string) ($datos["link"] ?? "")), "orden" => (int) ($datos["orden"] ?? 0)]);
      return $response->withHeader("Location", "/admin/carrusel")->withStatus(302);
    }
    return $render($response, "admin/form.php", ["title" => "Editar oferta", "tipo" => "carrusel", "item" => $item]);
  });

  $admin->post("/carrusel/{id}/delete", function ($request, $response, array $args) {
    $stmt = getDB()->prepare("UPDATE carrusel SET activo = 0 WHERE id = :id");
    $stmt->execute(["id" => (int) $args["id"]]);
    return $response->withHeader("Location", "/admin/carrusel")->withStatus(302);
  });
})->add(new AdminMiddleware());
