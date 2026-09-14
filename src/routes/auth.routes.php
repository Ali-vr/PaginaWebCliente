<?php

use App\Middleware\AuthMiddleware;

$app->get("/login", function ($request, $response) use ($render) {
  // Si ya hay sesión activa, redirige directo a mi cuenta
  if (!empty($_SESSION["usuario_id"])) {
    return $response->withHeader("Location", "/mi-cuenta")->withStatus(302);
  }
  return $render($response, "auth/login.php", [
    "title" => "Iniciar sesión | Maderas Artesanales",
  ]);
});

$app->post("/login", function ($request, $response) use ($render) {
  $datos = (array) $request->getParsedBody();
  $email = trim((string) ($datos["email"] ?? ""));
  $password = (string) ($datos["password"] ?? "");

  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === "") {
    return $render($response->withStatus(422), "auth/login.php", [
      "title" => "Iniciar sesión | Maderas Artesanales",
      "error" => "Ingresá un email y una contraseña válidos.",
      "email" => $email,
    ]);
  }

  $stmt = getDB()->prepare("SELECT id, nombre, email, password_hash, rol FROM usuarios WHERE email = :email LIMIT 1");
  $stmt->execute(["email" => $email]);
  $usuario = $stmt->fetch();

  if (!$usuario || !password_verify($password, $usuario["password_hash"])) {
    return $render($response->withStatus(422), "auth/login.php", [
      "title" => "Iniciar sesión | Maderas Artesanales",
      "error" => "El email o la contraseña no son correctos.",
      "email" => $email,
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
});

$app->get("/registro", function ($request, $response) use ($render) {
  if (!empty($_SESSION["usuario_id"])) {
    return $response->withHeader("Location", "/mi-cuenta")->withStatus(302);
  }
  return $render($response, "auth/registro.php", [
    "title" => "Crear cuenta | Maderas Artesanales",
  ]);
});

$app->post("/registro", function ($request, $response) use ($render) {
  $datos = (array) $request->getParsedBody();
  $nombre = trim((string) ($datos["nombre"] ?? ""));
  $email = trim((string) ($datos["email"] ?? ""));
  $password = (string) ($datos["password"] ?? "");
  $confirmacion = (string) ($datos["password_confirmation"] ?? "");

  $error = null;
  if ($nombre === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Completá un nombre y un email válido.";
  } elseif (strlen($password) < 8) {
    $error = "La contraseña debe tener al menos 8 caracteres.";
  } elseif ($password !== $confirmacion) {
    $error = "Las contraseñas no coinciden.";
  }

  if ($error !== null) {
    return $render($response->withStatus(422), "auth/registro.php", [
      "title" => "Crear cuenta | Maderas Artesanales",
      "error" => $error,
      "nombre" => $nombre,
      "email" => $email,
    ]);
  }

  try {
    $stmt = getDB()->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (:nombre, :email, :password_hash, 'usuario')");
    $stmt->execute([
      "nombre" => $nombre,
      "email" => $email,
      "password_hash" => password_hash($password, PASSWORD_DEFAULT),
    ]);
  } catch (PDOException $exception) {
    if ($exception->getCode() === "23000") {
      return $render($response->withStatus(422), "auth/registro.php", [
        "title" => "Crear cuenta | Maderas Artesanales",
        "error" => "Ya existe una cuenta con ese email.",
        "nombre" => $nombre,
        "email" => $email,
      ]);
    }
    throw $exception;
  }

  return $response->withHeader("Location", "/login")->withStatus(302);
});

$app->get("/logout", function ($request, $response) {
  $_SESSION = [];
  session_destroy();
  return $response->withHeader("Location", "/")->withStatus(302);
});

$app->get("/mi-cuenta", function ($request, $response) use ($render) {
  $stmt = getDB()->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE id = :id LIMIT 1");
  $stmt->execute(["id" => (int) $_SESSION["usuario_id"]]);
  $usuario = $stmt->fetch();

  if (!$usuario) {
    $_SESSION = [];
    session_destroy();
    return $response->withHeader("Location", "/login")->withStatus(302);
  }

  return $render($response, "auth/mi-cuenta.php", [
    "title" => "Mi cuenta | Maderas Artesanales",
    "usuario" => $usuario,
  ]);
})->add(new AuthMiddleware());
