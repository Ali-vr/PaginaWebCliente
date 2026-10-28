<?php

require_once __DIR__ . "/../controllers/personalizacion.controller.php";

// Función auxiliar para verificar la sesión de forma segura
$verificarSesion = function ($response) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['usuario']) && !isset($_SESSION['user_id'])) {
        return false;
    }
    return true;
};

$app->get("/personalizacion", function ($request, $response, $args) use ($verificarSesion) {
    if (!$verificarSesion($response)) {
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
    return show_solicitud_personalizacion($request, $response, $args);
});

$app->post("/personalizacion", function ($request, $response, $args) use ($verificarSesion) {
    if (!$verificarSesion($response)) {
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
    return handle_solicitud_personalizacion($request, $response, $args);
});

$app->get("/mi-cuenta/personalizaciones", function ($request, $response, $args) use ($verificarSesion) {
    if (!$verificarSesion($response)) {
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
    return show_solicitudes_personalizacion_cliente($request, $response, $args);
});

$app->get("/mi-cuenta/personalizaciones/{id}/archivo", function ($request, $response, $args) use ($verificarSesion) {
    if (!$verificarSesion($response)) {
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
    return show_archivo_solicitud_personalizacion_cliente($request, $response, $args);
});
