<?php

use App\Middleware\AuthMiddleware;

require_once __DIR__ . "/../controllers/personalizacion.controller.php";

$app->get("/personalizacion", "show_solicitud_personalizacion")
    ->add(new AuthMiddleware());
$app->post("/personalizacion", "handle_solicitud_personalizacion")
    ->add(new AuthMiddleware());
$app->get("/mi-cuenta/personalizaciones", "show_solicitudes_personalizacion_cliente")
    ->add(new AuthMiddleware());
$app->get("/mi-cuenta/personalizaciones/{id}/archivo", "show_archivo_solicitud_personalizacion_cliente")
    ->add(new AuthMiddleware());
