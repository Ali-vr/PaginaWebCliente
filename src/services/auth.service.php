<?php

require_once __DIR__ . "/../persistence/auth.persistence.php";

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
    session_destroy();
}
