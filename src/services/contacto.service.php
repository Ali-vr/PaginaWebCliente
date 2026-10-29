<?php

require_once __DIR__ . "/../services/email.service.php";

function validar_contacto(array $datos): array
{
    $nombre = trim((string) ($datos["nombre"] ?? ""));
    $email = trim((string) ($datos["email"] ?? ""));
    $mensaje = trim((string) ($datos["mensaje"] ?? ""));

    $errores = [];

    if ($nombre === "") {
        $errores["nombre"] = "El nombre es obligatorio.";
    } elseif (mb_strlen($nombre, "UTF-8") > 120) {
        $errores["nombre"] = "El nombre no puede superar los 120 caracteres.";
    }

    if ($email === "") {
        $errores["email"] = "El correo electrónico es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores["email"] = "El formato del correo electrónico no es válido.";
    } elseif (mb_strlen($email, "UTF-8") > 254) {
        $errores["email"] = "El correo electrónico es demasiado largo.";
    }

    if ($mensaje === "") {
        $errores["mensaje"] = "El mensaje es obligatorio.";
    } elseif (mb_strlen($mensaje, "UTF-8") > 2000) {
        $errores["mensaje"] = "El mensaje no puede superar los 2000 caracteres.";
    }

    if ($errores !== []) {
        return [
            "valid" => false,
            "errores" => $errores,
        ];
    }

    return [
        "valid" => true,
        "nombre" => $nombre,
        "email" => $email,
        "mensaje" => $mensaje,
    ];
}

function enviar_correo_contacto(string $nombre, string $email, string $mensaje): bool
{
    $from = obtener_resend_from_email();
    $destinatario = obtener_contacto_destinatario();

    if ($from === "" || $destinatario === "") {
        error_log("Configuración de Resend incompleta para mensajes de contacto.");
        return false;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException("El correo del remitente no es válido.");
    }

    $asunto = "Nuevo mensaje de contacto";
    $nombreLimpio = preg_replace('/[\r\n]+/', ' ', $nombre);
    $nombreLimpio = trim((string) $nombreLimpio);
    if ($nombreLimpio !== "") {
        $asunto = "Nuevo mensaje de contacto de " . mb_substr($nombreLimpio, 0, 60, "UTF-8");
    }

    $html = "
        <div style=\"font-family: Arial, sans-serif; color: #1f2937;\">
            <h2 style=\"margin-bottom: 16px;\">Nuevo mensaje recibido desde la web</h2>
            <p><strong>Nombre:</strong> " . htmlspecialchars($nombre, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8") . "</p>
            <p><strong>Correo electrónico:</strong> " . htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8") . "</p>
            <p><strong>Mensaje:</strong></p>
            <div style=\"white-space: pre-wrap;\">" . nl2br(htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"), false) . "</div>
        </div>
    ";

    $texto = "Nuevo mensaje recibido desde la web\n\n"
        . "Nombre: " . $nombre . "\n"
        . "Correo electrónico: " . $email . "\n\n"
        . "Mensaje:\n" . $mensaje . "\n";

    try {
        $resend = obtener_resend_client();
        if ($resend === null) {
            error_log("No hay una API key válida configurada para Resend.");
            return false;
        }

        $resend->emails->send([
            "from" => $from,
            "to" => [$destinatario],
            "reply_to" => [$email],
            "subject" => $asunto,
            "html" => $html,
            "text" => $texto,
        ]);

        return true;
    } catch (Throwable $exception) {
        error_log("Falló el envío del formulario de contacto con Resend (" . get_class($exception) . ").");
        return false;
    }
}
