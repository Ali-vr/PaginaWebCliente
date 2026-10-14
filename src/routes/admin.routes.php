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
  $admin->post("/productos/{id}/purge", "handle_admin_producto_permanent_delete");

  $admin->get("/categorias", "show_admin_categorias");
  $admin->map(["GET", "POST"], "/categorias/create", "handle_admin_categoria_create");
  $admin->map(["GET", "POST"], "/categorias/{id}/edit", "handle_admin_categoria_edit");
  $admin->post("/categorias/{id}/delete", "handle_admin_categoria_delete");
  $admin->post("/categorias/{id}/purge", "handle_admin_categoria_permanent_delete");

  $admin->get("/carrusel", "show_admin_carrusel");
  $admin->map(["GET", "POST"], "/carrusel/create", "handle_admin_carrusel_create");
  $admin->map(["GET", "POST"], "/carrusel/{id}/edit", "handle_admin_carrusel_edit");
  $admin->post("/carrusel/{id}/delete", "handle_admin_carrusel_delete");

  $admin->get("/pedidos", "show_admin_pedidos");
  $admin->post("/pedidos/{id}/estado", "handle_admin_pedido_estado");

  $admin->get("/personalizaciones", "show_admin_solicitudes_personalizacion");
  $admin->get("/personalizaciones/{id}", "show_admin_solicitud_personalizacion");
  $admin->get("/personalizaciones/{id}/archivo", "show_archivo_solicitud_personalizacion_admin");
  $admin->post("/personalizaciones/{id}/respuesta", "handle_respuesta_solicitud_personalizacion_admin");
  $admin->post("/personalizaciones/{id}/notificar", "handle_reintento_notificacion_personalizacion_admin");
})->add(new AdminMiddleware());
