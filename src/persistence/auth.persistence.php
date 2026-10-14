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

function save_password_reset_token(int $userId, string $tokenHash): void
{
    $stmt = getDB()->prepare(
        "UPDATE usuarios
         SET reset_token = :token, reset_token_expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR)
         WHERE id = :id",
    );
    $stmt->execute([
        "token" => $tokenHash,
        "id" => $userId,
    ]);
}

function password_reset_token_exists(string $tokenHash): bool
{
    $stmt = getDB()->prepare(
        "SELECT 1 FROM usuarios
         WHERE reset_token = :token AND reset_token_expires_at > NOW()
         LIMIT 1",
    );
    $stmt->execute(["token" => $tokenHash]);

    return $stmt->fetchColumn() !== false;
}

function update_password_with_reset_token(string $tokenHash, string $passwordHash): bool
{
    $stmt = getDB()->prepare(
        "UPDATE usuarios
         SET password_hash = :password_hash,
             reset_token = NULL,
             reset_token_expires_at = NULL
         WHERE reset_token = :token AND reset_token_expires_at > NOW()",
    );
    $stmt->execute([
        "password_hash" => $passwordHash,
        "token" => $tokenHash,
    ]);

    return $stmt->rowCount() === 1;
}

function clear_password_reset_token(int $userId, string $tokenHash): void
{
    $stmt = getDB()->prepare(
        "UPDATE usuarios
         SET reset_token = NULL, reset_token_expires_at = NULL
         WHERE id = :id AND reset_token = :token",
    );
    $stmt->execute([
        "id" => $userId,
        "token" => $tokenHash,
    ]);
}
