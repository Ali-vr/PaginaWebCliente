<?php

require_once __DIR__ . "/../controllers/carrito.controller.php";

$app->post("/carrito/agregar", "handle_carrito_agregar");
$app->post("/carrito/eliminar/{id}", "handle_carrito_eliminar");
$app->get("/carrito", "show_carrito");
$app->get("/carrito/pago", "show_carrito_pago");
$app->post("/carrito/pago", "handle_carrito_pago");
$app->get("/carrito/confirmacion", "show_confirmacion");
