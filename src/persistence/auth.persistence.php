<?php

function find_user_by_email(string $email): ?array
{
    $stmt = getDB()->prepare("SELECT id, nombre, email, password_hash, rol FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute(["email" => $email]);

    $usuario = $stmt->fetch();
    return $usuario === false ? null : $usuario;
}

function find_user_by_id(int $id): ?array
{
    $stmt = getDB()->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE id = :id LIMIT 1");
    $stmt->execute(["id" => $id]);

    $usuario = $stmt->fetch();
    return $usuario === false ? null : $usuario;
}

function create_user(string $nombre, string $email, string $passwordHash): void
{
    $stmt = getDB()->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (:nombre, :email, :password_hash, 'usuario')");
    $stmt->execute([
        "nombre" => $nombre,
        "email" => $email,
        "password_hash" => $passwordHash,
    ]);
}

// Guardar el token generado para el usuario
function guardarTokenRecuperacion($email, $token, $expiracion) {
    $sql = "UPDATE usuarios SET reset_token = :token, reset_token_expires_at = :expiracion WHERE email = :email";
    $stmt = getDB()->prepare($sql);
    return $stmt->execute([
        'token' => $token,
        'expiracion' => $expiracion,
        'email' => $email
    ]);
}

// Verificar que el token sea válido y no haya expirado
function verificarTokenValido($token) {
    $sql = "SELECT id, email FROM usuarios WHERE reset_token = :token AND reset_token_expires_at > NOW()";
    $stmt = getDB()->prepare($sql);
    $stmt->execute(['token' => $token]);
    return $stmt->fetch();
}