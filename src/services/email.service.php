<?php

function obtener_resend_client(): ?\Resend\Client
{
    $apiKey = trim((string) ($_ENV["RESEND_API_KEY"] ?? getenv("RESEND_API_KEY") ?: ""));

    if ($apiKey === "") {
        return null;
    }

    try {
        return Resend::client($apiKey);
    } catch (Throwable $exception) {
        error_log("No se pudo inicializar el cliente de Resend (" . get_class($exception) . ").");
        return null;
    }
}

function obtener_resend_from_email(): string
{
    $sender = trim((string) ($_ENV["RESEND_FROM_EMAIL"] ?? getenv("RESEND_FROM_EMAIL") ?: ""));
    if ($sender !== "") {
        return $sender;
    }

    return trim((string) ($_ENV["MAIL_FROM"] ?? getenv("MAIL_FROM") ?: ""));
}

function obtener_contacto_destinatario(): string
{
    return trim((string) ($_ENV["CONTACT_TO_EMAIL"] ?? getenv("CONTACT_TO_EMAIL") ?: ""));
}
