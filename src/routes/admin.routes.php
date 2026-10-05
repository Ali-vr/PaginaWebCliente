<?php

use App\Middleware\AdminMiddleware;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . "/../controllers/admin.controller.php";

$app->group("/admin", function (RouteCollectorProxy $admin) {
  $admin->get("", "show_admin_dashboard");

  $admin->get("/productos", "show_admin_productos");
  $admin->map(["GET", "POST"], "/productos/create", "handle_admin_producto_create");
  $admin->map(["GET", "POST"], "/productos/{id}/edit", "handle_admin_producto_edit");
  $admin->post("/productos/{id}/delete", "handle_admin_producto_delete");

  $admin->get("/categorias", "show_admin_categorias");
  $admin->map(["GET", "POST"], "/categorias/create", "handle_admin_categoria_create");
  $admin->map(["GET", "POST"], "/categorias/{id}/edit", "handle_admin_categoria_edit");
  $admin->post("/categorias/{id}/delete", "handle_admin_categoria_delete");

  $admin->get("/carrusel", "show_admin_carrusel");
  $admin->map(["GET", "POST"], "/carrusel/create", "handle_admin_carrusel_create");
  $admin->map(["GET", "POST"], "/carrusel/{id}/edit", "handle_admin_carrusel_edit");
  $admin->post("/carrusel/{id}/delete", "handle_admin_carrusel_delete");
})->add(new AdminMiddleware());
