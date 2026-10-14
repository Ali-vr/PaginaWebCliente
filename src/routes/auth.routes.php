<?php

use App\Middleware\AuthMiddleware;

require_once __DIR__ . "/../controllers/auth.controller.php";

$app->get("/login", "show_login_form");
$app->post("/login", "handle_login");
$app->get("/registro", "show_register_form");
$app->post("/registro", "handle_register");
$app->get("/logout", "handle_logout");
$app->get("/mi-cuenta", "show_mi_cuenta")->add(new AuthMiddleware());
$app->get("/mi-cuenta/pedidos", "show_mis_pedidos")->add(new AuthMiddleware());
$app->get("/recuperar-contrasena", "show_recuperar_contrasena");
$app->post("/recuperar-contrasena", "handle_solicitud_recuperacion");
$app->get("/restablecer-contrasena", "show_restablecer_contrasena");
$app->post("/restablecer-contrasena", "handle_restablecer_contrasena");
