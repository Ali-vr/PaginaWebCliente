<?php

require_once __DIR__ . "/../services/auth.service.php";

function show_login_form($request, $response)
{
    global $render;

    if (!empty($_SESSION["usuario_id"])) {
        return $response->withHeader("Location", "/mi-cuenta")->withStatus(302);
    }

    return $render($response, "auth/login.php", [
        "title" => "Iniciar sesión | Maderas Artesanales",
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

    return $response->withHeader("Location", "/mi-cuenta")->withStatus(302);
}

function show_register_form($request, $response)
{
    global $render;

    if (!empty($_SESSION["usuario_id"])) {
        return $response->withHeader("Location", "/mi-cuenta")->withStatus(302);
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
    global $render;

    $usuario = obtener_usuario_por_id((int) ($_SESSION["usuario_id"] ?? 0));

    if (!$usuario) {
        cerrar_sesion_actual();
        return $response->withHeader("Location", "/login")->withStatus(302);
    }

    return $render($response, "auth/mi-cuenta.php", [
        "title" => "Mi cuenta | Maderas Artesanales",
        "usuario" => $usuario,
    ]);
}
