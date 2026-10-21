<?php

require_once __DIR__ . "/../controllers/contacto.controller.php";

$app->get("/contacto", "show_contacto");
$app->post("/contacto", "handle_contacto");

$router->get('/contacto', [ContactoController::class, 'index']);
$router->post('/contacto', [ContactoController::class, 'enviar']);