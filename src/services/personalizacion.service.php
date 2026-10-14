<?php

use Psr\Http\Message\UploadedFileInterface;

require_once __DIR__ . "/../persistence/personalizacion.persistence.php";
require_once __DIR__ . "/../persistence/auth.persistence.php";

const PERSONALIZACION_MAX_ARCHIVO_BYTES = 10 * 1024 * 1024;

function validar_solicitud_personalizacion(array $datos, ?UploadedFileInterface $archivo): array
{
    $descripcion = trim((string) ($datos["descripcion"] ?? ""));
    if (mb_strlen($descripcion) < 20) {
        return [
            "ok" => false,
            "error" => "Contanos más detalles de lo que necesitás (al menos 20 caracteres).",
        ];
    }
    if (mb_strlen($descripcion) > 5000) {
        return [
            "ok" => false,
            "error" => "La descripción no puede superar los 5000 caracteres.",
        ];
    }

    if ($archivo === null || $archivo->getError() === UPLOAD_ERR_NO_FILE) {
        return [
            "ok" => false,
            "error" => "Adjuntá un plano o una imagen de referencia.",
        ];
    }

    if ($archivo->getError() !== UPLOAD_ERR_OK) {
        if (in_array($archivo->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return [
                "ok" => false,
                "error" => "El archivo no puede superar los 10 MB.",
            ];
        }

        return [
            "ok" => false,
            "error" => "No se pudo recibir el archivo. Volvé a intentarlo.",
        ];
    }

    if (($archivo->getSize() ?? 0) > PERSONALIZACION_MAX_ARCHIVO_BYTES) {
        return [
            "ok" => false,
            "error" => "El archivo no puede superar los 10 MB.",
        ];
    }

    return [
        "ok" => true,
        "descripcion" => $descripcion,
    ];
}

function guardar_archivo_personalizacion(UploadedFileInterface $archivo): array
{
    $contenido = $archivo->getStream()->getContents();
    if (strlen($contenido) > PERSONALIZACION_MAX_ARCHIVO_BYTES) {
        throw new InvalidArgumentException("El archivo no puede superar los 10 MB.");
    }

    $dimensiones = getimagesizefromstring($contenido);
    $mimeImagen = is_array($dimensiones) ? ($dimensiones["mime"] ?? null) : null;
    $mimeDetectado = (new finfo(FILEINFO_MIME_TYPE))->buffer($contenido);

    if ($mimeDetectado === "application/pdf" && str_starts_with($contenido, "%PDF-")) {
        $mime = "application/pdf";
        $extension = "pdf";
    } elseif (
        $mimeDetectado === "image/jpeg"
        && $mimeImagen === "image/jpeg"
    ) {
        $mime = "image/jpeg";
        $extension = "jpg";
    } elseif (
        $mimeDetectado === "image/png"
        && $mimeImagen === "image/png"
    ) {
        $mime = "image/png";
        $extension = "png";
    } else {
        throw new InvalidArgumentException("Formato no permitido. Usá PDF, JPG o PNG");
    }

    $nombreOriginal = str_replace("\\", "/", $archivo->getClientFilename() ?? "");
    $nombreOriginal = basename($nombreOriginal);
    $nombreOriginal = preg_replace('/[\x00-\x1F\x7F]/u', "", $nombreOriginal) ?? "";
    $nombreOriginal = trim(mb_substr($nombreOriginal, 0, 255));
    if ($nombreOriginal === "") {
        $nombreOriginal = "referencia." . $extension;
    }

    $directorio = dirname(__DIR__, 2) . "/storage/personalizaciones";
    if (!is_dir($directorio) && !mkdir($directorio, 0750, true) && !is_dir($directorio)) {
        throw new RuntimeException("No se pudo preparar el almacenamiento de solicitudes.");
    }

    $nombreGuardado = bin2hex(random_bytes(24)) . "." . $extension;
    try {
        $archivo->moveTo($directorio . "/" . $nombreGuardado);
    } catch (Throwable $exception) {
        throw new RuntimeException("No se pudo guardar el archivo adjunto.", 0, $exception);
    }

    return [
        "nombre" => $nombreOriginal,
        "guardado" => $nombreGuardado,
        "mime" => $mime,
    ];
}

function crear_solicitud_personalizacion_cliente(
    int $usuarioId,
    string $descripcion,
    UploadedFileInterface $archivo,
): int {
    $datosArchivo = guardar_archivo_personalizacion($archivo);

    try {
        return create_solicitud_personalizacion([
            "usuario_id" => $usuarioId,
            "descripcion" => $descripcion,
            "archivo_nombre" => $datosArchivo["nombre"],
            "archivo_guardado" => $datosArchivo["guardado"],
            "archivo_mime" => $datosArchivo["mime"],
        ]);
    } catch (Throwable $exception) {
        $ruta = dirname(__DIR__, 2) . "/storage/personalizaciones/" . $datosArchivo["guardado"];
        if (is_file($ruta) && !unlink($ruta)) {
            throw new RuntimeException(
                "La solicitud no se pudo registrar y no se pudo limpiar el archivo temporal.",
                0,
                $exception,
            );
        }
        throw $exception;
    }
}

function listar_solicitudes_personalizacion_cliente(int $usuarioId): array
{
    return fetch_solicitudes_personalizacion_usuario($usuarioId);
}

function listar_solicitudes_personalizacion_admin(): array
{
    return fetch_solicitudes_personalizacion_admin();
}

function obtener_solicitud_personalizacion_admin(int $id): ?array
{
    return fetch_solicitud_personalizacion_por_id($id);
}

function obtener_archivo_solicitud_personalizacion_admin(int $id): ?array
{
    $solicitud = fetch_solicitud_personalizacion_por_id($id);
    return $solicitud;
}

function obtener_archivo_solicitud_personalizacion_cliente(int $id, int $usuarioId): ?array
{
    return fetch_solicitud_personalizacion_de_usuario($id, $usuarioId);
}

function leer_archivo_solicitud_personalizacion(array $solicitud): array
{
    $nombre = (string) ($solicitud["archivo_guardado"] ?? "");
    $mime = (string) ($solicitud["archivo_mime"] ?? "");
    $extensiones = [
        "application/pdf" => "pdf",
        "image/jpeg" => "jpg",
        "image/png" => "png",
    ];
    if (!isset($extensiones[$mime]) || !preg_match('/^[a-f0-9]{48}\.' . $extensiones[$mime] . '$/', $nombre)) {
        throw new RuntimeException("Los datos del archivo adjunto no son válidos.");
    }

    $ruta = dirname(__DIR__, 2) . "/storage/personalizaciones/" . $nombre;
    $contenido = file_get_contents($ruta);
    if ($contenido === false) {
        throw new RuntimeException("No se pudo leer el archivo adjunto de la solicitud.");
    }

    return [
        "contenido" => $contenido,
        "mime" => $mime,
        "nombre" => $nombre,
    ];
}

function validar_respuesta_personalizacion(array $datos): array
{
    $estado = (string) ($datos["estado"] ?? "");
    $respuesta = trim((string) ($datos["respuesta"] ?? ""));
    $presupuesto = null;

    if (!in_array($estado, ["aceptada", "rechazada"], true)) {
        return ["ok" => false, "error" => "Seleccioná si la solicitud se acepta o se rechaza."];
    }

    if ($estado === "aceptada") {
        $presupuesto = filter_var($datos["presupuesto"] ?? null, FILTER_VALIDATE_FLOAT);
        if ($presupuesto === false || $presupuesto <= 0 || $presupuesto > 99999999.99) {
            return ["ok" => false, "error" => "Ingresá un presupuesto entre $0,01 y $99.999.999,99 para aceptar la solicitud."];
        }
        if ($respuesta === "") {
            return ["ok" => false, "error" => "Agregá un mensaje con los detalles del presupuesto."];
        }
    } elseif ($respuesta === "") {
        return ["ok" => false, "error" => "Indicá el motivo del rechazo para informar al cliente."];
    }

    return [
        "ok" => true,
        "estado" => $estado,
        "presupuesto" => $presupuesto,
        "respuesta" => $respuesta,
    ];
}

function responder_solicitud_personalizacion_admin(int $id, array $datos): void
{
    $validado = validar_respuesta_personalizacion($datos);
    if (!$validado["ok"]) {
        throw new InvalidArgumentException($validado["error"]);
    }

    $solicitud = fetch_solicitud_personalizacion_por_id($id);
    if ($solicitud === null || $solicitud["estado"] !== "pendiente") {
        throw new RuntimeException("La solicitud no existe o ya fue respondida.");
    }

    if (!update_solicitud_personalizacion_respuesta(
        $id,
        $validado["estado"],
        $validado["presupuesto"],
        $validado["respuesta"],
    )) {
        throw new RuntimeException("La solicitud dejó de estar pendiente y no se pudo actualizar.");
    }

    $respuestaGuardada = [
        ...$solicitud,
        "estado" => $validado["estado"],
        "presupuesto" => $validado["presupuesto"],
        "respuesta" => $validado["respuesta"],
    ];
    enviar_notificacion_solicitud_personalizacion($respuestaGuardada);
    mark_solicitud_personalizacion_notificada($id);
}

function reenviar_notificacion_solicitud_personalizacion_admin(int $id): void
{
    $solicitud = fetch_solicitud_personalizacion_por_id($id);
    if (
        $solicitud === null
        || !in_array($solicitud["estado"], ["aceptada", "rechazada"], true)
        || $solicitud["notificacion_enviada_en"] !== null
    ) {
        throw new RuntimeException("No hay una notificación pendiente para esta solicitud.");
    }

    enviar_notificacion_solicitud_personalizacion($solicitud);
    mark_solicitud_personalizacion_notificada($id);
}

function enviar_notificacion_solicitud_personalizacion(array $solicitud): void
{
    $apiKey = trim((string) ($_ENV["RESEND_API_KEY"] ?? ""));
    $sender = trim((string) ($_ENV["MAIL_FROM"] ?? ""));
    if ($apiKey === "" || $sender === "") {
        throw new RuntimeException("La respuesta se guardó, pero falta configurar RESEND_API_KEY y MAIL_FROM para notificar al cliente.");
    }

    $esAceptada = $solicitud["estado"] === "aceptada";
    $asunto = $esAceptada
        ? "Presupuesto para tu producto personalizado"
        : "Respuesta a tu solicitud de producto personalizado";
    $monto = $esAceptada
        ? "\nPresupuesto estimado: $" . number_format((float) $solicitud["presupuesto"], 2, ",", ".")
        : "";
    $mensaje = ($esAceptada
        ? "Aceptamos tu solicitud de producto personalizado."
        : "No podremos avanzar con tu solicitud de producto personalizado.")
        . $monto
        . "\n\n"
        . $solicitud["respuesta"]
        . "\n\nPodés ver el detalle en tu cuenta de Maderas Artesanales.";

    try {
        $resend = \Resend\Resend::client($apiKey);
        $resend->emails->send([
            "from" => $sender,
            "to" => [$solicitud["cliente_email"]],
            "subject" => $asunto,
            "text" => $mensaje,
            "html" => nl2br(htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8")),
        ]);
    } catch (Throwable $exception) {
        throw new RuntimeException(
            "La respuesta se guardó, pero no se pudo enviar la notificación por correo.",
            0,
            $exception,
        );
    }
}
