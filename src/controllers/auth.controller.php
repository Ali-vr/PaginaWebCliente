<?php

require_once __DIR__ . "/../services/auth.service.php";
require_once __DIR__ . "/../services/admin.service.php";

function show_login_form($request, $response)
{
    global $render;

    if (!empty($_SESSION["usuario_id"])) {
        return $response->withHeader("Location", "/#mi-cuenta")->withStatus(302);
    }

    $success = $_SESSION["flash_auth"]["success"] ?? null;
    unset($_SESSION["flash_auth"]);

    return $render($response, "auth/login.php", [
        "title" => "Iniciar sesión | Maderas Artesanales",
        "success" => $success,
    ]);
}

function handle_login($request, $response)
{
    global $render;

    $datos = (array) $request->getParsedBody();
    $resultado = validar_login($datos);

    if (!$resultado["valid"]) {
        return $render($response->withStatus(422), "auth/login.php", [
            "title" => "Iniciar sesión | Maderas Artesanales",
            "error" => $resultado["error"],
            "email" => $resultado["email"],
        ]);
    }

    $usuario = autenticar_usuario($resultado["email"], $resultado["password"]);
    if (!$usuario) {
        return $render($response->withStatus(422), "auth/login.php", [
            "title" => "Iniciar sesión | Maderas Artesanales",
            "error" => "El email o la contraseña no son correctos.",
            "email" => $resultado["email"],
        ]);
    }

    session_regenerate_id(true);
    $_SESSION["usuario_id"] = (int) $usuario["id"];
    $_SESSION["usuario"] = [
        "id" => (int) $usuario["id"],
        "nombre" => $usuario["nombre"],
        "email" => $usuario["email"],
        "rol" => $usuario["rol"],
    ];

    return $response->withHeader("Location", "/#mi-cuenta")->withStatus(302);
}

function show_register_form($request, $response)
{
    global $render;

    if (!empty($_SESSION["usuario_id"])) {
        return $response->withHeader("Location", "/#mi-cuenta")->withStatus(302);
    }

    return $render($response, "auth/registro.php", [
        "title" => "Crear cuenta | Maderas Artesanales",
    ]);
}

function handle_register($request, $response)
{
    global $render;

    $datos = (array) $request->getParsedBody();
    $resultado = validar_registro($datos);

    if (!$resultado["valid"]) {
        return $render($response->withStatus(422), "auth/registro.php", [
            "title" => "Crear cuenta | Maderas Artesanales",
            "error" => $resultado["error"],
            "nombre" => $resultado["nombre"],
            "email" => $resultado["email"],
        ]);
    }

    try {
        registrar_usuario($resultado["nombre"], $resultado["email"], $resultado["password"]);
    } catch (PDOException $exception) {
        if ($exception->getCode() === "23000") {
            return $render($response->withStatus(422), "auth/registro.php", [
                "title" => "Crear cuenta | Maderas Artesanales",
                "error" => "Ya existe una cuenta con ese email.",
                "nombre" => $resultado["nombre"],
                "email" => $resultado["email"],
            ]);
        }

        throw $exception;
    }

    return $response->withHeader("Location", "/login")->withStatus(302);
}

function handle_logout($request, $response)
{
    cerrar_sesion_actual();
    return $response->withHeader("Location", "/")->withStatus(302);
}

function show_mi_cuenta($request, $response)
{
    if (empty($_SESSION["usuario_id"])) {
        return $response->withHeader("Location", "/login")->withStatus(302);
    }

    return $response->withHeader("Location", "/#mi-cuenta")->withStatus(302);
}

function show_datos_panel_cuenta($request, $response)
{
    $usuarioId = (int) ($_SESSION["usuario_id"] ?? 0);

    try {
        $datos = obtener_datos_panel_cuenta($usuarioId);
    } catch (Throwable $exception) {
        error_log("No se pudieron cargar los datos del panel de cuenta (" . get_class($exception) . ").");
        $response = $response
            ->withHeader("Content-Type", "application/json; charset=utf-8")
            ->withStatus(500);
        $response->getBody()->write(json_encode([
            "error" => "No se pudieron cargar los datos de la cuenta. Intentá de nuevo más tarde.",
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
        return $response;
    }

    if ($datos === null) {
        cerrar_sesion_actual();
        $response = $response
            ->withHeader("Content-Type", "application/json; charset=utf-8")
            ->withStatus(401);
        $response->getBody()->write(json_encode([
            "error" => "La sesión ya no es válida. Iniciá sesión nuevamente.",
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
        return $response;
    }

    $response = $response->withHeader("Content-Type", "application/json; charset=utf-8");
    $response->getBody()->write(json_encode(
        $datos,
        JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE,
    ));
    return $response;
}

function show_mis_pedidos($request, $response)
{
    global $render;

    $usuarioId = (int) ($_SESSION["usuario_id"] ?? 0);
    $usuario = obtener_usuario_por_id($usuarioId);

    if (!$usuario) {
        cerrar_sesion_actual();
        return $response->withHeader("Location", "/login")->withStatus(302);
    }

    $pedidos = fetch_pedidos_por_usuario($usuarioId);

    return $render($response, "auth/mis-pedidos.php", [
        "title" => "Mis pedidos | Maderas Artesanales",
        "usuario" => $usuario,
        "pedidos" => $pedidos,
    ]);
}

function show_recuperar_contrasena($request, $response)
{
    global $render;

    return $render($response, "auth/recuperar-contrasena.php", [
        "title" => "Recuperar contraseña | Maderas Artesanales",
    ]);
}

function handle_solicitud_recuperacion($request, $response)
{
    global $render;

    $datos = (array) ($request->getParsedBody() ?? []);
    $email = strtolower(trim((string) ($datos["email"] ?? "")));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $render($response->withStatus(422), "auth/recuperar-contrasena.php", [
            "title" => "Recuperar contraseña | Maderas Artesanales",
            "error" => "Ingresá un email válido.",
            "email" => $email,
        ]);
    }

    solicitar_recuperacion_contrasena($email);
    $_SESSION["flash_auth"] = [
        "success" => "Si el correo está registrado, recibirás un enlace para recuperar tu contraseña.",
    ];

    return $response->withHeader("Location", "/login")->withStatus(302);
}

function show_restablecer_contrasena($request, $response)
{
    global $render;

    $params = $request->getQueryParams();
    $token = (string) ($params["token"] ?? "");
    $tokenValido = validar_token_recuperacion($token);

    return $render($response->withStatus($tokenValido ? 200 : 400), "auth/restablecer-contrasena.php", [
        "title" => "Restablecer contraseña | Maderas Artesanales",
        "token" => $token,
        "tokenValido" => $tokenValido,
    ]);
}

function handle_restablecer_contrasena($request, $response)
{
    global $render;

    $datos = (array) ($request->getParsedBody() ?? []);
    $token = (string) ($datos["token"] ?? "");

    if (!validar_token_recuperacion($token)) {
        return $render($response->withStatus(400), "auth/restablecer-contrasena.php", [
            "title" => "Restablecer contraseña | Maderas Artesanales",
            "tokenValido" => false,
            "error" => "El enlace no es válido o ya venció. Solicitá uno nuevo.",
        ]);
    }

    $resultado = validar_nueva_contrasena($datos);
    if (!$resultado["valid"]) {
        return $render($response->withStatus(422), "auth/restablecer-contrasena.php", [
            "title" => "Restablecer contraseña | Maderas Artesanales",
            "token" => $token,
            "tokenValido" => true,
            "error" => $resultado["error"],
        ]);
    }

    if (!restablecer_contrasena($token, $resultado["password"])) {
        return $render($response->withStatus(400), "auth/restablecer-contrasena.php", [
            "title" => "Restablecer contraseña | Maderas Artesanales",
            "tokenValido" => false,
            "error" => "El enlace no es válido o ya venció. Solicitá uno nuevo.",
        ]);
    }

    $_SESSION["flash_auth"] = [
        "success" => "Tu contraseña se actualizó. Ya podés iniciar sesión.",
    ];

    return $response->withHeader("Location", "/login")->withStatus(302);
}
