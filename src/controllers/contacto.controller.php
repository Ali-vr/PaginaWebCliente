<?php

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
    $data = $request->getParsedBody() ?? [];
    $nombre = trim((string) ($data["nombre"] ?? ""));
    $email = trim(strtolower((string) ($data["email"] ?? "")));
    $mensaje = trim((string) ($data["mensaje"] ?? ""));

    if ($nombre === "" || $mensaje === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["flash_contacto"] = [
            "error" => "Completá tu nombre, un email válido y tu mensaje para enviar la consulta.",
        ];

        return $response
            ->withHeader("Location", "/contacto")
            ->withStatus(302);
    }

    $_SESSION["flash_contacto"] = [
        "mensaje" => "Gracias, tu consulta fue enviada correctamente. Nos pondremos en contacto a la brevedad.",
    ];

    return $response
        ->withHeader("Location", "/contacto")
        ->withStatus(302);
}
