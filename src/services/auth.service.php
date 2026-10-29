<?php

require_once __DIR__ . "/../persistence/auth.persistence.php";
require_once __DIR__ . "/../persistence/admin.persistence.php";
require_once __DIR__ . "/email.service.php";
require_once __DIR__ . "/personalizacion.service.php";

function validar_login(array $datos): array
{
    $email = trim((string) ($datos["email"] ?? ""));
    $password = (string) ($datos["password"] ?? "");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === "") {
        return [
            "valid" => false,
            "error" => "Ingresá un email y una contraseña válidos.",
            "email" => $email,
        ];
    }

    return [
        "valid" => true,
        "email" => $email,
        "password" => $password,
    ];
}

function autenticar_usuario(string $email, string $password): ?array
{
    $usuario = find_user_by_email($email);

    if (!$usuario || !password_verify($password, (string) ($usuario["password_hash"] ?? ""))) {
        return null;
    }

    return $usuario;
}

function validar_registro(array $datos): array
{
    $nombre = trim((string) ($datos["nombre"] ?? ""));
    $email = trim((string) ($datos["email"] ?? ""));
    $password = (string) ($datos["password"] ?? "");
    $confirmacion = (string) ($datos["password_confirmation"] ?? "");

    if ($nombre === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            "valid" => false,
            "error" => "Completá un nombre y un email válido.",
            "nombre" => $nombre,
            "email" => $email,
        ];
    }

    if (strlen($password) < 8) {
        return [
            "valid" => false,
            "error" => "La contraseña debe tener al menos 8 caracteres.",
            "nombre" => $nombre,
            "email" => $email,
        ];
    }

    if ($password !== $confirmacion) {
        return [
            "valid" => false,
            "error" => "Las contraseñas no coinciden.",
            "nombre" => $nombre,
            "email" => $email,
        ];
    }

    return [
        "valid" => true,
        "nombre" => $nombre,
        "email" => $email,
        "password" => $password,
    ];
}

function registrar_usuario(string $nombre, string $email, string $password): void
{
    create_user($nombre, $email, password_hash($password, PASSWORD_DEFAULT));
}

function obtener_usuario_por_id(int $id): ?array
{
    return find_user_by_id($id);
}

function cerrar_sesion_actual(): void
{
    $_SESSION = [];

    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $cookieParams = session_get_cookie_params();
    if (ini_get("session.use_cookies")) {
        setcookie(session_name(), "", [
            "expires" => time() - 42000,
            "path" => $cookieParams["path"],
            "domain" => $cookieParams["domain"],
            "secure" => $cookieParams["secure"],
            "httponly" => $cookieParams["httponly"],
            "samesite" => $cookieParams["samesite"] ?? "Lax",
        ]);
    }

    session_destroy();
}

function obtener_datos_panel_cuenta(int $usuarioId): ?array
{
    $usuario = find_user_by_id($usuarioId);
    if ($usuario === null) {
        return null;
    }

    $pedidos = array_map(
        static fn (array $pedido): array => [
            "numero" => (string) $pedido["numero"],
            "created_at" => (string) $pedido["created_at"],
            "total" => (float) $pedido["total"],
            "estado" => (string) $pedido["estado"],
            "productos_resumen" => (string) ($pedido["productos_resumen"] ?? ""),
            "items_count" => (int) ($pedido["items_count"] ?? 0),
        ],
        fetch_pedidos_por_usuario($usuarioId),
    );

    $solicitudes = array_map(
        static fn (array $solicitud): array => [
            "id" => (int) $solicitud["id"],
            "descripcion" => (string) $solicitud["descripcion"],
            "archivo_nombre" => (string) $solicitud["archivo_nombre"],
            "estado" => (string) $solicitud["estado"],
            "presupuesto" => $solicitud["presupuesto"] === null ? null : (float) $solicitud["presupuesto"],
            "respuesta" => (string) ($solicitud["respuesta"] ?? ""),
            "created_at" => (string) $solicitud["created_at"],
        ],
        listar_solicitudes_personalizacion_cliente($usuarioId),
    );

    return [
        "usuario" => [
            "nombre" => (string) $usuario["nombre"],
            "email" => (string) $usuario["email"],
        ],
        "pedidos" => $pedidos,
        "solicitudes" => $solicitudes,
    ];
}

function solicitar_recuperacion_contrasena(string $email): void
{
    $resend = obtener_resend_client();
    $sender = obtener_resend_from_email();
    $appUrl = rtrim(trim((string) ($_ENV["APP_URL"] ?? "")), "/");
    $scheme = strtolower((string) parse_url($appUrl, PHP_URL_SCHEME));

    if (
        $resend === null
        || $sender === ""
        || !filter_var($appUrl, FILTER_VALIDATE_URL)
        || !in_array($scheme, ["http", "https"], true)
    ) {
        throw new RuntimeException("La configuración del correo para recuperar contraseñas está incompleta.");
    }

    $usuario = find_user_by_email($email);
    if ($usuario === null) {
        return;
    }

    $token = bin2hex(random_bytes(32));
    $tokenHash = hash("sha256", $token);
    save_password_reset_token((int) $usuario["id"], $tokenHash);

    $resetUrl = $appUrl . "/restablecer-contrasena?token=" . urlencode($token);
    $escapedResetUrl = htmlspecialchars($resetUrl, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");

    try {
        $resend->emails->send([
            "from" => $sender,
            "to" => [$email],
            "subject" => "Restablecer contraseña",
            "html" => "<p>Recibimos una solicitud para restablecer tu contraseña.</p>"
                . "<p><a href=\"{$escapedResetUrl}\">Crear una nueva contraseña</a></p>"
                . "<p>El enlace vence en una hora. Si no solicitaste este cambio, ignora este correo.</p>",
            "text" => "Recibimos una solicitud para restablecer tu contraseña.\n\n"
                . "Abrí este enlace para crear una nueva contraseña (vence en una hora):\n"
                . $resetUrl
                . "\n\nSi no solicitaste este cambio, ignora este correo.",
        ]);
    } catch (Throwable $exception) {
        clear_password_reset_token((int) $usuario["id"], $tokenHash);
        throw $exception;
    }
}

function validar_token_recuperacion(string $token): bool
{
    if (strlen($token) !== 64 || !ctype_xdigit($token)) {
        return false;
    }

    return password_reset_token_exists(hash("sha256", $token));
}

function validar_nueva_contrasena(array $datos): array
{
    $password = (string) ($datos["password"] ?? "");
    $confirmacion = (string) ($datos["password_confirmation"] ?? "");

    if (strlen($password) < 8) {
        return [
            "valid" => false,
            "error" => "La contraseña debe tener al menos 8 caracteres.",
        ];
    }

    if ($password !== $confirmacion) {
        return [
            "valid" => false,
            "error" => "Las contraseñas no coinciden.",
        ];
    }

    return [
        "valid" => true,
        "password" => $password,
    ];
}

function restablecer_contrasena(string $token, string $password): bool
{
    if (!validar_token_recuperacion($token)) {
        return false;
    }

    return update_password_with_reset_token(
        hash("sha256", $token),
        password_hash($password, PASSWORD_DEFAULT),
    );
}
