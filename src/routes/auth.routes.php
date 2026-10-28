<?php

require_once __DIR__ . "/../controllers/auth.controller.php";

// Rutas de Autenticación y Cuenta
$router->get("/login", "show_login_form");
$router->post("/login", "handle_login");

$router->get("/registro", "show_register_form");
$router->post("/registro", "handle_register");

$router->get("/logout", "handle_logout");
$router->post("/logout", "handle_logout");

$router->get("/mi-cuenta", "show_mi_cuenta");
$router->get("/mi-cuenta/pedidos", "show_mis_pedidos");

// Recuperación y Restablecimiento de Contraseña
$router->get("/recuperar-contrasena", "show_recuperar_contrasena");
$router->post("/recuperar-contrasena", "handle_solicitud_recuperacion");

$router->get("/restablecer-contrasena", "show_restablecer_contrasena");
$router->post("/restablecer-contrasena", "handle_restablecer_contrasena");