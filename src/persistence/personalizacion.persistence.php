<?php

function create_solicitud_personalizacion(array $data): int
{
    $stmt = getDB()->prepare(
        "INSERT INTO solicitudes_personalizacion
         (usuario_id, descripcion, archivo_nombre, archivo_guardado, archivo_mime)
         VALUES (:usuario_id, :descripcion, :archivo_nombre, :archivo_guardado, :archivo_mime)",
    );
    $stmt->execute([
        "usuario_id" => $data["usuario_id"],
        "descripcion" => $data["descripcion"],
        "archivo_nombre" => $data["archivo_nombre"],
        "archivo_guardado" => $data["archivo_guardado"],
        "archivo_mime" => $data["archivo_mime"],
    ]);

    return (int) getDB()->lastInsertId();
}

function fetch_solicitudes_personalizacion_usuario(int $usuarioId): array
{
    $stmt = getDB()->prepare(
        "SELECT id, descripcion, archivo_nombre, archivo_mime, estado, presupuesto, respuesta,
                respondida_en, notificacion_enviada_en, created_at
         FROM solicitudes_personalizacion
         WHERE usuario_id = :usuario_id
         ORDER BY created_at DESC, id DESC",
    );
    $stmt->execute(["usuario_id" => $usuarioId]);

    return $stmt->fetchAll();
}

function fetch_solicitudes_personalizacion_admin(): array
{
    return getDB()->query(
        "SELECT s.id, s.descripcion, s.archivo_nombre, s.archivo_mime, s.estado,
                s.presupuesto, s.respuesta, s.respondida_en, s.notificacion_enviada_en, s.created_at,
                u.nombre AS cliente_nombre, u.email AS cliente_email
         FROM solicitudes_personalizacion s
         JOIN usuarios u ON u.id = s.usuario_id
         ORDER BY (s.estado = 'pendiente') DESC, s.created_at DESC, s.id DESC",
    )->fetchAll();
}

function fetch_solicitud_personalizacion_por_id(int $id): ?array
{
    $stmt = getDB()->prepare(
        "SELECT s.*, u.nombre AS cliente_nombre, u.email AS cliente_email
         FROM solicitudes_personalizacion s
         JOIN usuarios u ON u.id = s.usuario_id
         WHERE s.id = :id
         LIMIT 1",
    );
    $stmt->execute(["id" => $id]);
    $solicitud = $stmt->fetch();

    return $solicitud === false ? null : $solicitud;
}

function fetch_solicitud_personalizacion_de_usuario(int $id, int $usuarioId): ?array
{
    $stmt = getDB()->prepare(
        "SELECT id, archivo_guardado, archivo_mime
         FROM solicitudes_personalizacion
         WHERE id = :id AND usuario_id = :usuario_id
         LIMIT 1",
    );
    $stmt->execute([
        "id" => $id,
        "usuario_id" => $usuarioId,
    ]);
    $solicitud = $stmt->fetch();

    return $solicitud === false ? null : $solicitud;
}

function update_solicitud_personalizacion_respuesta(
    int $id,
    string $estado,
    ?float $presupuesto,
    string $respuesta,
): bool {
    $stmt = getDB()->prepare(
        "UPDATE solicitudes_personalizacion
         SET estado = :estado,
             presupuesto = :presupuesto,
             respuesta = :respuesta,
             respondida_en = NOW()
         WHERE id = :id AND estado = 'pendiente'",
    );
    $stmt->execute([
        "id" => $id,
        "estado" => $estado,
        "presupuesto" => $presupuesto,
        "respuesta" => $respuesta,
    ]);

    return $stmt->rowCount() === 1;
}

function mark_solicitud_personalizacion_notificada(int $id): void
{
    $stmt = getDB()->prepare(
        "UPDATE solicitudes_personalizacion
         SET notificacion_enviada_en = NOW()
         WHERE id = :id AND estado IN ('aceptada', 'rechazada')",
    );
    $stmt->execute(["id" => $id]);
}
