<?php

use Psr\Http\Message\UploadedFileInterface;

require_once __DIR__ . "/../services/personalizacion.service.php";

function archivo_solicitud_personalizacion($request): ?UploadedFileInterface
{
    $archivo = $request->getUploadedFiles()["archivo"] ?? null;
    if ($archivo !== null && !$archivo instanceof UploadedFileInterface) {
        throw new RuntimeException("El archivo adjunto recibido no es válido.");
    }

    return $archivo;
}

function show_solicitud_personalizacion($request, $response)
{
    global $render;

    return $render($response, "personalizacion/crear.php", [
        "title" => "Solicitar personalización | Maderas Artesanales",
    ]);
}

function handle_solicitud_personalizacion($request, $response)
{
    global $render;

    $datos = (array) ($request->getParsedBody() ?? []);
    $archivo = archivo_solicitud_personalizacion($request);
    $validado = validar_solicitud_personalizacion($datos, $archivo);
    if (!$validado["ok"]) {
        return $render($response->withStatus(422), "personalizacion/crear.php", [
            "title" => "Solicitar personalización | Maderas Artesanales",
            "error" => $validado["error"],
            "descripcion" => trim((string) ($datos["descripcion"] ?? "")),
        ]);
    }

    if (!$archivo instanceof UploadedFileInterface) {
        throw new LogicException("No se recibió el archivo adjunto validado.");
    }

    try {
        crear_solicitud_personalizacion_cliente(
            (int) $_SESSION["usuario_id"],
            $validado["descripcion"],
            $archivo,
        );
    } catch (InvalidArgumentException $exception) {
        return $render($response->withStatus(422), "personalizacion/crear.php", [
            "title" => "Solicitar personalización | Maderas Artesanales",
            "error" => $exception->getMessage(),
            "descripcion" => $validado["descripcion"],
        ]);
    }

    $_SESSION["flash_personalizacion"] = "Tu solicitud fue enviada, te contactaremos pronto";

    return $response->withHeader("Location", "/#mi-cuenta")->withStatus(302);
}

function show_solicitudes_personalizacion_cliente($request, $response)
{
    global $render;

    $mensaje = $_SESSION["flash_personalizacion"] ?? null;
    unset($_SESSION["flash_personalizacion"]);

    return $render($response, "personalizacion/mis-solicitudes.php", [
        "title" => "Mis solicitudes | Maderas Artesanales",
        "solicitudes" => listar_solicitudes_personalizacion_cliente((int) $_SESSION["usuario_id"]),
        "mensaje" => $mensaje,
    ]);
}

function show_archivo_solicitud_personalizacion_cliente($request, $response, array $args = [])
{
    $archivo = obtener_archivo_solicitud_personalizacion_cliente(
        (int) ($args["id"] ?? 0),
        (int) $_SESSION["usuario_id"],
    );
    if ($archivo === null) {
        return $response->withStatus(404);
    }

    return responder_archivo_solicitud_personalizacion($response, $archivo);
}

function show_admin_solicitudes_personalizacion($request, $response)
{
    global $render;

    $mensaje = $_SESSION["flash_admin_personalizacion"] ?? null;
    unset($_SESSION["flash_admin_personalizacion"]);

    return $render($response, "admin/personalizaciones/index.php", [
        "title" => "Personalizaciones | Administración",
        "solicitudes" => listar_solicitudes_personalizacion_admin(),
        "mensaje" => $mensaje,
    ]);
}

function show_admin_solicitud_personalizacion($request, $response, array $args = [])
{
    global $render;

    $solicitud = obtener_solicitud_personalizacion_admin((int) ($args["id"] ?? 0));
    if ($solicitud === null) {
        return $response->withStatus(404);
    }

    $error = $_SESSION["error_admin_personalizacion"] ?? null;
    $mensaje = $_SESSION["flash_admin_personalizacion"] ?? null;
    unset($_SESSION["error_admin_personalizacion"], $_SESSION["flash_admin_personalizacion"]);

    return $render($response, "admin/personalizaciones/detalle.php", [
        "title" => "Solicitud #" . (int) $solicitud["id"] . " | Administración",
        "solicitud" => $solicitud,
        "error" => $error,
        "mensaje" => $mensaje,
    ]);
}

function show_archivo_solicitud_personalizacion_admin($request, $response, array $args = [])
{
    $archivo = obtener_archivo_solicitud_personalizacion_admin((int) ($args["id"] ?? 0));
    if ($archivo === null) {
        return $response->withStatus(404);
    }

    return responder_archivo_solicitud_personalizacion($response, $archivo);
}

function responder_archivo_solicitud_personalizacion($response, array $archivo)
{
    $datosArchivo = leer_archivo_solicitud_personalizacion($archivo);
    $response->getBody()->write($datosArchivo["contenido"]);
    $extension = [
        "application/pdf" => "pdf",
        "image/jpeg" => "jpg",
        "image/png" => "png",
    ][$datosArchivo["mime"]];

    return $response
        ->withHeader("Content-Type", $datosArchivo["mime"])
        ->withHeader("Content-Disposition", "attachment; filename=\"referencia." . $extension . "\"")
        ->withHeader("X-Content-Type-Options", "nosniff")
        ->withHeader("Cache-Control", "private, no-store");
}

function handle_respuesta_solicitud_personalizacion_admin($request, $response, array $args = [])
{
    $id = (int) ($args["id"] ?? 0);
    $datos = (array) ($request->getParsedBody() ?? []);

    try {
        responder_solicitud_personalizacion_admin($id, $datos);
        $_SESSION["flash_admin_personalizacion"] = "La decisión y la respuesta quedaron guardadas.";
        unset($_SESSION["error_admin_personalizacion"]);
    } catch (InvalidArgumentException | RuntimeException $exception) {
        $_SESSION["error_admin_personalizacion"] = $exception->getMessage();
    }

    return $response
        ->withHeader("Location", "/admin/personalizaciones/" . $id)
        ->withStatus(302);
}
