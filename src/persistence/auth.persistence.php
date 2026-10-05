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
