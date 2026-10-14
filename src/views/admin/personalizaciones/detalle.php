<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Solicitud #<?= (int) $solicitud["id"] ?></h1>
    <a class="btn btn-outline-secondary" href="/admin/personalizaciones">Volver a solicitudes</a>
  </div>
  <?php if (!empty($mensaje)): ?><div class="alert alert-success" role="alert"><?= html($mensaje) ?></div><?php endif; ?>
  <?php if (!empty($error)): ?><div class="alert alert-danger" role="alert"><?= html($error) ?></div><?php endif; ?>
  <div class="card mb-4">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Cliente</dt><dd class="col-sm-9"><?= html($solicitud["cliente_nombre"]) ?> (<?= html($solicitud["cliente_email"]) ?>)</dd>
        <dt class="col-sm-3">Fecha</dt><dd class="col-sm-9"><?= html((string) $solicitud["created_at"]) ?></dd>
        <dt class="col-sm-3">Estado</dt><dd class="col-sm-9"><?= html(ucfirst($solicitud["estado"])) ?></dd>
        <dt class="col-sm-3">Descripción</dt><dd class="col-sm-9"><?= nl2br(html($solicitud["descripcion"])) ?></dd>
        <dt class="col-sm-3">Archivo</dt><dd class="col-sm-9"><a href="/admin/personalizaciones/<?= (int) $solicitud["id"] ?>/archivo"><?= html($solicitud["archivo_nombre"]) ?> (descargar)</a></dd>
      </dl>
    </div>
  </div>

  <?php if ($solicitud["estado"] === "pendiente"): ?>
    <section class="card">
      <div class="card-body">
        <h2 class="h4">Responder solicitud</h2>
        <form method="post" action="/admin/personalizaciones/<?= (int) $solicitud["id"] ?>/respuesta">
          <div class="mb-3">
            <label for="estado" class="form-label">Decisión</label>
            <select class="form-select" id="estado" name="estado" required>
              <option value="">Seleccionar</option>
              <option value="aceptada">Aceptar</option>
              <option value="rechazada">Rechazar</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="presupuesto" class="form-label">Presupuesto estimado (requerido para aceptar)</label>
            <input class="form-control" type="number" id="presupuesto" name="presupuesto" min="0.01" step="0.01">
          </div>
          <div class="mb-3">
            <label for="respuesta" class="form-label">Mensaje para el cliente</label>
            <textarea class="form-control" id="respuesta" name="respuesta" rows="4" required></textarea>
          </div>
          <button class="btn btn-primary" type="submit">Guardar y notificar al cliente</button>
        </form>
      </div>
    </section>
  <?php else: ?>
    <section class="card">
      <div class="card-body">
        <?php if ($solicitud["estado"] === "aceptada"): ?><p class="fw-semibold">Presupuesto estimado: $<?= number_format((float) $solicitud["presupuesto"], 2, ",", ".") ?></p><?php endif; ?>
        <h2 class="h5">Respuesta enviada</h2>
        <p><?= nl2br(html((string) $solicitud["respuesta"])) ?></p>
        <p class="small text-muted">Respondida <?= html((string) $solicitud["respondida_en"]) ?></p>
        <?php if ($solicitud["notificacion_enviada_en"] === null): ?>
          <p class="text-warning">La notificación todavía no se pudo confirmar como enviada.</p>
          <form method="post" action="/admin/personalizaciones/<?= (int) $solicitud["id"] ?>/notificar">
            <button class="btn btn-outline-primary" type="submit">Reintentar notificación por correo</button>
          </form>
        <?php else: ?>
          <p class="small text-success mb-0">Notificación enviada <?= html((string) $solicitud["notificacion_enviada_en"]) ?></p>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>
</div>
