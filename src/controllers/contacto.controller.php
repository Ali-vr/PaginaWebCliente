<?php

require_once __DIR__ . "/../services/contacto.service.php";

function es_peticion_ajax($request): bool
{
    $header = $request->getHeaderLine("X-Requested-With");
    return strtolower($header) === "xmlhttprequest";
}

function show_contacto($request, $response)
{
    global $render;

    $success = $_SESSION["flash_contacto"]["mensaje"] ?? null;
    $error = $_SESSION["flash_contacto"]["error"] ?? null;
    unset($_SESSION["flash_contacto"]);

    return $render($response, "contacto.php", [
        "title" => "Contacto | Maderas Artesanales",
        "success" => $success,
        "error" => $error,
    ]);
}

function handle_contacto($request, $response)
{
    $datos = (array) ($request->getParsedBody() ?? []);
    $resultado = validar_contacto($datos);

    if (!$resultado["valid"]) {
        if (es_peticion_ajax($request)) {
            $response = $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(422);
            $response->getBody()->write(json_encode([
                "exito" => false,
                "errores" => $resultado["errores"],
            ], JSON_THROW_ON_ERROR));
            return $response;
        }

        $_SESSION["flash_contacto"] = [
            "error" => "Completá tu nombre, un email válido y un mensaje para enviar la consulta.",
        ];

        return $response
            ->withHeader("Location", "/contacto")
            ->withStatus(302);
    }

    try {
        $enviado = enviar_correo_contacto(
            (string) $resultado["nombre"],
            (string) $resultado["email"],
            (string) $resultado["mensaje"],
        );
    } catch (Throwable $exception) {
        error_log("Error inesperado al enviar el formulario de contacto.");
        $enviado = false;
    }

    if ($enviado) {
        if (es_peticion_ajax($request)) {
            $response = $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(200);
            $response->getBody()->write(json_encode([
                "exito" => true,
                "mensaje" => "Gracias, tu consulta fue enviada correctamente. Nos pondremos en contacto a la brevedad.",
            ], JSON_THROW_ON_ERROR));
            return $response;
        }

        $_SESSION["flash_contacto"] = [
            "mensaje" => "Gracias, tu consulta fue enviada correctamente. Nos pondremos en contacto a la brevedad.",
        ];

        return $response
            ->withHeader("Location", "/contacto")
            ->withStatus(302);
    }

    if (es_peticion_ajax($request)) {
        $response = $response
            ->withHeader("Content-Type", "application/json")
            ->withStatus(500);
        $response->getBody()->write(json_encode([
            "exito" => false,
            "error_general" => "Ocurrió un error al enviar el mensaje. Intentá de nuevo más tarde.",
        ], JSON_THROW_ON_ERROR));
        return $response;
    }

    $_SESSION["flash_contacto"] = [
        "error" => "Ocurrió un error al enviar el mensaje. Intentá de nuevo más tarde.",
    ];

    return $response
        ->withHeader("Location", "/contacto")
        ->withStatus(302);
}
